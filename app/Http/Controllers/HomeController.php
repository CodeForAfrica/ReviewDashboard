<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Misd\Linkify\Linkify;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $linkify = new Linkify(array('attr' => array('target' => '_blank', 'rel' => 'noreferrer noopener')));
        $data = array(
            'forms' => $request->user()->forms()->orderBy('updated_at', 'desc')->get(),
            'linkify' => $linkify
        );
        return view('home', $data);
    }
}
