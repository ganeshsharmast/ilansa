<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function create(Request $req): Create
    {
        echo "<pre>";
        print_r($req->all());
        die;
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    /**
     * Show the profile for a given user.
     */
    public function update(string $id): Update
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    /**
     * Show the profile for a given user.
     */
    public function view(string $id): View
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    /**
     * Show the profile for a given user.
     */
    public function delete(string $id): Delete
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
    
}
