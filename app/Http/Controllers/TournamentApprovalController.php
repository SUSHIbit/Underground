<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\SetComment;
use Illuminate\Http\Request;

class TournamentApprovalController extends Controller
{
    public function addComment(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'comment' => 'required|string'
        ]);
        
        SetComment::create([
            'commentable_type' => Tournament::class,
            'commentable_id' => $tournament->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment']
        ]);
        
        return back()->with('success', 'Comment added successfully.');
    }
    
    public function approve(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string'
        ]);
        
        // Changed to set status to 'approved_unpublished' instead of 'approved'
        // Using the model's method which we updated
        $tournament->approve(auth()->user(), $validated['review_notes'] ?? null);
        
        return redirect()->route('accessor.tournaments')
                        ->with('success', 'Tournament approved successfully. It is now ready for the lecturer to publish.');
    }
    
    public function reject(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string'
        ]);
        
        $tournament->reject(auth()->user(), $validated['review_notes']);
        
        return redirect()->route('accessor.tournaments')
                        ->with('success', 'Tournament rejected successfully.');
    }
}