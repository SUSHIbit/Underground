<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentParticipant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tournament_id', 
        'user_id', 
        'team_id',
        'role',
        'submission_url', 
        'score', 
        'feedback',
        'points_awarded',
        'tournament_rank',
        'ue_points_awarded',
        'ranking_calculated'
    ];
    
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function team()
    {
        return $this->belongsTo(TournamentTeam::class);
    }

    /**
     * Get all judge scores for this participant.
     */
    public function judgeScores()
    {
        return $this->hasMany(JudgeScore::class, 'tournament_participant_id');
    }

    /**
     * Get the judge score for a specific judge.
     */
    public function getJudgeScore($judgeUserId)
    {
        return $this->judgeScores()->where('judge_user_id', $judgeUserId)->first();
    }

    /**
     * Calculate and update the average score from all judge scores.
     */
    public function updateAverageScore()
    {
        $judgeScores = $this->judgeScores;
        
        if ($judgeScores->count() > 0) {
            $averageScore = $judgeScores->avg('score');
            $this->score = round($averageScore, 1);
            
            // Keep the original feedback approach for backward compatibility
            // but also provide methods to access all feedback
            $latestFeedback = $judgeScores->sortByDesc('created_at')->first()->feedback;
            $this->feedback = $latestFeedback;
            
            $this->save();
        }
    }

    /**
     * Get all judge feedback as a formatted collection
     */
    public function getAllJudgeFeedback()
    {
        return $this->judgeScores()
                    ->with('judge')
                    ->whereNotNull('feedback')
                    ->where('feedback', '!=', '')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($judgeScore) {
                        return [
                            'judge_name' => $judgeScore->judge->name,
                            'score' => $judgeScore->score,
                            'feedback' => $judgeScore->feedback,
                            'rubric_scores' => $judgeScore->rubric_scores,
                            'created_at' => $judgeScore->created_at,
                        ];
                    });
    }

    /**
     * Check if all assigned judges have provided scores
     */
    public function isFullyGradedByAllJudges()
    {
        $totalJudges = $this->tournament->judges()->count();
        $judgeCount = $this->judgeScores()->count();
        
        return $judgeCount >= $totalJudges;
    }

    /**
     * Get the breakdown of individual judge scores
     */
    public function getJudgeScoreBreakdown()
    {
        return $this->judgeScores()
                    ->with('judge')
                    ->orderBy('score', 'desc')
                    ->get()
                    ->map(function ($judgeScore) {
                        return [
                            'judge' => $judgeScore->judge->name,
                            'score' => $judgeScore->score,
                            'percentage' => round(($judgeScore->score / 10) * 100, 1),
                            'graded_at' => $judgeScore->created_at->format('M j, Y g:i a'),
                            'feedback' => $judgeScore->feedback,
                            'rubric_scores' => $judgeScore->rubric_scores,
                        ];
                    });
    }

    /**
     * Get average score by rubric category (if rubrics are used)
     */
    public function getRubricAverages()
    {
        if (!$this->tournament->rubrics()->exists()) {
            return collect();
        }
        
        $rubrics = $this->tournament->rubrics;
        $judgeScores = $this->judgeScores()->whereNotNull('rubric_scores')->get();
        
        if ($judgeScores->isEmpty()) {
            return collect();
        }
        
        $averages = [];
        
        foreach ($rubrics as $rubric) {
            $scores = $judgeScores->map(function ($judgeScore) use ($rubric) {
                return $judgeScore->rubric_scores[$rubric->id] ?? null;
            })->filter()->values();
            
            if ($scores->isNotEmpty()) {
                $averages[] = [
                    'title' => $rubric->title,
                    'weight' => $rubric->score_weight,
                    'average' => round($scores->avg(), 1),
                    'min' => $scores->min(),
                    'max' => $scores->max(),
                    'judge_count' => $scores->count(),
                ];
            }
        }
        
        return collect($averages);
    }

    /**
     * Get detailed scoring summary for display
     */
    public function getScoringDetailsSummary()
    {
        $judgeScores = $this->judgeScores()->with('judge')->get();
        
        if ($judgeScores->isEmpty()) {
            return null;
        }
        
        return [
            'final_score' => $this->score,
            'total_judges' => $judgeScores->count(),
            'expected_judges' => $this->tournament->judges()->count(),
            'score_range' => [
                'min' => $judgeScores->min('score'),
                'max' => $judgeScores->max('score'),
            ],
            'judges_with_feedback' => $judgeScores->where('feedback', '!=', null)->where('feedback', '!=', '')->count(),
            'all_scores' => $judgeScores->pluck('score')->toArray(),
            'score_distribution' => $this->getScoreDistribution($judgeScores),
        ];
    }

    /**
     * Helper method to get score distribution
     */
    private function getScoreDistribution($judgeScores)
    {
        $distribution = [
            'excellent' => 0,  // 9-10
            'good' => 0,       // 7-8.9
            'average' => 0,    // 5-6.9
            'poor' => 0,       // 0-4.9
        ];
        
        foreach ($judgeScores as $score) {
            $value = $score->score;
            if ($value >= 9) {
                $distribution['excellent']++;
            } elseif ($value >= 7) {
                $distribution['good']++;
            } elseif ($value >= 5) {
                $distribution['average']++;
            } else {
                $distribution['poor']++;
            }
        }
        
        return $distribution;
    }

    /**
     * Get the rubric scores for this participant.
     */
    public function rubricScores()
    {
        return $this->hasMany(RubricScore::class, 'tournament_participant_id');
    }

    /**
     * Override the score attribute setter to synchronize scores for team members
     * 
     * @param float|null $value
     * @return void
     */
    public function setScoreAttribute($value)
    {
        $this->attributes['score'] = $value;
        
        // Only synchronize if this is a team tournament and the score is being set
        if ($this->team_id && $value !== null && !$this->isDirty('score')) {
            // Use a flag to prevent infinite recursion
            if (!isset($this->syncingScore)) {
                $this->syncingScore = true;
                
                // Synchronize with other team members
                static::where('team_id', $this->team_id)
                    ->where('id', '!=', $this->id)
                    ->update(['score' => $value]);
                
                unset($this->syncingScore);
            }
        }
    }

    /**
     * Get rank display with ordinal suffix
     */
    public function getRankDisplayAttribute()
    {
        if (!$this->tournament_rank) {
            return 'Unranked';
        }

        $rank = $this->tournament_rank;
        $suffix = 'th';

        if ($rank % 100 >= 11 && $rank % 100 <= 13) {
            $suffix = 'th';
        } else {
            switch ($rank % 10) {
                case 1:
                    $suffix = 'st';
                    break;
                case 2:
                    $suffix = 'nd';
                    break;
                case 3:
                    $suffix = 'rd';
                    break;
            }
        }

        return $rank . $suffix;
    }

    /**
     * Get rank color class for styling
     */
    public function getRankColorAttribute()
    {
        if (!$this->tournament_rank) {
            return 'text-gray-400';
        }

        switch ($this->tournament_rank) {
            case 1:
                return 'text-yellow-400'; // Gold
            case 2:
                return 'text-gray-300'; // Silver
            case 3:
                return 'text-amber-600'; // Bronze
            default:
                return 'text-blue-400'; // Other ranks
        }
    }

    /**
     * Get rank background color for podium display
     */
    public function getRankBgColorAttribute()
    {
        if (!$this->tournament_rank) {
            return 'bg-gray-700';
        }

        switch ($this->tournament_rank) {
            case 1:
                return 'bg-yellow-600'; // Gold
            case 2:
                return 'bg-gray-400'; // Silver
            case 3:
                return 'bg-amber-600'; // Bronze
            default:
                return 'bg-blue-600'; // Other ranks
        }
    }

    /**
     * Check if this participant is in top 3
     */
    public function isTopThree()
    {
        return $this->tournament_rank && $this->tournament_rank <= 3;
    }
}