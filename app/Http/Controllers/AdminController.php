<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard'); // resources/views/dashboards/admin.blade.php
    }

    public function profile()
    {
        return view('dashboards.profile'); // optional profile page
    }

    public function showFile($dir = null, $file = null)
    {
        // your logic for showing files
    }

    public function downloadZip($id, $dir = null)
    {
        // your logic for downloading zip
    }

    public function downloadPdf(Request $request)
    {
        // your logic for downloading pdf
    }
}
