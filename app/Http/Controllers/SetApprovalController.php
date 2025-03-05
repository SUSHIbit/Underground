<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\SetComment;
use Illuminate\Http\Request;

class SetApprovalController extends Controller
{
    /**
     * Add a comment to a set or question.
     */
    public function addComment(Request $request, Set $set)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'question_id' => 'nullable|exists:questions,id'
        ]);
        
        SetComment::create([
            'set_id' => $set->id,
            'question_id' => $validated['question_id'] ?? null,
            'user_id' => auth()->id(),
            'comment' => $validated['comment']
        ]);
        
        return back()->with('success', 'Comment added successfully.');
    }
    
    /**
     * Approve a set.
     */
    public function approve(Request $request, Set $set)
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string'
        ]);
        
        $set->approve(auth()->user(), $validated['review_notes'] ?? null);
        
        return redirect()->route('accessor.dashboard')
                        ->with('success', 'Set approved successfully.');
    }
    
    /**
     * Reject a set.
     */
    public function reject(Request $request, Set $set)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string'
        ]);
        
        $set->reject(auth()->user(), $validated['review_notes']);
        
        return redirect()->route('accessor.dashboard')
                        ->with('success', 'Set rejected successfully.');
    }
}