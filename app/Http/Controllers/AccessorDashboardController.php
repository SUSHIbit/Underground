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
        
        // Get recently reviewed sets - include both approved_unpublished and approved statuses
        $reviewedSets = Set::whereIn('status', ['approved', 'approved_unpublished', 'rejected'])
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
        // Allow review of sets that are pending approval OR already reviewed (for viewing)
        if (!$set->isPendingApproval() && !$set->isApproved() && !$set->isApprovedUnpublished() && !$set->isRejected()) {
            return redirect()->route('accessor.dashboard')
                           ->with('error', 'This set cannot be reviewed.');
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

    /**
     * Display tournaments dashboard for accessor.
     */
    public function tournaments()
    {
        // Get tournaments pending approval
        $pendingTournaments = Tournament::where('status', 'pending_approval')
                    ->with(['creator', 'judges'])
                    ->latest('submitted_at')
                    ->get();
        
        // Get recently reviewed tournaments - include both approved_unpublished and approved statuses
        $reviewedTournaments = Tournament::whereIn('status', ['approved', 'approved_unpublished', 'rejected'])
                    ->with(['creator', 'reviewer', 'judges'])
                    ->latest('reviewed_at')
                    ->limit(10)
                    ->get();
        
        return view('accessor.tournaments', compact('pendingTournaments', 'reviewedTournaments'));
    }

    /**
     * Show the tournament review page.
     */
    public function reviewTournament(Tournament $tournament)
    {
        // Allow review of tournaments that are pending approval OR already reviewed (for viewing)
        if (!$tournament->isPendingApproval() && !$tournament->isApproved() && !$tournament->isApprovedUnpublished() && !$tournament->isRejected()) {
            return redirect()->route('accessor.tournaments')
                        ->with('error', 'This tournament cannot be reviewed.');
        }
        
        // Load the tournament with its relationships including rubrics
        $tournament->load(['creator', 'judges', 'comments.user', 'rubrics']);
        
        return view('accessor.review-tournament', compact('tournament'));
    }
}