<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleMap = [
            'admin' => Role::admin,
            'supervisor' => Role::supervisor,
            'volunteer' => Role::volunteer
        ];

        $users = [
            ['id' => 1, 'name' => 'أحمد محمد', 'email' => 'ahmad@ghras.com', 'role' => 'admin', 'status' => true, 'teams' => [1, 3], 'job_field' => 'تطوير ويب'],
            ['id' => 2, 'name' => 'فاطمة أحمد', 'email' => 'fatima@ghras.com', 'role' => 'supervisor', 'status' => true, 'teams' => [1], 'job_field' => 'تصميم جرافيك'],
            ['id' => 3, 'name' => 'يوسف علي', 'email' => 'youssef@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [5], 'job_field' => 'كتابة محتوى'],
            ['id' => 4, 'name' => 'مريم حسن', 'email' => 'maryam@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [2], 'job_field' => 'تسويق إلكتروني'],
            ['id' => 5, 'name' => 'عمر خالد', 'email' => 'omar@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [4], 'job_field' => 'إدارة مجتمع'],
            ['id' => 6, 'name' => 'نور الدين', 'email' => 'nour@ghras.com', 'role' => 'supervisor', 'status' => true, 'teams' => [3], 'job_field' => 'البرمجة'],
            ['id' => 7, 'name' => 'سارة عبدالله', 'email' => 'sara@ghras.com', 'role' => 'volunteer', 'status' => false, 'teams' => [1], 'job_field' => 'تحرير فيديو'],
            ['id' => 8, 'name' => 'خالد إبراهيم', 'email' => 'khaled@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [1, 2], 'job_field' => 'تصميم UI/UX'],
            ['id' => 9, 'name' => 'ليلى محمود', 'email' => 'layla@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [5], 'job_field' => 'الكتابة الإبداعية'],
            ['id' => 10, 'name' => 'حسن علي', 'email' => 'hassan@ghras.com', 'role' => 'supervisor', 'status' => true, 'teams' => [2, 5], 'job_field' => 'إدارة محتوى'],
            ['id' => 11, 'name' => 'زينب أحمد', 'email' => 'zainab@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [1, 2], 'job_field' => 'التصوير الفوتوغرافي'],
            ['id' => 12, 'name' => 'طارق السيد', 'email' => 'tarek@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [3], 'job_field' => 'تطوير تطبيقات'],
            ['id' => 13, 'name' => 'هدى محمد', 'email' => 'huda@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [4], 'job_field' => 'إدارة تلجرام'],
            ['id' => 14, 'name' => 'إبراهيم خليل', 'email' => 'ibrahim@ghras.com', 'role' => 'volunteer', 'status' => false, 'teams' => [3], 'job_field' => 'تحليل البيانات'],
            ['id' => 15, 'name' => 'ريم عبدالرحمن', 'email' => 'reem@ghras.com', 'role' => 'volunteer', 'status' => true, 'teams' => [2], 'job_field' => 'التسويق الرقمي'],
        ];

        foreach ($users as $data) {
            $teams = $data['teams']; // نحفظ الفرق جانباً
            unset($data['teams']);  // نحذفها من مصفوفة البيانات الأساسية لأنها ليست عموداً في جدول المستخدمين

            $user = User::updateOrCreate(['id' => $data['id']], [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'role_id' => $roleMap[$data['role']],
                'status' => $data['status'],
                'job_field' => $data['job_field'],
            ]);

            $user->teams()->sync($teams);
        }

    }
}
