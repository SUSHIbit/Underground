<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function show(QuizAttempt $attempt)
    {
        // Make sure the user is only viewing their own attempts
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }
        
        $attempt->load(['set', 'answers.question']);
        
        if ($attempt->set->type === 'quiz') {
            $attempt->set->load('quizDetail.subject', 'quizDetail.topic');
        } else {
            $attempt->set->load('challengeDetail');
        }
        
        return view('results.show', compact('attempt'));
    }
}
