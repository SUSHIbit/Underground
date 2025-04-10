<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ExtrasController extends Controller
{
    /**
     * Display the user's extras page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('extras.index', compact('user'));
    }
}