<?php

namespace App\Http\Controllers;

use App\Response;
use Illuminate\Http\Request;

use App\Http\Requests;

class ResponseController extends Controller
{
    /**
     * Instantiate a new ResponseController instance.
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = Response::findOrFail($id);

        $prev_id = $response->form->responses()->where('id', '<', $response->id)->max('id');
        $next_id = $response->form->responses()->where('id', '>', $response->id)->min('id');

        if( is_null($prev_id) ){
            $prev_id = $response->form->responses()->where('id', '>', $response->id)->max('id');
        }
        if( is_null($next_id) ){
            $next_id = $response->form->responses()->where('id', '<', $response->id)->min('id');
        }

        $data = array(
            'response' => $response,
            'form'     => $response->form,
            'prev_id'  => $prev_id,
            'next_id'  => $next_id
        );
        return view('response', $data);
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
        //
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
    }
}
