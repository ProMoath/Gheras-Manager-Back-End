<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,    // 1. الرتب أولاً
          /*  TeamSeeder::class, */   // 2. الفرق
            UserSeeder::class,    // 3. المستخدمين (يرتبطون بالرتب والفرق)
          /*  ProjectSeeder::class, // 4. المشاريع (تحتاج مستخدمين)
            TaskSeeder::class,    // 5. المهام (تحتاج الجميع)*/
        ]);
    }
}
