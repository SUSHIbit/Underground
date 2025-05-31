<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\TournamentParticipant;

class UEPointsController extends Controller
{
    /**
     * Display the UEPoints information page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get UEPoints spending history (retakes)
        $spendingHistory = QuizAttempt::where('user_id', $user->id)
                         ->where('is_retake', true)
                         ->where('ue_points_spent', '>', 0)
                         ->with(['set'])
                         ->orderBy('created_at', 'desc')
                         ->get()
                         ->map(function($attempt) {
                             return [
                                 'type' => 'spending',
                                 'activity' => $attempt->set->type === 'quiz' ? 'Quiz Retake' : 'Challenge Retake',
                                 'description' => $attempt->set->type === 'quiz' 
                                     ? $attempt->set->quizDetail->subject->name ?? 'Quiz'
                                     : $attempt->set->challengeDetail->name ?? 'Challenge',
                                 'points' => -$attempt->ue_points_spent,
                                 'score' => $attempt->score,
                                 'total_questions' => $attempt->total_questions,
                                 'created_at' => $attempt->created_at,
                                 'icon' => $attempt->set->type === 'quiz' ? 'quiz' : 'challenge'
                             ];
                         });

        // Get UEPoints earning history from quiz/challenge completions (first attempts only)
        $earningHistory = QuizAttempt::where('user_id', $user->id)
                        ->where('completed', true)
                        ->where('is_retake', false)
                        ->with(['set.quizDetail.subject', 'set.challengeDetail'])
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($attempt) {
                            return [
                                'type' => 'earning',
                                'activity' => $attempt->set->type === 'quiz' ? 'Quiz Completion' : 'Challenge Completion',
                                'description' => $attempt->set->type === 'quiz' 
                                    ? $attempt->set->quizDetail->subject->name ?? 'Quiz'
                                    : $attempt->set->challengeDetail->name ?? 'Challenge',
                                'points' => 2, // Standard +2 UEPoints for completions
                                'score' => $attempt->score,
                                'total_questions' => $attempt->total_questions,
                                'created_at' => $attempt->created_at,
                                'icon' => $attempt->set->type === 'quiz' ? 'quiz' : 'challenge'
                            ];
                        });

        // Get UEPoints earning history from tournament participation
        $tournamentHistory = TournamentParticipant::where('user_id', $user->id)
                          ->with(['tournament'])
                          ->orderBy('created_at', 'desc')
                          ->get()
                          ->flatMap(function($participant) {
                              $activities = [];
                              
                              // Add participation reward (+2 UEPoints for joining)
                              $activities[] = [
                                  'type' => 'earning',
                                  'activity' => 'Tournament Participation',
                                  'description' => $participant->tournament->title,
                                  'points' => 2,
                                  'score' => null,
                                  'total_questions' => null,
                                  'created_at' => $participant->created_at,
                                  'icon' => 'tournament'
                              ];
                              
                              // Add ranking reward if tournament has finished and points were awarded
                              if ($participant->ue_points_awarded && $participant->ue_points_awarded > 0 && $participant->ranking_calculated) {
                                  $rankText = '';
                                  switch($participant->tournament_rank) {
                                      case 1: $rankText = '🥇 1st Place'; break;
                                      case 2: $rankText = '🥈 2nd Place'; break;
                                      case 3: $rankText = '🥉 3rd Place'; break;
                                      default: $rankText = "#{$participant->tournament_rank} Place"; break;
                                  }
                                  
                                  $activities[] = [
                                      'type' => 'earning',
                                      'activity' => 'Tournament Ranking',
                                      'description' => $participant->tournament->title . " - " . $rankText,
                                      'points' => $participant->ue_points_awarded,
                                      'score' => $participant->score,
                                      'total_questions' => null,
                                      'created_at' => $participant->updated_at, // Use updated_at for ranking awards
                                      'icon' => 'trophy'
                                  ];
                              }
                              
                              return $activities;
                          });

        // Combine all activities and sort by date
        $allActivities = $spendingHistory
                        ->concat($earningHistory)
                        ->concat($tournamentHistory)
                        ->sortByDesc('created_at')
                        ->take(10) // Limit to most recent 10 activities
                        ->values();

        // Calculate totals
        $totalEarned = $allActivities->where('type', 'earning')->sum('points');
        $totalSpent = abs($allActivities->where('type', 'spending')->sum('points'));
        
        return view('uepoints.index', compact('user', 'allActivities', 'totalEarned', 'totalSpent'));
    }
}