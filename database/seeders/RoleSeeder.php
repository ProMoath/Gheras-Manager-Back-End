<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => Role::admin, 'name' => 'admin'],
            ['id' => Role::supervisor, 'name' => 'supervisor'],
            ['id' => Role::volunteer, 'name' => 'volunteer'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']],['name' => $role['name']]);
        }
    }
}
