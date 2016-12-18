<?php

namespace App\Http\Controllers;

use App\Form;
use App\Jobs\ImportResponses;
use App\Response;
use App\Review;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class FormController extends Controller
{
    /**
     * Create a new FormController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return redirect('/home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array(
            'page' => array(
                'title' => 'Create Form',
                'cancel_link' => '/home'
            )
        );
        return view('forms.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO: Validator

        $user = $request->user();

        $form = new Form;
        $form->title = $request->input('title');
        $form->description = $request->input('description');
        $form->responses_url = $request->input('responses_url');

        $form->save();

        $user->forms()->attach(
            $form,
            [
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
        $user->save();

        $this->dispatch(new ImportResponses($form));

        return redirect('/form/'.$form->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $form = Form::findOrFail($id);

        $responses = $form->responses()->paginate(10);

        $role = 'Nothing';
        
        switch (DB::table('form_user')->where('user_id', $request->user()->id)->where('form_id', $form->id)->first()->role_id) {
            case 1:
                $role = 'Administrator';
                break;
            case 2:
                $role = 'Reviewer';
                break;
        }

        if ($form->import_status == 2) {
            $brief_field = 1;
            foreach ($form->responses_headers as $index => $header){
                if (trim($header) == 'STORY CONCEPT (short: two sentences max)'){
                    $brief_field = $index;
                }
            }
        }


        $data = compact('form', 'responses', 'role', 'brief_field');

        return view('forms.view',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = array(
            'page' => array(
                'title' => 'Create Form',
                'cancel_link' => '/form/'.$id
            ),
            'form' => Form::findOrFail($id)
        );
        return view('forms.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO: Validator

        $form = Form::findOrFail($id);
        $form->title = $request->input('title');
        $form->description = $request->input('description');

        // TODO: Check if responses url has changed, new import if so.
        $form->responses_url = $request->input('responses_url');

        $form->save();

        // TODO: If url changed do:
        $form->import_status = 1;
        $form->save();
        $this->dispatch(new ImportResponses($form));

        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Form::findOrFail($id)->delete();
        return 1;
    }


    /**
     * Show review config page
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showReviewConfig($id)
    {
        $form = Form::findOrFail($id);

        return view('forms.ratings_config', compact('form'));
    }

    /**
     * Update the review config
     *
     * @param Request $request
     * @param $id
     * @return int
     */
    public function updateReviewConfig(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $form->ratings_config = $request->input('configs');
        $form->save();

        return 1;
    }


    /**
     * Show users who have access to this form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUsers(Request $request, $id)
    {
        //
        $form = Form::findOrFail($id);

        $users = array();

        foreach ($form->users as $index => $user) {
            $users[$index] = new \stdClass();

            $users[$index]->id = $user->id;
            $users[$index]->name = $user->name;
            $users[$index]->email = $user->email;

            $users[$index]->reviews_done = Review::where('user_id', $user->id)->where('form_id', $form->id)->count();

            switch (DB::table('form_user')->where('user_id', $user->id)->where('form_id', $form->id)->first()->role_id) {
                case 1:
                    $users[$index]->role = 'Administrator';
                    break;
                case 2:
                    $users[$index]->role = 'Reviewer';
                    break;
                default:
                    $users[$index]->role = 'Nothing';
                    break;
            }
        }

        $data = array(
            'page' => array(
                'title' => 'Users',
                'cancel_link' => '/form/'.$id
            ),
            'form'  => $form,
            'users' => $users
        );
        return view('forms.share', $data);
    }

    public function updateUsers(Request $request, $id)
    {
        // TODO: Allow bulk adding of users.

        // TODO: Validate input.

        $user = User::firstOrNew(['email' => $request->input('email')]);
        if (!$user->name) {
            $user->name = $request->input('name');
            $user->password = str_random(40);
            $user->save();

            // TODO: Send welcome e-mail.
        }

        $form = Form::find($id);

        $user->forms()->attach(
            $form,
            [
                'role_id' => $request->input('role_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
        $user->save();

        return redirect('/form/'.$id.'/share');
    }


    public function deleteUsers(Request $request, $id)
    {
        $form = Form::find($id);
        $form->users()->detach($request->input('users'));
        return 1;
    }
    
    
}
