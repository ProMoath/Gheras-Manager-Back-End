<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            ['id' => 1, 'name' => 'موقع الأكاديمية الرسمي', 'description' => 'بناء وتطوير الموقع الرسمي للأكاديمية', 'created_by' => 1],
            ['id' => 2, 'name' => 'دورة التسويق الرقمي', 'description' => 'إنتاج محتوى وتطوير منصة الدورة', 'created_by' => 1],
            ['id' => 3, 'name' => 'تطبيق إدارة المشاريع', 'description' => 'تطوير تطبيق ويب لإدارة مشاريع الأكاديمية', 'created_by' => 1],
            ['id' => 4, 'name' => 'حملة توعية الخصوصية', 'description' => 'إعداد محتوى توعوي حول الخصوصية الرقمية', 'created_by' => 1],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['id' => $project['id']], $project);
        }
    }
}
