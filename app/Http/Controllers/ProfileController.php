<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getProfile()
    {
        return $this->showProfile();
    }

    public function showProfile(Request $request)
    {
        $data = array('user' => $request->user());
        return view('profile', $data);
    }

    /**
     * Update the user's profile.
     *
     * @param  Request  $request
     * @return Response
     */
    public function updateProfile(Request $request)
    {
        if ($request->user()) {
            // $request->user() returns an instance of the authenticated user...

            // TODO: Set up validator

            // TODO: Save inputs
        }
    }
}
