<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RankController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\QuizController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController; 
use App\Http\Controllers\ChallengeController; 
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SetApprovalController; 

use App\Http\Controllers\AccessorDashboardController; 
use App\Http\Controllers\LecturerDashboardController; 
use App\Http\Controllers\TournamentApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // Quiz Routes
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{set}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{set}/attempt', [QuizController::class, 'attempt'])->name('quizzes.attempt');
    Route::post('/quizzes/{set}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    
    // Challenge Routes
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/{set}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::get('/challenges/{set}/attempt', [ChallengeController::class, 'attempt'])->name('challenges.attempt');
    Route::post('/challenges/{set}/submit', [ChallengeController::class, 'submit'])->name('challenges.submit');
    
    // Results Routes
    Route::get('/results/{attempt}', [ResultController::class, 'show'])->name('results.show');

    // Add to routes/web.php
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tournament Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');
    Route::post('/tournaments/{tournament}/join', [TournamentController::class, 'join'])->name('tournaments.join');
    Route::post('/tournaments/{tournament}/submit', [TournamentController::class, 'submit'])->name('tournaments.submit');
});

// Lecturer Tournament Routes
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/tournaments', [LecturerDashboardController::class, 'tournaments'])->name('tournaments');
    Route::get('/tournaments/{tournament}/edit', [LecturerDashboardController::class, 'editTournament'])->name('tournaments.edit');
    Route::put('/tournaments/{tournament}', [LecturerDashboardController::class, 'updateTournament'])->name('tournaments.update');
    Route::post('/tournaments/{tournament}/submit', [LecturerDashboardController::class, 'submitTournamentForApproval'])->name('tournaments.submit');
});

// Accessor Tournament Routes  
Route::middleware(['auth', 'accessor'])->prefix('accessor')->name('accessor.')->group(function () {
    Route::get('/tournaments', [AccessorDashboardController::class, 'tournaments'])->name('tournaments');
    Route::get('/tournaments/{tournament}/review', [AccessorDashboardController::class, 'reviewTournament'])->name('tournaments.review');
    Route::post('/tournaments/{tournament}/comment', [TournamentApprovalController::class, 'addComment'])->name('tournaments.comment');
    Route::post('/tournaments/{tournament}/approve', [TournamentApprovalController::class, 'approve'])->name('tournaments.approve');
    Route::post('/tournaments/{tournament}/reject', [TournamentApprovalController::class, 'reject'])->name('tournaments.reject');
});

// Lecturer Routes
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/sets/{set}/edit', [LecturerDashboardController::class, 'edit'])->name('sets.edit');
    Route::put('/sets/{set}', [LecturerDashboardController::class, 'update'])->name('sets.update');
    Route::post('/sets/{set}/submit', [LecturerDashboardController::class, 'submitForApproval'])->name('sets.submit');
});

// Accessor Routes
Route::middleware(['auth', 'accessor'])->prefix('accessor')->name('accessor.')->group(function () {
    Route::get('/dashboard', [AccessorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/sets/{set}/review', [AccessorDashboardController::class, 'review'])->name('sets.review');
    Route::post('/sets/{set}/comment', [SetApprovalController::class, 'addComment'])->name('sets.comment');
    Route::post('/sets/{set}/approve', [SetApprovalController::class, 'approve'])->name('sets.approve');
    Route::post('/sets/{set}/reject', [SetApprovalController::class, 'reject'])->name('sets.reject');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/ranks', [RankController::class, 'index'])->name('ranks.index');
});

require __DIR__.'/auth.php';
