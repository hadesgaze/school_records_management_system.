<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChairController extends Controller
{
    public function index()
    {
        return view('chairperson.dashboard'); // resources/views/dashboards/chair.blade.php
    }
}
