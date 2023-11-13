<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function __construct()
    {

    }

    public function register(Request $req)
    {
        echo "<pre>";
        print_r($req->all());
        die;

    }

}
