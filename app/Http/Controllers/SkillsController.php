<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentParticipant;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SkillsController extends Controller
{
    /**
     * Display the skills dashboard with tournament statistics.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all tournament types
        $tournamentTypes = [
            'web_design' => 'Web Design',
            'hackathon' => 'Hackathon',
            'coup_detat' => 'Coup d\'État',
            'coding_competition' => 'Coding Competition',
            'mobile' => 'Mobile Development'
        ];
        
        // Get tournament statistics for the user
        $stats = $this->getUserTournamentStats($user->id);
        
        // Prepare data for charts
        $chartData = $this->prepareChartData($stats, $tournamentTypes);
        
        return view('skills.index', compact('user', 'stats', 'chartData', 'tournamentTypes'));
    }
    
    /**
     * Get tournament statistics for a user.
     *
     * @param int $userId
     * @return array
     */
    private function getUserTournamentStats($userId)
    {
        $stats = [];
        
        // Get all tournament participation for the user
        $participations = TournamentParticipant::where('user_id', $userId)
            ->with(['tournament' => function($query) {
                $query->select('id', 'tournament_type', 'title');
            }])
            ->get();
            
        // Group by tournament type
        $typeCounts = [];
        $typeScores = [];
        
        foreach ($participations as $participant) {
            if (!$participant->tournament) {
                continue;
            }
            
            $type = $participant->tournament->tournament_type;
            
            // Calculate points based on placement
            $points = $this->calculatePointsForPlacement($participant);
            
            if (!isset($typeScores[$type])) {
                $typeScores[$type] = 0;
                $typeCounts[$type] = 0;
            }
            
            $typeScores[$type] += $points;
            $typeCounts[$type]++;
        }
        
        // Calculate average scores
        foreach ($typeScores as $type => $score) {
            $count = $typeCounts[$type];
            $stats[$type] = [
                'total_score' => $score,
                'count' => $count,
                'average' => $count > 0 ? round($score / $count, 1) : 0
            ];
        }
        
        return $stats;
    }
    
    /**
     * Calculate points based on placement.
     *
     * @param TournamentParticipant $participant
     * @return int
     */
    private function calculatePointsForPlacement($participant)
    {
        // Default to participation points
        $points = 5;
        
        // If there's a ranking and score available
        if ($participant->score !== null) {
            // This is a simplified approach. In a real system, you would compare
            // with other participants to determine placement.
            if ($participant->score >= 90) {
                $points = 20; // Winner (1st place)
            } elseif ($participant->score >= 80) {
                $points = 15; // 2nd place
            } elseif ($participant->score >= 70) {
                $points = 10; // 3rd place
            }
            // Otherwise, keep the default participation points (5)
        }
        
        return $points;
    }
    
    /**
     * Prepare data for charts.
     *
     * @param array $stats
     * @param array $tournamentTypes
     * @return array
     */
    private function prepareChartData($stats, $tournamentTypes)
    {
        $data = [
            'types' => [],
            'averages' => [],
            'counts' => []
        ];
        
        // Include all tournament types, even if the user has no data for them
        foreach ($tournamentTypes as $key => $label) {
            $data['types'][] = $label;
            $data['averages'][] = isset($stats[$key]) ? $stats[$key]['average'] : 0;
            $data['counts'][] = isset($stats[$key]) ? $stats[$key]['count'] : 0;
        }
        
        return $data;
    }
}