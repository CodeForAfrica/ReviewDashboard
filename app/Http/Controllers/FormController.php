<?php

namespace App\Http\Controllers;

use App\Form;
use App\Jobs\ImportResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
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
                'role_id' => 2,
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
    public function show($id)
    {
        //
        $data = array(
            'form' => Form::findOrFail($id)
        );
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
}
