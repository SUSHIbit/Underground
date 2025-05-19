<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 student
        User::create([
            'name' => "SushiMaru",
            'username' => "sushimaru",
            'email' => "ariefsushi1@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'student',
        ]);

        // Create 2 lecturer
        User::create([
            'name' => "Zhafri",
            'username' => "zhafrihandsome",
            'email' => "zhafri@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'lecturer',
        ]);

        // Create 2 lecturer
        User::create([
            'name' => "Datillah",
            'username' => "datillahcantik",
            'email' => "datillah@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'lecturer',
        ]);

        // Create 1 accessor
        User::create([
            'name' => "Hana",
            'username' => "hanacantikl",
            'email' => "hana@gmail.com",
            'password' => Hash::make('password'),
            'role' => 'accessor',
        ]);

        // Create 1 admin
        User::create([
            'name' => "mimin",
            'username' => "mimin",
            'email' => "mimin@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'admin',
        ]);

        // Create 2 judge
        User::create([
            'name' => "judge1",
            'username' => "judge1",
            'email' => "judge1@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'student',
            'is_judge' => true,
        ]);

        // Create 2 judge
        User::create([
            'name' => "judge2",
            'username' => "judge2",
            'email' => "judge2@gmail.com",
            'password' => Hash::make('123456789'),
            'role' => 'student',
            'is_judge' => true,
        ]);

        // Create 2 judges (using the is_judge flag with student role)
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'name' => "Judge $i",
                'username' => "judge$i",
                'email' => "judge$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'student', // Judges have a regular role (usually student)
                'is_judge' => true,  // But they have the judge flag
                'points' => rand(500, 1000), // Judges usually have higher ranks
                'ue_points' => rand(10, 30),
            ]);
        }

        $this->command->info('Users created successfully: 5 students, 2 lecturers, 1 accessor, 1 admin, and 2 judges.');
    }
}