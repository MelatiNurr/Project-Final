<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Regular User',
            'username' => 'user',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $positiveWords = ['growth', 'profit', 'success', 'boom', 'increase', 'stable', 'positive', 'gain', 'surplus', 'recovery', 'uptrend', 'thrive', 'prosper', 'improvement', 'advantage'];
        foreach ($positiveWords as $word) {
            \App\Models\PositiveWord::create(['word' => $word]);
        }

        $negativeWords = ['loss', 'crisis', 'risk', 'storm', 'decrease', 'drop', 'negative', 'decline', 'deficit', 'recession', 'downtrend', 'fail', 'bankruptcy', 'threat', 'disaster'];
        foreach ($negativeWords as $word) {
            \App\Models\NegativeWord::create(['word' => $word]);
        }
    }
}
