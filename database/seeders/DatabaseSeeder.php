<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DatabaseSeeder
 *
 * Seeds test users covering every login scenario:
 *   1. demo@example.com        / password    → verified, can login
 *   2. unverified@example.com  / password    → not verified, login blocked
 *   3. wrongpass@example.com   / password    → verified, but use "wrong password" to test
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── User 1: Verified demo user with 15 todos
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'              => 'Demo User',
                'password'          => bcrypt('password'),
                'is_verified'       => true,
                'email_verified_at' => now(),
                'verification_code' => null,
            ]
        );

        // Only seed todos if not already seeded
        if ($demoUser->todos()->count() === 0) {
            $categories = ['Work', 'Personal', 'Shopping', 'Health', 'Learning'];
            for ($i = 1; $i <= 15; $i++) {
                Todo::create([
                    'user_id'     => $demoUser->id,
                    'title'       => $categories[($i - 1) % count($categories)] . ' task #' . $i,
                    'description' => 'This is the description for todo item number ' . $i . '.',
                ]);
            }
        }

        // ── User 2: Unverified user (email never verified)
        User::firstOrCreate(
            ['email' => 'unverified@example.com'],
            [
                'name'              => 'Unverified User',
                'password'          => bcrypt('password'),
                'is_verified'       => false,
                'email_verified_at' => null,
                'verification_code' => Str::random(64),
            ]
        );

        // ── User 3: Verified user (use wrong password to test invalid creds)
        User::firstOrCreate(
            ['email' => 'wrongpass@example.com'],
            [
                'name'              => 'Wrong Pass User',
                'password'          => bcrypt('password'),
                'is_verified'       => true,
                'email_verified_at' => now(),
                'verification_code' => null,
            ]
        );

        $this->command->info('demo@example.com        / password       → verified');
        $this->command->info('unverified@example.com  / password       → not verified');
        $this->command->info('wrongpass@example.com   / wrong password  → wrong password test');
    }
}
