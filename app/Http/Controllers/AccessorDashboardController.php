<?php

namespace App\Http\Controllers;

use App\Models\Set;
use Illuminate\Http\Request;

class AccessorDashboardController extends Controller
{
        /**
     * Display the accessor's dashboard.
     */
    public function index()
    {
        // Get sets pending approval
        $pendingSets = Set::where('status', 'pending_approval')
                      ->with(['creator', 'quizDetail.subject', 'quizDetail.topic', 'challengeDetail'])
                      ->latest('submitted_at')
                      ->get();
        
        // Get recently reviewed sets
        $reviewedSets = Set::whereIn('status', ['approved', 'rejected'])
                      ->with(['creator', 'reviewer', 'quizDetail.subject', 'quizDetail.topic', 'challengeDetail'])
                      ->latest('reviewed_at')
                      ->limit(10)
                      ->get();
        
        return view('accessor.dashboard', compact('pendingSets', 'reviewedSets'));
    }
    
    /**
     * Show the set review page.
     */
    public function review(Set $set)
    {
        // Ensure the set is pending approval
        if (!$set->isPendingApproval()) {
            return redirect()->route('accessor.dashboard')
                           ->with('error', 'This set is not pending approval.');
        }
        
        $set->load(['creator', 'questions', 'comments.user', 'comments.question']);
        
        if ($set->type === 'quiz') {
            $set->load('quizDetail.subject', 'quizDetail.topic');
            return view('accessor.review-quiz', compact('set'));
        } else {
            $set->load('challengeDetail', 'challengeDetail.prerequisites');
            return view('accessor.review-challenge', compact('set'));
        }
    }
}
