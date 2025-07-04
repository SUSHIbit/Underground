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
            
            // FIXED: Update timer settings for both quizzes and challenges
            if ($set->type === 'quiz' && $set->quizDetail) {
                $timerMinutes = null;
                if ($request->has('enable_timer') && $request->input('enable_timer') == '1') {
                    $timerMinutes = $request->input('timer_minutes');
                }
                
                $set->quizDetail->update([
                    'timer_minutes' => $timerMinutes,
                ]);
            } elseif ($set->type === 'challenge' && $set->challengeDetail) {
                // FIXED: Added timer update logic for challenges
                $timerMinutes = null;
                if ($request->has('enable_timer') && $request->input('enable_timer') == '1') {
                    $timerMinutes = $request->input('timer_minutes');
                }
                
                $set->challengeDetail->update([
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
            // FIXED: Simple validation like quiz/challenge functions - NO DATE RESTRICTIONS
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date_time' => 'required|date',
                'location' => 'required|string|max:255',
                'eligibility' => 'required|string',
                'minimum_rank' => 'required|string',
                'team_size' => 'required|integer|min:1',
                'deadline' => 'required|date',
                'judging_date' => 'required|date', // NO after:deadline restriction
                'rules' => 'required|string',
                'judging_criteria' => 'required|string',
                'project_submission' => 'required|string',
                'judges' => 'required|array|min:1',
                'judges.*' => 'nullable|exists:users,id',
                'judge_roles' => 'nullable|array',
                'judge_roles.*' => 'nullable|string|max:255',
                'rubrics' => 'required|array|min:1',
                'rubrics.*.title' => 'required|string|max:255',
                'rubrics.*.score_weight' => 'required|integer|min:1|max:100',
            ]);
            
            // Validate total rubric weight = 100
            $totalWeight = 0;
            foreach ($validated['rubrics'] as $rubric) {
                $totalWeight += intval($rubric['score_weight']);
            }
            
            if ($totalWeight !== 100) {
                return redirect()->back()
                    ->withErrors(['rubrics' => 'Total rubric weight must equal 100'])
                    ->withInput();
            }
            
            // Filter out empty judges
            $judges = array_filter($validated['judges'], function($judgeId) {
                return !empty($judgeId);
            });
            
            if (empty($judges)) {
                return redirect()->back()
                    ->withErrors(['judges' => 'At least one judge must be selected'])
                    ->withInput();
            }
            
            DB::beginTransaction();
            
            // FIXED: Direct update like quiz/challenge functions - simple and clean
            $tournament->title = $validated['title'];
            $tournament->description = $validated['description'];
            $tournament->date_time = $validated['date_time'];
            $tournament->location = $validated['location'];
            $tournament->eligibility = $validated['eligibility'];
            $tournament->minimum_rank = $validated['minimum_rank'];
            $tournament->team_size = $validated['team_size'];
            $tournament->deadline = $validated['deadline'];
            $tournament->judging_date = $validated['judging_date']; // Can be any date now
            $tournament->rules = $validated['rules'];
            $tournament->judging_criteria = $validated['judging_criteria'];
            $tournament->project_submission = $validated['project_submission'];
            $tournament->save();
            
            // Handle judges - prepare data with roles
            $judgeData = [];
            $originalJudges = $validated['judges']; // Keep original array with indices
            
            foreach ($judges as $judgeId) {
                // Find the original index to get the correct role
                $originalIndex = array_search($judgeId, $originalJudges);
                $role = null;
                
                if ($originalIndex !== false && 
                    isset($validated['judge_roles'][$originalIndex]) && 
                    !empty($validated['judge_roles'][$originalIndex])) {
                    $role = $validated['judge_roles'][$originalIndex];
                }
                
                $judgeData[$judgeId] = ['role' => $role];
            }
            
            // Sync judges
            $tournament->judges()->sync($judgeData);
            
            // Update rubrics - delete existing and create new ones
            $tournament->rubrics()->delete();
            
            foreach ($validated['rubrics'] as $rubricData) {
                $tournament->rubrics()->create([
                    'title' => $rubricData['title'],
                    'score_weight' => $rubricData['score_weight'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('lecturer.tournaments')
                    ->with('success', 'Tournament updated successfully.');
                    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
                    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update tournament', [
                'error' => $e->getMessage(),
                'tournament_id' => $tournament->id
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

/**
     * Display tournament submissions with proper team loading.
     */
    public function tournamentSubmissions(Tournament $tournament)
    {
        // Ensure the lecturer owns this tournament
        if ($tournament->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Load the tournament with its participants and their users and teams
        $tournament->load([
            'participants.user',
            'participants.team.participants.user' // Load team with all team members
        ]);
        
        // Get all participants - for team tournaments, we want to show each team once
        if ($tournament->team_size > 1) {
            // For team tournaments, group by team and show one entry per team
            $participants = $tournament->participants()
                ->whereNotNull('team_id')
                ->with([
                    'user',
                    'team.participants.user' // Load all team members
                ])
                ->get()
                ->groupBy('team_id') // Group by team
                ->map(function($teamParticipants) {
                    // Return the team leader as the representative, or first member if no leader
                    return $teamParticipants->sortBy(function($participant) {
                        return $participant->role === 'leader' ? 0 : 1;
                    })->first();
                })
                ->values(); // Reset array keys
        } else {
            // For individual tournaments, just get all participants
            $participants = $tournament->participants()
                ->with('user')
                ->get();
        }
        
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