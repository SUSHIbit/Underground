<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Tournament; // Add this import line
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


    public function tournaments()
    {
        // Get tournaments pending approval
        $pendingTournaments = Tournament::where('status', 'pending_approval')
                    ->with(['creator', 'judges'])
                    ->latest('submitted_at')
                    ->get();
        
        // Get recently reviewed tournaments
        $reviewedTournaments = Tournament::whereIn('status', ['approved', 'rejected'])
                    ->with(['creator', 'reviewer', 'judges'])
                    ->latest('reviewed_at')
                    ->limit(10)
                    ->get();
        
        return view('accessor.tournaments', compact('pendingTournaments', 'reviewedTournaments'));
    }

    public function reviewTournament(Tournament $tournament)
    {
        // Ensure the tournament is pending approval
        if (!$tournament->isPendingApproval()) {
            return redirect()->route('accessor.tournaments')
                        ->with('error', 'This tournament is not pending approval.');
        }
        
        // Load the tournament with its relationships including rubrics
        $tournament->load(['creator', 'judges', 'comments.user', 'rubrics']);
        
        return view('accessor.review-tournament', compact('tournament'));
    }
}