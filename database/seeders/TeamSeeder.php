<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['id' => 1, 'name' => 'إبداع (تصميم)', 'slug' => 'design', 'members_count' => 8],
            ['id' => 2, 'name' => 'سوشيال ميديا', 'slug' => 'social', 'members_count' => 6],
            ['id' => 3, 'name' => 'تقني', 'slug' => 'tech', 'members_count' => 5],
            ['id' => 4, 'name' => 'تلجرام', 'slug' => 'telegram', 'members_count' => 4],
            ['id' => 5, 'name' => 'محتوى', 'slug' => 'content', 'members_count' => 7],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(['id' => $team['id']], $team);
        }
    }
}
