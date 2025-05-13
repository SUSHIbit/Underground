<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\SetComment;
use App\Models\Question;
use App\Models\Tournament; 
use App\Models\TournamentJudge; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        $approvedUnpublishedSets = $sets->filter(function ($set) {
            return $set->isApprovedUnpublished();
        });
        
        $approvedSets = $sets->filter(function ($set) {
            return $set->isPublished();
        });
        
        $rejectedSets = $sets->filter(function ($set) {
            return $set->isRejected();
        });
        
        return view('lecturer.dashboard', compact(
            'sets',
            'draftSets', 
            'pendingSets',
            'approvedUnpublishedSets',
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
        
        try {
            // Enable query logging
            DB::enableQueryLog();
            
            $validated = $request->validate([
                'questions' => 'required|array',
                'questions.*.id' => 'required|exists:questions,id',
                'questions.*.question_text' => 'required|string',
                'questions.*.options' => 'required|array',
                'questions.*.correct_answer' => 'required|string|size:1',
                'questions.*.reason' => 'required|string',
                'enable_timer' => 'sometimes|nullable|boolean',
                'timer_minutes' => 'nullable|integer|min:1|max:180',
            ]);
            
            Log::info('Starting question updates with data', ['count' => count($validated['questions'])]);
            
            DB::beginTransaction();
            
            // Update each question
            foreach ($validated['questions'] as $questionData) {
                $questionId = $questionData['id'];
                
                // Find question directly using query builder for more reliable results
                $question = Question::where('id', $questionId)
                                   ->where('set_id', $set->id)
                                   ->first();
                
                if ($question) {
                    Log::info('Found question to update', [
                        'id' => $question->id,
                        'old_text' => $question->question_text,
                        'new_text' => $questionData['question_text']
                    ]);
                    
                    $result = $question->update([
                        'question_text' => $questionData['question_text'],
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correct_answer'],
                        'reason' => $questionData['reason'],
                    ]);
                    
                    Log::info('Question update result', ['success' => $result]);
                    
                    // Mark any comments on this question as resolved
                    SetComment::where('question_id', $question->id)
                              ->update(['is_resolved' => true]);
                } else {
                    Log::warning('Question not found', ['id' => $questionId, 'set_id' => $set->id]);
                }
            }
            
            // Update timer settings
            if ($set->type === 'quiz' && $set->quizDetail) {
                $timerMinutes = null;
                if ($request->has('enable_timer')) {
                    $timerMinutes = $request->input('timer_minutes');
                }
                
                $set->quizDetail->update([
                    'timer_minutes' => $timerMinutes,
                ]);
            }
            
            DB::commit();
            
            // Log the executed queries for debugging
            Log::info('Executed queries:', ['queries' => DB::getQueryLog()]);
            
            return redirect()->route('lecturer.dashboard')
                            ->with('success', 'Set updated successfully.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update questions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Failed to update questions: ' . $e->getMessage())
                           ->withInput();
        }
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
        
        // Make sure we also get tournaments that are ready to be published
        $approvedUnpublishedTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isApprovedUnpublished();
        });
        
        // Get the published tournaments
        $approvedTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isApproved();
        });
        
        $rejectedTournaments = $tournaments->filter(function ($tournament) {
            return $tournament->isRejected();
        });
        
        return view('lecturer.tournaments', compact(
            'draftTournaments', 
            'pendingTournaments',
            'approvedUnpublishedTournaments',
            'approvedTournaments', 
            'rejectedTournaments'
        ));
    }

    // Update the editTournament method to load rubrics too
    public function editTournament(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Include rubrics in loading, but load judges directly
        $tournament->load(['judges', 'comments.user', 'rubrics']);
        
        return view('lecturer.edit-tournament', compact('tournament'));
    }

    public function updateTournament(Request $request, Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        try {
            // Enable query logging for debugging
            DB::enableQueryLog();
            
            // Filter out empty judge entries before validation
            $judges = array_filter($request->input('judges', []), function($value) {
                return !empty($value);
            });
            
            // Rebuild judges array to be sequential
            $judges = array_values($judges);
            
            // Create a filtered version of judge_roles that matches the filtered judges
            $judgeRoles = [];
            if (is_array($request->input('judge_roles'))) {
                foreach ($judges as $key => $judgeId) {
                    $originalKey = array_search($judgeId, $request->input('judges', []));
                    if ($originalKey !== false && isset($request->input('judge_roles')[$originalKey])) {
                        $judgeRoles[$key] = $request->input('judge_roles')[$originalKey];
                    } else {
                        $judgeRoles[$key] = null;
                    }
                }
            }
            
            // Replace judges and judge_roles in the request data
            $requestData = $request->all();
            $requestData['judges'] = $judges;
            $requestData['judge_roles'] = $judgeRoles;
            
            // Now validate the modified request data
            $validated = validator($requestData, [
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
                'judges' => 'required|array|min:1',
                'judges.*' => 'required|exists:users,id',
                'judge_roles' => 'nullable|array',
                'judge_roles.*' => 'nullable|string|max:255',
                'rubrics' => 'required|array|min:1',
                'rubrics.*.title' => 'required|string|max:255',
                'rubrics.*.score_weight' => 'required|integer|min:1|max:100',
            ])->validate();
            
            // Validate total rubric weight = 100
            $totalWeight = 0;
            foreach ($validated['rubrics'] as $rubric) {
                $totalWeight += intval($rubric['score_weight']);
            }
            
            if ($totalWeight !== 100) {
                return redirect()->back()->withErrors(['rubrics' => 'Total rubric weight must equal 100'])->withInput();
            }
            
            DB::beginTransaction();
            
            // Update tournament basic details first
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
            
            // Clear all existing judges and add the new ones from the form
            $judgeData = [];
            foreach ($validated['judges'] as $index => $judgeId) {
                // Only include role if it exists and is not empty
                $role = isset($validated['judge_roles'][$index]) && !empty($validated['judge_roles'][$index]) 
                    ? $validated['judge_roles'][$index] 
                    : null;
                    
                $judgeData[$judgeId] = ['role' => $role];
            }
            
            // Sync judges
            $tournament->judges()->sync($judgeData);
            Log::info('Judges synced successfully', ['judgeData' => $judgeData]);
            
            // For rubrics, first delete all existing ones
            $tournament->rubrics()->delete();
            
            // Then create new ones
            foreach ($validated['rubrics'] as $rubricData) {
                $tournament->rubrics()->create([
                    'title' => $rubricData['title'],
                    'score_weight' => $rubricData['score_weight'],
                ]);
            }
            
            DB::commit();
            
            // Log the executed queries for debugging
            Log::info('Tournament update executed queries:', ['queries' => DB::getQueryLog()]);
            
            return redirect()->route('lecturer.tournaments')
                    ->with('success', 'Tournament updated successfully.');
                    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update tournament', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                    ->with('error', 'Failed to update tournament: ' . $e->getMessage())
                    ->withInput();
        }
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

    public function tournamentSubmissions(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Load the tournament with its participants and their users
        $tournament->load(['participants.user']);
        
        // Get all participants
        $participants = $tournament->participants;
        
        return view('lecturer.tournament-submissions', compact('tournament', 'participants'));
    }

    /**
     * Publish the set that has been approved by accessor.
     */
    public function publishSet(Set $set)
    {
        // Ensure the lecturer owns this set
        if ($set->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Ensure the set is in the approved_unpublished state
        if (!$set->isApprovedUnpublished()) {
            return redirect()->route('lecturer.dashboard')
                            ->with('error', 'Only sets that have been approved by an accessor can be published.');
        }
        
        // Publish the set
        $set->publish();
        
        return redirect()->route('lecturer.dashboard')
                        ->with('success', 'Set published successfully. It is now available to students.');
    }

    public function publishTournament(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Ensure the tournament is in the approved_unpublished state
        if (!$tournament->isApprovedUnpublished()) {
            return redirect()->route('lecturer.tournaments')
                            ->with('error', 'Only tournaments that have been approved by an accessor can be published.');
        }
        
        // Publish the tournament
        $tournament->publish();
        
        return redirect()->route('lecturer.tournaments')
                        ->with('success', 'Tournament published successfully. It is now available to students.');
    }
}