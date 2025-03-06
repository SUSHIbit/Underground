<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\QuizController; 
use App\Http\Controllers\ChallengeController; 
use App\Http\Controllers\ResultController; 
use App\Http\Controllers\LecturerDashboardController; 
use App\Http\Controllers\AccessorDashboardController; 
use App\Http\Controllers\SetApprovalController; 

use Illuminate\Support\Facades\Route;

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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

require __DIR__.'/auth.php';
