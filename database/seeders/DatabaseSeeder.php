<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Admission;
use App\Models\Announcement;
use App\Models\Olympiad;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $superAdmin = User::query()->updateOrCreate([
            'email' => 'admin@maktabbot.uz',
        ], [
            'name' => 'Super Admin',
            'password' => 'secret123',
            'role' => UserRole::SuperAdmin,
        ]);

        $school = School::query()->updateOrCreate([
            'slug' => 'quva-ideal',
        ], [
            'name' => 'Quva Ideal Maktabi',
            'city' => 'Quva',
            'district' => 'Farg`ona',
            'address' => 'Quva shahri, Mustaqillik ko`chasi 12',
            'phone' => '+998901234567',
            'director_name' => 'Karimov Jasur Umarovich',
            'is_active' => true,
        ]);

        $school->bot()->updateOrCreate([
            'school_id' => $school->id,
        ], [
            'bot_name' => $school->name,
            'bot_username' => 'QuvaIdealBot',
            'welcome_text' => 'Assalomu alaykum. Quva Ideal Maktabi botiga xush kelibsiz.',
            'main_menu_text' => 'Asosiy menyu',
            'menu_buttons' => [
                ['label' => 'Olimpiadalar', 'action' => 'olympiads'],
                ['label' => 'Vakansiyalar', 'action' => 'vacancies'],
                ['label' => 'Qabul', 'action' => 'admissions'],
                ['label' => 'E`lonlar', 'action' => 'announcements'],
            ],
            'is_active' => true,
        ]);

        $schoolAdmin = User::query()->updateOrCreate([
            'email' => 'admin@quva-ideal.uz',
        ], [
            'name' => 'Maktab Admini',
            'password' => 'secret123',
            'role' => UserRole::SchoolAdmin,
        ]);

        SchoolAdmin::query()->updateOrCreate([
            'user_id' => $schoolAdmin->id,
            'school_id' => $school->id,
        ], [
            'permissions' => ['olympiad', 'vacancy', 'admission', 'announcement'],
        ]);

        Vacancy::query()->updateOrCreate([
            'school_id' => $school->id,
            'title' => 'Matematika o`qituvchisi',
        ], [
            'created_by' => $superAdmin->id,
            'category' => 'teacher',
            'subject' => 'Matematika',
            'description' => 'Tajribali matematika o`qituvchisi kerak.',
            'requirements' => 'Kamida 2 yil tajriba.',
            'status' => 'published',
        ]);

        Olympiad::query()->updateOrCreate([
            'school_id' => $school->id,
            'title' => 'Matematika olimpiadasi 2026',
        ], [
            'created_by' => $superAdmin->id,
            'subject' => 'Matematika',
            'registration_start' => now(),
            'registration_end' => now()->addDays(10),
            'olympiad_date' => now()->addDays(15)->toDateString(),
            'status' => 'published',
            'is_free' => true,
        ]);

        Admission::query()->updateOrCreate([
            'school_id' => $school->id,
            'title' => '2026-2027 o`quv yili qabuli',
        ], [
            'created_by' => $superAdmin->id,
            'academic_year' => 2026,
            'target_classes' => [1, 5, 10],
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => 'published',
        ]);

        Announcement::query()->updateOrCreate([
            'school_id' => $school->id,
            'title' => 'Yangi qabul boshlandi',
        ], [
            'created_by' => $superAdmin->id,
            'message_text' => '2026-2027 o`quv yili uchun qabul boshlandi.',
            'target_type' => 'all_users',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
