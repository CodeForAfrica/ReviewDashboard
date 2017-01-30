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
use Misd\Linkify\Linkify;

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
            $brief_field = 0;
            foreach ($form->responses_headers as $index => $header){
                if (trim($header) == 'STORY CONCEPT (short: two sentences max)'){
                    $brief_field = $index;
                }
            }
        }

        $linkify = new Linkify(array('attr' => array('target' => '_blank', 'rel' => 'noreferrer noopener')));


        $data = compact('form', 'responses', 'role', 'brief_field', 'linkify');

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

        // For checks
        $responses_url_old = $form->responses_url;
        $form->responses_url = $request->input('responses_url');

        $form->save();

        $refresh_form = $request->input('refresh_form');
        if ($responses_url_old != $request->input('responses_url')) {
            $refresh_form = 1;
        }

        if ($refresh_form == 1) {
            $form->import_status = 1;
            $form->save();
            $this->dispatch(new ImportResponses($form));
        }

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
        $linkify = new Linkify(array('attr' => array('target' => '_blank', 'rel' => 'noreferrer noopener')));

        return view('forms.ratings_config', compact('form', 'linkify'));
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

            $reviews = Review::where('user_id', $user->id)->where('form_id', $form->id)->get();
            $reviews_done = 0;
            foreach ($reviews as $review){
                $do_not_count = false;
                if (count((array) $review->feedback) == 0) { $do_not_count = true; };
                foreach ((array)$review->feedback as $feedback_index => $feedback){
                    if ($form->ratings_config[$feedback_index]['required'] == 'yes' && trim($feedback) == ''){ $do_not_count = true; }
                }
                if (!$do_not_count) { $reviews_done++ ; };
            }

            $users[$index]->reviews_done = $reviews_done;
            $users[$index]->responses_total = $form->responses()->count();

            switch (DB::table('form_user')->where('user_id', $user->id)->where('form_id', $form->id)->first()->role_id) {
                case 1:
                    $users[$index]->role = 'Administrator';
                    break;
                case 2:
                    $users[$index]->role = 'Reviewer';
                    break;
                case 3:
                    $users[$index]->role = 'Viewer';
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
