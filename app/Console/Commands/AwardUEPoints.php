<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AwardUEPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uepoints:award {--user=} {--points=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award UEPoints to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // Award points to all student users
            $points = $this->option('points') ? (int) $this->option('points') : 5;
            
            $count = User::where('role', 'student')->count();
            
            if ($count > 0) {
                User::where('role', 'student')->chunk(100, function ($users) use ($points) {
                    foreach ($users as $user) {
                        $user->addUEPoints($points);
                        $user->notify(new \App\Notifications\UEPointsAwarded($points, 'Administrative award to all students'));
                    }
                });
                
                $this->info("Successfully awarded {$points} UEPoints to all {$count} students");
            } else {
                $this->info("No student users found");
            }
            
            return;
        }
        
        if ($this->option('user') && $this->option('points')) {
            // Award points to specific user
            $userIdentifier = $this->option('user');
            $points = (int) $this->option('points');
            
            $user = User::where('email', $userIdentifier)
                      ->orWhere('username', $userIdentifier)
                      ->first();
            
            if ($user) {
                $user->addUEPoints($points);
                $user->notify(new \App\Notifications\UEPointsAwarded($points, 'Manual award by administrator'));
                $this->info("Successfully awarded {$points} UEPoints to {$user->name} ({$user->email})");
            } else {
                $this->error("User not found with email or username: {$userIdentifier}");
            }
            
            return;
        }
        
        $this->error('No valid options provided. Use --user=email --points=N or --all --points=N');
    }
}