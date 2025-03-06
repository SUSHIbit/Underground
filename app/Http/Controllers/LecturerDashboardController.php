<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\SetComment;
use Illuminate\Http\Request;

class LecturerDashboardController extends Controller
{
    /**
     * Display the lecturer's dashboard with their sets.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get sets created by this lecturer
        $sets = Set::where('created_by', $user->id)
                 ->with(['quizDetail.subject', 'quizDetail.topic', 'challengeDetail'])
                 ->latest()
                 ->get();
        
        // Group sets by status
        $draftSets = $sets->filter(function ($set) {
            return $set->isDraft();
        });
        
        $pendingSets = $sets->filter(function ($set) {
            return $set->isPendingApproval();
        });
        
        $approvedSets = $sets->filter(function ($set) {
            return $set->isApproved();
        });
        
        $rejectedSets = $sets->filter(function ($set) {
            return $set->isRejected();
        });
        
        return view('lecturer.dashboard', compact(
            'draftSets', 
            'pendingSets', 
            'approvedSets', 
            'rejectedSets'
        ));
    }
    
    /**
     * Show the form for editing the specified set.
     */
    public function edit(Set $set)
    {
        // Ensure the lecturer owns this set
        if ($set->created_by !== auth()->id()) {
            abort(403);
        }
        
        $set->load(['questions', 'comments.user', 'comments.question']);
        
        if ($set->type === 'quiz') {
            $set->load('quizDetail.subject', 'quizDetail.topic');
            return view('lecturer.edit-quiz', compact('set'));
        } else {
            $set->load('challengeDetail', 'challengeDetail.prerequisites');
            return view('lecturer.edit-challenge', compact('set'));
        }
    }
    

    
    /**
     * Update the specified set.
     */
    public function update(Request $request, Set $set)
    {
        // Ensure the lecturer owns this set
        if ($set->created_by !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array',
            'questions.*.correct_answer' => 'required|string|size:1',
            'questions.*.reason' => 'required|string',
        ]);
        
        \Log::info('Updating questions with data: ', $validated);
        
        // Update each question
        foreach ($validated['questions'] as $questionId => $questionData) {
            $question = $set->questions->firstWhere('id', $questionData['id']);
            
            if ($question) {
                \Log::info('Updating question ID: ' . $question->id);
                
                $question->update([
                    'question_text' => $questionData['question_text'],
                    'options' => $questionData['options'],
                    'correct_answer' => $questionData['correct_answer'],
                    'reason' => $questionData['reason'],
                ]);
                
                // Mark any comments on this question as resolved
                SetComment::where('question_id', $question->id)
                          ->update(['is_resolved' => true]);
            } else {
                \Log::warning('Question not found with ID: ' . $questionData['id']);
            }
        }
        
        return redirect()->route('lecturer.dashboard')
                        ->with('success', 'Set updated successfully.');
    }


    
    /**
     * Submit the set for approval.
     */
    public function submitForApproval(Set $set)
    {
        // Ensure the lecturer owns this set
        if ($set->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Update status and submitted_at directly
        $set->status = 'pending_approval';
        $set->submitted_at = now();
        $set->save();
        
        return redirect()->route('lecturer.dashboard')
                        ->with('success', 'Set submitted for approval successfully.');
    }
}
