<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'id' => 1,
                'name' => 'موقع الأكاديمية الرسمي',
                'description' => 'بناء وتطوير الموقع الرسمي للأكاديمية',
                'active' => true,
                'status' => 'new',
                'creator_id' => 1,
                'updated_by' => null,
                'created_at' => Carbon::parse('2025-01-01 00:00:00'),
            ],
            [
                'id' => 2,
                'name' => 'دورة التسويق الرقمي',
                'description' => 'إنتاج محتوى وتطوير منصة الدورة',
                'active' => true,
                'status' => 'in_progress',
                'creator_id' => 1,
                'updated_by' => null,
                'created_at' => Carbon::parse('2025-02-01 00:00:00'),
            ],
            [
                'id' => 3,
                'name' => 'تطبيق إدارة المشاريع',
                'description' => 'تطوير تطبيق ويب لإدارة مشاريع الأكاديمية',
                'active' => true,
                'status' => 'issue',
                'creator_id' => 1,
                'updated_by' => null,
                'created_at' => Carbon::parse('2025-03-01 00:00:00'),
            ],
            [
                'id' => 4,
                'name' => 'حملة توعية الخصوصية',
                'description' => 'إعداد محتوى توعوي حول حماية البيانات الشخصية',
                'active' => false,
                'status' => 'done',
                'creator_id' => 1,
                'updated_by' => null,
                'created_at' => Carbon::parse('2025-04-01 00:00:00'),
            ],
        ];
        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['id' => $project['id']],[
                    'name'        => $project['name'],
                    'description' => $project['description'],
                    'active'      => $project['active'],
                    'status'      => $project['status'],
                    'creator_id'  => $project['creator_id'],
                    'updated_by'  => $project['updated_by'],
                    'created_at'  => $project['created_at'],
                    'updated_at'  => now(),
                    ]
            );
        }
    }
}
