<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RankController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\QuizController; 
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\LegionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController; 
use App\Http\Controllers\UEPointsController;
use App\Http\Controllers\SettingsController;
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

    // Leaderboard Route (kept for backward compatibility)
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    
    // Settings Route
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
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

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users/{user}/edit-role', [AdminController::class, 'editRole'])->name('edit-role');
    Route::put('/users/{user}/update-role', [AdminController::class, 'updateRole'])->name('update-role');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/ranks', [RankController::class, 'index'])->name('ranks.index');
});

// Legion Routes
Route::middleware(['auth'])->group(function () {
    // Basic Legion CRUD routes
    Route::get('/legions', [LegionController::class, 'index'])->name('legions.index');
    Route::get('/legions/create', [LegionController::class, 'create'])->name('legions.create');
    Route::post('/legions', [LegionController::class, 'store'])->name('legions.store');
    Route::get('/legions/{legion}', [LegionController::class, 'show'])->name('legions.show');
    Route::get('/legions/{legion}/edit', [LegionController::class, 'edit'])->name('legions.edit');
    Route::put('/legions/{legion}', [LegionController::class, 'update'])->name('legions.update');
    
    // Legion membership actions
    Route::post('/legions/{legion}/apply', [LegionController::class, 'apply'])->name('legions.apply');
    Route::post('/legions/{legion}/invite', [LegionController::class, 'invite'])->name('legions.invite');
    Route::post('/legions/{legion}/accept-application/{user}', [LegionController::class, 'acceptApplication'])->name('legions.accept-application');
    Route::post('/legions/{legion}/reject-application/{user}', [LegionController::class, 'rejectApplication'])->name('legions.reject-application');
    Route::post('/legions/{legion}/accept-invitation', [LegionController::class, 'acceptInvitation'])->name('legions.accept-invitation');
    Route::post('/legions/{legion}/reject-invitation', [LegionController::class, 'rejectInvitation'])->name('legions.reject-invitation');
    Route::post('/legions/{legion}/leave', [LegionController::class, 'leave'])->name('legions.leave');
    
    // Legion management routes
    Route::post('/legions/{legion}/promote/{user}', [LegionController::class, 'promote'])->name('legions.promote');
    Route::post('/legions/{legion}/demote/{user}', [LegionController::class, 'demote'])->name('legions.demote');
    Route::post('/legions/{legion}/transfer-leadership/{user}', [LegionController::class, 'transferLeadership'])->name('legions.transfer-leadership');
    Route::post('/legions/{legion}/remove-member/{user}', [LegionController::class, 'removeMember'])->name('legions.remove-member');
    
    // Legion leaderboard
    Route::get('/legion-leaderboard', [LegionController::class, 'leaderboard'])->name('legions.leaderboard');
});

Route::middleware(['auth'])->group(function () {
    // Quiz retake routes
    Route::post('/quizzes/{set}/retake', [QuizController::class, 'retake'])->name('quizzes.retake');

    // Challenge retake routes
    Route::post('/challenges/{set}/retake', [ChallengeController::class, 'retake'])->name('challenges.retake');

    // UEPoints management route
    Route::get('/uepoints', [UEPointsController::class, 'index'])->name('uepoints.index');

    Route::put('/tournaments/{tournament}/update-team', [TournamentController::class, 'updateTeam'])->name('tournaments.update-team');
});

require __DIR__.'/auth.php';