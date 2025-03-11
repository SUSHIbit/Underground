<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\SetComment;
use App\Models\Tournament; // Add this import line
use App\Models\TournamentJudge; // Add this import line
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



    public function tournaments()
    {
        $user = auth()->user();
        
        // Get tournaments created by this lecturer
        $tournaments = Tournament::where('created_by', $user->id)
                    ->latest()
                    ->get();
        
        // Group tournaments by status
        $draftTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isDraft();
        });
        
        $pendingTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isPendingApproval();
        });
        
        $approvedTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isApproved();
        });
        
        $rejectedTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isRejected();
        });
        
        return view('lecturer.tournaments', compact(
            'draftTournaments', 
            'pendingTournaments', 
            'approvedTournaments', 
            'rejectedTournaments'
        ));
    }

    public function editTournament(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Try to load judges and comments, handle the case if comments can't be loaded
        try {
            $tournament->load(['judges', 'comments.user']);
        } catch (\Exception $e) {
            // If comments can't be loaded due to schema issues, just load judges
            $tournament->load(['judges']);
            
            // Create a temporary property to avoid errors in view
            $tournament->comments = collect();
        }
        
        return view('lecturer.edit-tournament', compact('tournament'));
    }

    public function updateTournament(Request $request, Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_time' => 'required|date',
            'location' => 'required|string|max:255',
            'eligibility' => 'required|string',
            'minimum_rank' => 'required|string',
            'team_size' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'rules' => 'required|string',
            'judging_criteria' => 'required|string',
            'project_submission' => 'required|string',
            'judges' => 'required|array',
            'judges.*.name' => 'required|string|max:255',
            'judges.*.role' => 'nullable|string|max:255',
        ]);
        
        $tournament->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date_time' => $validated['date_time'],
            'location' => $validated['location'],
            'eligibility' => $validated['eligibility'],
            'minimum_rank' => $validated['minimum_rank'],
            'team_size' => $validated['team_size'],
            'deadline' => $validated['deadline'],
            'rules' => $validated['rules'],
            'judging_criteria' => $validated['judging_criteria'],
            'project_submission' => $validated['project_submission'],
        ]);
        
        // Update judges (delete old ones and add new ones)
        $tournament->judges()->delete();
        
        foreach ($validated['judges'] as $judge) {
            TournamentJudge::create([
                'tournament_id' => $tournament->id,
                'name' => $judge['name'],
                'role' => $judge['role'] ?? null,
            ]);
        }
        
        return redirect()->route('lecturer.tournaments')
                    ->with('success', 'Tournament updated successfully.');
    }

    public function submitTournamentForApproval(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        $tournament->submitForApproval();
        
        return redirect()->route('lecturer.tournaments')
                    ->with('success', 'Tournament submitted for approval successfully.');
    }
}