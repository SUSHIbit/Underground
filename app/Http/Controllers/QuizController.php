<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Subject;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        // Get all available subjects for the filter dropdown
        $subjects = Subject::orderBy('name')->get();
        
        // Get search and filter parameters
        $search = $request->input('search');
        $subjectId = $request->input('subject');
        
        // Start with base query for approved quizzes
        $quizzesQuery = Set::where('type', 'quiz')
                   ->where('status', 'approved')
                   ->with(['quizDetail.subject', 'quizDetail.topic']);
        
        // Apply search if provided
        if ($search) {
            $quizzesQuery->whereHas('quizDetail.subject', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('quizDetail.topic', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }
        
        // Apply subject filter if provided
        if ($subjectId) {
            $quizzesQuery->whereHas('quizDetail', function($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            });
        }
        
        // Get the quizzes
        $quizzes = $quizzesQuery->get();
                   
        $user = auth()->user();
        $attemptedQuizIds = $user->quizAttempts()
                               ->where('completed', true)
                               ->pluck('set_id')
                               ->toArray();
        
        return view('quizzes.index', compact('quizzes', 'attemptedQuizIds', 'subjects', 'search', 'subjectId'));
    }
    
    public function show(Set $set)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $set->load(['quizDetail.subject', 'quizDetail.topic', 'questions']);
        $user = auth()->user();
        
        // Check if the user has already attempted this quiz
        $attempt = QuizAttempt::where('user_id', $user->id)
                             ->where('set_id', $set->id)
                             ->where('completed', true)
                             ->orderBy('created_at', 'desc')
                             ->first();
        
        $isCompleted = $attempt !== null;
        $canRetake = $isCompleted && $user->hasEnoughUEPoints(5); // Assuming 5 UEPoints per retake
        
        return view('quizzes.show', compact('set', 'isCompleted', 'attempt', 'canRetake'));
    }
    
    public function retake(Set $set)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if UEPoints are sufficient (assuming 5 points per retake)
        if (!$user->hasEnoughUEPoints(5)) {
            return redirect()->route('quizzes.show', $set)
                           ->with('error', 'You do not have enough UEPoints to retake this quiz.');
        }
        
        // Find the original attempt
        $originalAttempt = QuizAttempt::where('user_id', $user->id)
                                    ->where('set_id', $set->id)
                                    ->where('completed', true)
                                    ->whereNull('original_attempt_id')
                                    ->first();
        
        if (!$originalAttempt) {
            $originalAttempt = QuizAttempt::where('user_id', $user->id)
                                        ->where('set_id', $set->id)
                                        ->where('completed', true)
                                        ->first();
        }
                                    
        if (!$originalAttempt) {
            return redirect()->route('quizzes.show', $set)
                           ->with('error', 'No previous attempt found.');
        }
        
        // Deduct UEPoints
        $user->deductUEPoints(5);
        
        // Create a new attempt as a retake
        $retakeAttempt = QuizAttempt::create([
            'user_id' => $user->id,
            'set_id' => $set->id,
            'score' => 0,
            'total_questions' => $set->questions->count(),
            'completed' => false,
            'is_retake' => true,
            'ue_points_spent' => 5,
            'original_attempt_id' => $originalAttempt->id
        ]);
        
        return redirect()->route('quizzes.attempt', $set)
                       ->with('success', 'You are now retaking the quiz. 5 UEPoints have been deducted.');
    }
    
    public function attempt(Set $set, Request $request)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if the user has an incomplete retake attempt
        $attemptQuery = QuizAttempt::where('user_id', $user->id)
                                 ->where('set_id', $set->id)
                                 ->where('completed', false);
        
        $attempt = $attemptQuery->first();
        
        // If no incomplete attempt exists, check if user has already completed the quiz
        if (!$attempt) {
            $completedAttempt = QuizAttempt::where('user_id', $user->id)
                                         ->where('set_id', $set->id)
                                         ->where('completed', true)
                                         ->first();
            
            // If completed and not explicitly requesting a retake, redirect to results
            if ($completedAttempt && !session('retaking')) {
                return redirect()->route('results.show', $completedAttempt);
            }
        }
        
        $set->load(['questions' => function($query) {
            $query->orderBy('question_number');
        }, 'quizDetail']);
        
        $questions = $set->questions;
        $currentPage = $request->query('page', 1);
        $questionsPerPage = 1;
        $currentQuestion = $questions->skip(($currentPage - 1) * $questionsPerPage)
                                   ->take($questionsPerPage)
                                   ->first();
        
        if (!$currentQuestion) {
            abort(404);
        }
        
        // Create or get existing attempt if doesn't exist
        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'set_id' => $set->id,
                'score' => 0,
                'total_questions' => $questions->count(),
                'completed' => false,
                'is_retake' => session('retaking', false)
            ]);
        }
        
        // Start timer if not already started and if quiz has timer
        if (!$attempt->started_at && isset($set->quizDetail->timer_minutes) && $set->quizDetail->timer_minutes > 0) {
            $attempt->startTimer($set->quizDetail->timer_minutes);
        }
        
        // Check if timer has expired
        if ($attempt->hasTimerExpired()) {
            // Auto-submit the quiz with current answers
            return $this->autoSubmitExpiredQuiz($attempt, $set);
        }
        
        $timer_minutes = isset($set->quizDetail->timer_minutes) ? $set->quizDetail->timer_minutes : null;
        $remaining_seconds = $attempt->remaining_time;
        
        return view('quizzes.attempt', [
            'set' => $set,
            'question' => $currentQuestion,
            'currentPage' => $currentPage,
            'totalPages' => $questions->count(),
            'attempt' => $attempt,
            'timer_minutes' => $timer_minutes,
            'remaining_seconds' => $remaining_seconds,
            'is_retake' => $attempt->is_retake
        ]);
    }
    
    private function autoSubmitExpiredQuiz(QuizAttempt $attempt, Set $set)
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
        
        // Mark the attempt as completed
        $attempt->update([
            'score' => $score,
            'completed' => true
        ]);
        
        // Award 5 points for completing any quiz (only if it's not a retake)
        if (!$attempt->is_retake) {
            $attempt->user->addPoints(5);
        }
        
        return redirect()->route('results.show', $attempt)
                        ->with('warning', 'Your time has expired. The quiz was automatically submitted.');
    }
    
    public function submit(Request $request, Set $set)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Find the current attempt
        $attempt = QuizAttempt::where('user_id', $user->id)
                            ->where('set_id', $set->id)
                            ->where('completed', false)
                            ->firstOrFail();
        
        $isRetake = $attempt->is_retake;
        
        // Check if timer has expired
        if ($attempt->hasTimerExpired()) {
            return $this->autoSubmitExpiredQuiz($attempt, $set);
        }
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string|size:1',
        ]);
        
        $score = 0;
        $set->load('questions');
        
        // Delete previous answers for this attempt (if it's a retake)
        $attempt->answers()->delete();
        
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
        
        // Award points only if it's not a retake
        if (!$isRetake) {
            $user->addPoints(5);
        } 
        // No UEPoints rewards for retakes
        
        return redirect()->route('results.show', $attempt);
    }
}