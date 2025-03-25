<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttempt;

class UEPointsController extends Controller
{
    /**
     * Display the UEPoints information page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get recent UEPoints spending history
        $spendingHistory = QuizAttempt::where('user_id', $user->id)
                         ->where('is_retake', true)
                         ->where('ue_points_spent', '>', 0)
                         ->with(['set'])
                         ->orderBy('created_at', 'desc')
                         ->limit(10)
                         ->get();
        
        return view('uepoints.index', compact('user', 'spendingHistory'));
    }
}