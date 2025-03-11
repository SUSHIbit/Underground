<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RankController extends Controller
{
    /**
     * Display a listing of all ranks.
     */
    public function index()
    {
        // Get current user's information to highlight their rank
        $user = auth()->user();
        
        // Define all available ranks and their requirements
        $ranks = [
            [
                'name' => 'One Above All',
                'min_points' => 1000,
                'description' => 'The highest rank achievable. Reserved for the elite.',
                'color' => 'indigo-600',
                'bg_color' => 'indigo-100',
            ],
            [
                'name' => 'Grand Master',
                'min_points' => 750,
                'max_points' => 999,
                'description' => 'A prestigious rank showing exceptional knowledge and skill.',
                'color' => 'red-600',
                'bg_color' => 'red-100',
            ],
            [
                'name' => 'Master',
                'min_points' => 500,
                'max_points' => 749,
                'description' => 'Advanced mastery of concepts and challenges.',
                'color' => 'purple-600',
                'bg_color' => 'purple-100',
            ],
            [
                'name' => 'Gold',
                'min_points' => 250,
                'max_points' => 499,
                'description' => 'Significant achievements and knowledge demonstrated.',
                'color' => 'yellow-500',
                'bg_color' => 'yellow-100',
            ],
            [
                'name' => 'Silver',
                'min_points' => 100,
                'max_points' => 249,
                'description' => 'Growing expertise and consistent performance.',
                'color' => 'gray-400',
                'bg_color' => 'gray-100',
            ],
            [
                'name' => 'Bronze',
                'min_points' => 50,
                'max_points' => 99,
                'description' => 'Beginning to show proficiency in various topics.',
                'color' => 'amber-600',
                'bg_color' => 'amber-100',
            ],
            [
                'name' => 'Unranked',
                'min_points' => 0,
                'max_points' => 49,
                'description' => 'Just starting out on your learning journey.',
                'color' => 'gray-600',
                'bg_color' => 'gray-100',
            ],
        ];
        
        // Sort ranks from highest to lowest
        // Already sorted in the array but explicitly sort to maintain future flexibility
        $ranks = collect($ranks)->sortByDesc('min_points')->values()->all();
        
        return view('ranks.index', compact('ranks', 'user'));
    }
}