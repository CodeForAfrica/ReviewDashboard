<?php

namespace App\Http\Controllers;

use App\Response;
use App\Review;
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
    public function show(Request $request, $id)
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

        $review = $response->reviews()->where('user_id', $request->user()->id )->first();

        if ( !$review ) {
            $review = new Review;
            $review->response_id = $response->id;
            $review->user_id     = $request->user()->id;
            $review->save();
        }

        $data = array(
            'response' => $response,
            'form'     => $response->form,
            'review'   => $review,
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
        $review = Review::firstOrNew(['response_id' => $id, 'user_id' => $request->user()->id]);
        $review->feedback  = $request->input('reviews');
        $review->save();
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
    }
}
