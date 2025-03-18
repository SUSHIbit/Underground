<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Subject;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        // Get all available subjects for the filter dropdown
        $subjects = Subject::orderBy('name')->get();
        
        // Get search and filter parameters
        $search = $request->input('search');
        $subjectId = $request->input('subject');
        
        // Start with base query for approved challenges
        $challengesQuery = Set::where('type', 'challenge')
                      ->where('status', 'approved')
                      ->with(['challengeDetail', 'challengeDetail.prerequisites']);
        
        // Apply search if provided
        if ($search) {
            $challengesQuery->whereHas('challengeDetail', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('challengeDetail.prerequisites.quizDetail.subject', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }
        
        // Apply subject filter if provided
        if ($subjectId) {
            $challengesQuery->whereHas('challengeDetail.prerequisites.quizDetail', function($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            });
        }
        
        $challenges = $challengesQuery->get();
                      
        $user = auth()->user();
        $attemptedChallengeIds = $user->quizAttempts()
                                     ->where('completed', true)
                                     ->pluck('set_id')
                                     ->toArray();
        
        // Check if user has met prerequisites for each challenge
        foreach ($challenges as $challenge) {
            $challenge->canAttempt = $challenge->challengeDetail->hasCompletedPrerequisites($user);
        }
        
        return view('challenges.index', compact('challenges', 'attemptedChallengeIds', 'subjects', 'search', 'subjectId'));
    }
    
    public function show(Set $set)
    {
        if ($set->type !== 'challenge') {
            abort(404);
        }
        
        $set->load(['challengeDetail.prerequisites.quizDetail.subject', 'challengeDetail.prerequisites.quizDetail.topic']);
        $user = auth()->user();
        
        // Check if the user has already attempted this challenge
        if ($set->isAttemptedBy($user)) {
            $attempt = QuizAttempt::where('user_id', $user->id)
                                 ->where('set_id', $set->id)
                                 ->where('completed', true)
                                 ->first();
                                 
            return redirect()->route('results.show', $attempt);
        }
        
        // Check if user has met prerequisites
        if (!$set->challengeDetail->hasCompletedPrerequisites($user)) {
            return redirect()->route('challenges.index')
                           ->with('error', 'You must complete all prerequisites before attempting this challenge.');
        }
        
        return view('challenges.show', compact('set'));
    }
    
    public function attempt(Set $set, Request $request)
    {
        if ($set->type !== 'challenge') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if the user has already attempted this challenge
        if ($set->isAttemptedBy($user)) {
            return redirect()->route('challenges.index')
                           ->with('error', 'You have already completed this challenge.');
        }
        
        // Check if user has met prerequisites
        if (!$set->challengeDetail->hasCompletedPrerequisites($user)) {
            return redirect()->route('challenges.index')
                           ->with('error', 'You must complete all prerequisites before attempting this challenge.');
        }
        
        $set->load(['questions' => function($query) {
            $query->orderBy('question_number');
        }, 'challengeDetail']);
        
        $questions = $set->questions;
        $currentPage = $request->query('page', 1);
        $questionsPerPage = 1;
        $currentQuestion = $questions->skip(($currentPage - 1) * $questionsPerPage)
                                   ->take($questionsPerPage)
                                   ->first();
        
        if (!$currentQuestion) {
            abort(404);
        }
        
        // Create or get existing attempt
        $attempt = QuizAttempt::firstOrCreate(
            [
                'user_id' => $user->id,
                'set_id' => $set->id,
                'completed' => false
            ],
            [
                'score' => 0,
                'total_questions' => $questions->count()
            ]
        );
        
        // Start timer if not already started and if challenge has timer
        if (!$attempt->started_at && isset($set->challengeDetail->timer_minutes) && $set->challengeDetail->timer_minutes > 0) {
            $attempt->startTimer($set->challengeDetail->timer_minutes);
        }
        
        // Check if timer has expired
        if ($attempt->hasTimerExpired()) {
            // Auto-submit the challenge with current answers
            return $this->autoSubmitExpiredChallenge($attempt, $set);
        }
        
        $timer_minutes = isset($set->challengeDetail->timer_minutes) ? $set->challengeDetail->timer_minutes : null;
        $remaining_seconds = $attempt->remaining_time;
        
        return view('challenges.attempt', [
            'set' => $set,
            'question' => $currentQuestion,
            'currentPage' => $currentPage,
            'totalPages' => $questions->count(),
            'attempt' => $attempt,
            'timer_minutes' => $timer_minutes,
            'remaining_seconds' => $remaining_seconds
        ]);
    }
    
    private function autoSubmitExpiredChallenge(QuizAttempt $attempt, Set $set)
    {
        // Get all answered questions
        $answeredQuestions = $attempt->answers()->pluck('selected_answer', 'question_id')->toArray();
        
        // Calculate score based on currently answered questions
        $score = 0;
        foreach ($answeredQuestions as $questionId => $answer) {
            $question = $set->questions->firstWhere('id', $questionId);
            if ($question && $question->correct_answer === $answer) {
                $score++;
            }
        }
        
        $attempt->update([
            'score' => $score,
            'completed' => true
        ]);
        
        // Calculate challenge points based on score percentage
        $totalQuestions = $set->questions->count();
        $scorePercentage = ($score / $totalQuestions) * 100;
        
        // Award points based on percentage
        $pointsToAward = 0;
        if ($scorePercentage >= 20 && $scorePercentage < 40) {
            $pointsToAward = 2;
        } elseif ($scorePercentage >= 40 && $scorePercentage < 60) {
            $pointsToAward = 4;
        } elseif ($scorePercentage >= 60 && $scorePercentage < 80) {
            $pointsToAward = 6;
        } elseif ($scorePercentage >= 80 && $scorePercentage < 100) {
            $pointsToAward = 8;
        } elseif ($scorePercentage == 100) {
            $pointsToAward = 10;
        }
        
        // Add points to user
        $attempt->user->addPoints($pointsToAward);
        
        return redirect()->route('results.show', $attempt)
                        ->with('warning', 'Your time has expired. The challenge was automatically submitted.');
    }
    
    public function submit(Request $request, Set $set)
    {
        if ($set->type !== 'challenge') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if the user has already attempted this challenge
        if ($set->isAttemptedBy($user)) {
            return redirect()->route('challenges.index')
                        ->with('error', 'You have already completed this challenge.');
        }
        
        $attempt = QuizAttempt::where('user_id', $user->id)
                            ->where('set_id', $set->id)
                            ->where('completed', false)
                            ->firstOrFail();
                            
        // Check if timer has expired
        if ($attempt->hasTimerExpired()) {
            return $this->autoSubmitExpiredChallenge($attempt, $set);
        }
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string|size:1',
        ]);
        
        $score = 0;
        $set->load('questions');
        
        foreach ($validated['answers'] as $questionId => $answer) {
            $question = $set->questions->firstWhere('id', $questionId);
            
            if (!$question) {
                continue;
            }
            
            $isCorrect = $question->correct_answer === $answer;
            
            if ($isCorrect) {
                $score++;
            }
            
            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'selected_answer' => $answer,
                'is_correct' => $isCorrect
            ]);
        }
        
        $attempt->update([
            'score' => $score,
            'completed' => true
        ]);
        
        // Calculate challenge points based on score percentage
        $totalQuestions = $set->questions->count();
        $scorePercentage = ($score / $totalQuestions) * 100;
        
        // Award points based on percentage
        $pointsToAward = 0;
        if ($scorePercentage >= 20 && $scorePercentage < 40) {
            $pointsToAward = 2;
        } elseif ($scorePercentage >= 40 && $scorePercentage < 60) {
            $pointsToAward = 4;
        } elseif ($scorePercentage >= 60 && $scorePercentage < 80) {
            $pointsToAward = 6;
        } elseif ($scorePercentage >= 80 && $scorePercentage < 100) {
            $pointsToAward = 8;
        } elseif ($scorePercentage == 100) {
            $pointsToAward = 10;
        }
        
        // Add points to user
        $user->addPoints($pointsToAward);
        
        return redirect()->route('results.show', $attempt);
    }
}