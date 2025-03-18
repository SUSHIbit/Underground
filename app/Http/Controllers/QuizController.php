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
        if ($set->isAttemptedBy($user)) {
            $attempt = QuizAttempt::where('user_id', $user->id)
                                 ->where('set_id', $set->id)
                                 ->where('completed', true)
                                 ->first();
                                 
            return redirect()->route('results.show', $attempt);
        }
        
        return view('quizzes.show', compact('set'));
    }
    
    public function attempt(Set $set, Request $request)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if the user has already attempted this quiz
        if ($set->isAttemptedBy($user)) {
            return redirect()->route('quizzes.index')
                           ->with('error', 'You have already completed this quiz.');
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
            'remaining_seconds' => $remaining_seconds
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
        
        // Award 5 points for completing any quiz
        $attempt->user->addPoints(5);
        
        return redirect()->route('results.show', $attempt)
                        ->with('warning', 'Your time has expired. The quiz was automatically submitted.');
    }
    
    public function submit(Request $request, Set $set)
    {
        if ($set->type !== 'quiz') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Check if the user has already attempted this quiz
        if ($set->isAttemptedBy($user)) {
            return redirect()->route('quizzes.index')
                        ->with('error', 'You have already completed this quiz.');
        }
        
        $attempt = QuizAttempt::where('user_id', $user->id)
                            ->where('set_id', $set->id)
                            ->where('completed', false)
                            ->firstOrFail();
        
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
        
        // Award 5 points for completing any quiz
        $user->addPoints(5);
        
        return redirect()->route('results.show', $attempt);
    }
}