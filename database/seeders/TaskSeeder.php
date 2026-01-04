<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            ['id' => 1, 'title' => 'تصميم بوستر رمضان', 'status' => 'new', 'priority' => 'urgent', 'project_id' => 2, 'team_id' => 1, 'assignee_id' => 2, 'created_by' => 1],
            ['id' => 2, 'title' => 'كتابة مقال عن الصبر', 'status' => 'in_progress', 'priority' => 'normal', 'project_id' => 4, 'team_id' => 5, 'assignee_id' => 3, 'created_by' => 1, 'work_hours' => 5.5],
            ['id' => 3, 'title' => 'نشر منشور يومي', 'status' => 'done', 'priority' => 'normal', 'project_id' => 2, 'team_id' => 2, 'assignee_id' => 4, 'created_by' => 1, 'work_hours' => 2],
            ['id' => 4, 'title' => 'إصلاح خطأ في الموقع', 'status' => 'issue', 'priority' => 'very_urgent', 'project_id' => 1, 'team_id' => 3, 'assignee_id' => 6, 'created_by' => 1, 'work_hours' => 3],
            ['id' => 5, 'title' => 'إدارة مجموعة الأسئلة', 'status' => 'scheduled', 'priority' => 'medium', 'project_id' => 4, 'team_id' => 4, 'assignee_id' => 5, 'created_by' => 1],
            ['id' => 6, 'title' => 'توثيق واجهات API', 'status' => 'docs', 'priority' => 'normal', 'project_id' => 3, 'team_id' => 3, 'assignee_id' => 6, 'created_by' => 1, 'work_hours' => 8],
            ['id' => 7, 'title' => 'تصميم شعار جديد', 'status' => 'new', 'priority' => 'medium', 'project_id' => 1, 'team_id' => 1, 'assignee_id' => null, 'created_by' => 1],
            ['id' => 8, 'title' => 'مراجعة المحتوى الأسبوعي', 'status' => 'in_progress', 'priority' => 'urgent', 'project_id' => 4, 'team_id' => 5, 'assignee_id' => 3, 'created_by' => 1, 'work_hours' => 2.5],
            ['id' => 9, 'title' => 'تطوير صفحة هبوط جديدة', 'status' => 'scheduled', 'priority' => 'urgent', 'project_id' => 2, 'team_id' => 3, 'assignee_id' => 12, 'created_by' => 1],
            ['id' => 10, 'title' => 'إنشاء فيديو ترويجي', 'status' => 'new', 'priority' => 'medium', 'project_id' => 2, 'team_id' => 1, 'assignee_id' => 8, 'created_by' => 1],
            ['id' => 11, 'title' => 'كتابة سلسلة مقالات', 'status' => 'in_progress', 'priority' => 'normal', 'project_id' => 4, 'team_id' => 5, 'assignee_id' => 9, 'created_by' => 1, 'work_hours' => 12],
            ['id' => 12, 'title' => 'حملة إعلانية على فيسبوك', 'status' => 'scheduled', 'priority' => 'very_urgent', 'project_id' => 2, 'team_id' => 2, 'assignee_id' => 15, 'created_by' => 1],
            ['id' => 13, 'title' => 'إدارة المجموعة اليومية', 'status' => 'in_progress', 'priority' => 'normal', 'project_id' => 4, 'team_id' => 4, 'assignee_id' => 13, 'created_by' => 1, 'work_hours' => 4],
            ['id' => 14, 'title' => 'تصوير محتوى المنتجات', 'status' => 'done', 'priority' => 'medium', 'project_id' => 2, 'team_id' => 1, 'assignee_id' => 11, 'created_by' => 1, 'work_hours' => 6],
            ['id' => 15, 'title' => 'تحديث قاعدة البيانات', 'status' => 'docs', 'priority' => 'normal', 'project_id' => 3, 'team_id' => 3, 'assignee_id' => 6, 'created_by' => 1, 'work_hours' => 10],
            ['id' => 16, 'title' => 'تصميم هوية بصرية', 'status' => 'new', 'priority' => 'urgent', 'project_id' => 1, 'team_id' => 1, 'assignee_id' => null, 'created_by' => 1],
            ['id' => 17, 'title' => 'إعداد تقرير شهري', 'status' => 'scheduled', 'priority' => 'normal', 'project_id' => 4, 'team_id' => 5, 'assignee_id' => 3, 'created_by' => 1],
            ['id' => 18, 'title' => 'اختبار التطبيق الجديد', 'status' => 'issue', 'priority' => 'very_urgent', 'project_id' => 3, 'team_id' => 3, 'assignee_id' => 12, 'created_by' => 1, 'work_hours' => 5],
        ];

        foreach ($tasks as $task) {
            Task::updateOrCreate(['id' => $task['id']], $task);
        }
    }
}
