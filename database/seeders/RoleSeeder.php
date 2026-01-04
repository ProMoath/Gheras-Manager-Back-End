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
        // نستخدم createOrFirst لضمان عدم التكرار
        $roles = [
            ['id' => 1, 'name' => 'admin', 'label' => 'مدير النظام'],
            ['id' => 2, 'name' => 'supervisor', 'label' => 'مشرف'],
            ['id' => 3, 'name' => 'volunteer', 'label' => 'متطوع'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
