<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $quizAttempts = $user->quizAttempts()
                            ->with('set')
                            ->where('completed', true)
                            ->latest()
                            ->limit(10)  // Limit to only 10 latest activities
                            ->get();
        
        return view('dashboard', compact('user', 'quizAttempts'));
    }
}