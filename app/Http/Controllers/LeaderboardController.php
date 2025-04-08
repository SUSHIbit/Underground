<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'student')
                    ->orderBy('points', 'desc')
                    ->take(100)
                    ->get();
        
        // Add rank attribute to each user
        foreach ($users as $user) {
            $user->rankTitle = $user->getRank();
        }
        
        return view('leaderboard.index', compact('users'));
    }
    
}
