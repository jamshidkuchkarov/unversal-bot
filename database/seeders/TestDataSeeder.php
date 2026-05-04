<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Vacancy;
use App\Models\Olympiad;
use App\Models\Admission;
use App\Models\Announcement;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();

        if (!$school) {
            $this->command->error('School not found! Please create a school first.');
            return;
        }

        $this->command->info('Creating test data for: ' . $school->name);

        // Create Vacancies
        $this->createVacancies($school);

        // Create Olympiads
        $this->createOlympiads($school);

        // Create Admissions
        $this->createAdmissions($school);

        // Create Announcements
        $this->createAnnouncements($school);

        $this->command->info('✅ Test data created successfully!');
    }

    private function createVacancies(School $school): void
    {
        // Get first admin user
        $admin = \App\Models\User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping vacancies.');
            return;
        }

        $vacancies = [
            [
                'title' => 'Matematika o\'qituvchisi',
                'subject' => 'Matematika',
                'description' => "Maktabimizga tajribali matematika o'qituvchisi kerak.\n\nTalablar:\n- Oliy ma'lumot\n- 3+ yil tajriba\n- Zamonaviy o'qitish metodlarini bilish\n- O'quvchilar bilan ishlash ko'nikmasi",
                'requirements' => "- Oliy pedagogik ma'lumot\n- Matematika mutaxassisligi\n- Kompyuter savodxonligi\n- Ingliz tili (ixtiyoriy)",
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'salary_note' => 'Tajribaga qarab',
                'work_schedule' => 'Dushanba-Juma, 8:00-17:00',
                'deadline' => Carbon::now()->addDays(30),
                'status' => 'published',
            ],
            [
                'title' => 'Ingliz tili o\'qituvchisi',
                'subject' => 'Ingliz tili',
                'description' => "Ingliz tili o'qituvchisi lavozimiga xodim qidirilmoqda.\n\nBiz taklif qilamiz:\n- Raqobatbardosh ish haqi\n- Professional rivojlanish\n- Qulay ish muhiti\n- Zamonaviy jihozlar",
                'requirements' => "- IELTS 7.0+ yoki CEFR C1\n- Pedagogik tajriba\n- Zamonaviy metodikalarni bilish\n- Kommunikabellik",
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'salary_note' => 'Sertifikatga qarab',
                'work_schedule' => 'Dushanba-Shanba, 8:00-14:00',
                'deadline' => Carbon::now()->addDays(45),
                'status' => 'published',
            ],
            [
                'title' => 'Fizika o\'qituvchisi',
                'subject' => 'Fizika',
                'description' => "Fizika fanidan malakali o'qituvchi talab qilinadi.\n\nVazifalar:\n- 7-11 sinflarda dars berish\n- Laboratoriya mashg'ulotlari\n- O'quvchilarni olimpiadalarga tayyorlash\n- Fanlar bo'yicha loyihalar",
                'requirements' => "- Fizika-matematika yo'nalishi\n- Tajriba: 2+ yil\n- Laboratoriya jihozlari bilan ishlash\n- Olimpiada tayyorlash tajribasi",
                'salary_min' => 5500000,
                'salary_max' => 9000000,
                'work_schedule' => 'Dushanba-Juma, 8:00-16:00',
                'deadline' => Carbon::now()->addDays(20),
                'status' => 'published',
            ],
            [
                'title' => 'Boshlang\'ich sinf o\'qituvchisi',
                'subject' => 'Boshlang\'ich ta\'lim',
                'description' => "1-4 sinf o'quvchilari bilan ishlaydigan o'qituvchi kerak.\n\nBiz qidiramiz:\n- Bolalarni sevadigan\n- Sabr-toqatli\n- Ijodiy yondashuvga ega\n- Zamonaviy metodlarni qo'llaydigan mutaxassis",
                'requirements' => "- Boshlang'ich ta'lim mutaxassisligi\n- Bolalar psixologiyasini bilish\n- Zamonaviy o'qitish texnologiyalari\n- Ota-onalar bilan ishlash tajribasi",
                'salary_min' => 4500000,
                'salary_max' => 7000000,
                'work_schedule' => 'Dushanba-Juma, 8:00-13:00',
                'deadline' => Carbon::now()->addDays(25),
                'status' => 'published',
            ],
        ];

        foreach ($vacancies as $vacancy) {
            Vacancy::create(array_merge($vacancy, [
                'school_id' => $school->id,
                'created_by' => $admin->id,
            ]));
        }

        $this->command->info('✓ Created ' . count($vacancies) . ' vacancies');
    }

    private function createOlympiads(School $school): void
    {
        // Get first admin user
        $admin = \App\Models\User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping olympiads.');
            return;
        }

        $olympiads = [
            [
                'title' => 'Matematika Olimpiadasi 2026',
                'subject' => 'Matematika',
                'description' => "Yillik matematika olimpiadasi!\n\n📅 Sana: 15-may, 2026\n⏰ Vaqt: 10:00\n📍 Manzil: Maktab binosi, 2-qavat\n\nIshtirokchilar:\n- 5-11 sinf o'quvchilari\n- Boshqa maktablardan ham ishtirok etish mumkin\n\nMukofotlar:\n🥇 1-o'rin: 2,000,000 so'm\n🥈 2-o'rin: 1,500,000 so'm\n🥉 3-o'rin: 1,000,000 so'm\n\nRo'yxatdan o'tish: 10-may gacha",
                'registration_start' => Carbon::now(),
                'registration_end' => Carbon::now()->addDays(13),
                'olympiad_date' => Carbon::now()->addDays(18),
                'olympiad_location' => 'Maktab binosi, 2-qavat',
                'max_participants' => 100,
                'status' => 'published',
            ],
            [
                'title' => 'Ingliz tili Olimpiadasi',
                'subject' => 'Ingliz tili',
                'description' => "Ingliz tili bo'yicha viloyat darajasidagi olimpiada!\n\n📚 Bosqichlar:\n1. Yozma test (Grammar, Vocabulary)\n2. Listening\n3. Speaking\n4. Writing essay\n\n🎯 Maqsad:\n- O'quvchilar bilimini baholash\n- Eng yaxshi natijalarni aniqlash\n- Respublika olimpiadasiga tayyorlash\n\nQo'shimcha:\n- Barcha ishtirokchilarga sertifikat\n- G'oliblarga sovg'alar\n- Eng yaxshilar respublika olimpiadasiga yuboriladi",
                'registration_start' => Carbon::now(),
                'registration_end' => Carbon::now()->addDays(18),
                'olympiad_date' => Carbon::now()->addDays(25),
                'olympiad_location' => 'Viloyat ta\'lim boshqarmasi',
                'max_participants' => 50,
                'status' => 'published',
            ],
            [
                'title' => 'Fizika va Astronomiya Olimpiadasi',
                'subject' => 'Fizika',
                'description' => "Fizika va astronomiya fanlaridan qiziqarli olimpiada!\n\n🔬 Mavzular:\n- Mexanika\n- Elektrodinamika\n- Optika\n- Astronomiya asoslari\n\n🏆 Mukofotlar:\n- Diplom va sovg'alar\n- Teleskop (1-o'rin)\n- Ilmiy kitoblar to'plami\n- Observatoriyaga sayohat\n\n👨‍🏫 Hakamlar:\n- Universitetdan professorlar\n- Tajribali o'qituvchilar",
                'registration_start' => Carbon::now(),
                'registration_end' => Carbon::now()->addDays(28),
                'olympiad_date' => Carbon::now()->addDays(35),
                'olympiad_location' => 'Maktab fizika laboratoriyasi',
                'max_participants' => 60,
                'status' => 'published',
            ],
        ];

        foreach ($olympiads as $olympiad) {
            Olympiad::create(array_merge($olympiad, [
                'school_id' => $school->id,
                'created_by' => $admin->id,
            ]));
        }

        $this->command->info('✓ Created ' . count($olympiads) . ' olympiads');
    }

    private function createAdmissions(School $school): void
    {
        // Get first admin user
        $admin = \App\Models\User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping admissions.');
            return;
        }

        $admissions = [
            [
                'title' => '2026-2027 o\'quv yili qabuli',
                'description' => "Hurmatli ota-onalar!\n\nMaktabimiz 2026-2027 o'quv yiliga qabul e'lon qiladi.\n\n📋 Qabul shartlari:\n\n1️⃣ 1-sinf uchun:\n- Yosh: 6-7 yosh\n- Hujjatlar: tug'ilganlik guvohnomasi, tibbiy ma'lumotnoma\n- Suhbat: ota-ona va bola bilan\n\n2️⃣ 2-11 sinflar uchun:\n- O'tgan yil baholar daftarchasi\n- Xarakteristika (oldingi maktabdan)\n- Test sinovlari\n\n🎯 Bizning afzalliklarimiz:\n- Zamonaviy ta'lim dasturi\n- Malakali o'qituvchilar\n- Kichik sinflar (20-25 o'quvchi)\n- Qo'shimcha to'garaklar\n- Zamonaviy jihozlar\n- Xavfsiz muhit\n\n💰 To'lov:\n- Boshlang'ich: 800,000 so'm/oy\n- O'rta: 1,000,000 so'm/oy\n- Yuqori: 1,200,000 so'm/oy\n\n📞 Qo'shimcha ma'lumot:\nTel: +998 90 123 45 67\nManzil: Farg'ona viloyati, Quva tumani",
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(50),
                'requirements' => "1-sinf:\n- 6-7 yosh\n- Tug'ilganlik guvohnomasi\n- Tibbiy ma'lumotnoma\n\n2-11 sinf:\n- Baholar daftarchasi\n- Xarakteristika\n- Test sinovlari",
                'status' => 'published',
            ],
            [
                'title' => 'Iqtidorli o\'quvchilar uchun maxsus qabul',
                'description' => "🌟 Iqtidorli bolalar uchun maxsus dastur!\n\nKimlar uchun:\n- Olimpiada g'oliblari\n- Yuqori natijalarga ega o'quvchilar\n- Qo'shimcha fanlarni chuqur o'rganmoqchi bo'lganlar\n\n📚 Dastur:\n- Chuqurlashtirilgan fanlar\n- Qo'shimcha darslar (bepul)\n- Olimpiadalarga tayyorlov\n- Universitetlarga tayyorgarlik\n- Xorijiy tillar (2 til)\n\n🎁 Imtiyozlar:\n- 50% chegirma (olimpiada g'oliblari)\n- Bepul qo'shimcha darslar\n- Bepul to'garaklar\n- Stipendiya dasturi\n\n📝 Qabul jarayoni:\n1. Ariza topshirish\n2. Test sinovlari\n3. Suhbat\n4. Natijalar e'lon qilish\n\n⏰ Muddatlar:\n- Ariza: 1-may gacha\n- Test: 5-may\n- Natijalar: 10-may",
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(4),
                'requirements' => "- Olimpiada diplomi yoki\n- Oldingi yil o'rtacha baho 4.5+\n- Tavsiya xati\n- Test sinovlari",
                'status' => 'published',
            ],
        ];

        foreach ($admissions as $admission) {
            Admission::create(array_merge($admission, [
                'school_id' => $school->id,
                'created_by' => $admin->id,
            ]));
        }

        $this->command->info('✓ Created ' . count($admissions) . ' admissions');
    }

    private function createAnnouncements(School $school): void
    {
        // Get first admin user
        $admin = \App\Models\User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping announcements.');
            return;
        }

        $announcements = [
            [
                'title' => 'Bahor ta\'tili e\'lon qilinadi',
                'content' => "🌸 Hurmatli o'quvchilar va ota-onalar!\n\nBahor ta'tili:\n📅 Boshlanishi: 25-mart\n📅 Tugashi: 5-aprel\n\nTa'til davomida:\n- Maktab yopiq\n- Qo'shimcha darslar yo'q\n- To'garaklar to'xtatiladi\n\n⚠️ Eslatma:\n6-aprel, dushanba kunidan darslar boshlanadi.\n\nYaxshi dam oling! 🎉",
                'type' => 'general',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Ota-onalar yig\'ini',
                'content' => "👨‍👩‍👧‍👦 Ota-onalar yig'ini!\n\n📅 Sana: 30-aprel, 2026\n⏰ Vaqt: 18:00\n📍 Manzil: Maktab aktlar zali\n\nKun tartibi:\n1. O'quv yili natijalari\n2. Yozgi ta'til rejalari\n3. Yangi o'quv yili tayyorgarligi\n4. Savol-javob\n\n📝 Qatnashish majburiy!\n\nQo'shimcha ma'lumot:\nTel: +998 90 123 45 67",
                'type' => 'event',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Yangi kompyuter sinfi ochildi',
                'content' => "💻 Ajoyib yangilik!\n\nMaktabimizda yangi zamonaviy kompyuter sinfi ochildi!\n\n🖥️ Jihozlar:\n- 30 ta yangi kompyuter\n- Proyektor va smart doska\n- Yuqori tezlikdagi internet\n- Zamonaviy dasturlar\n\n📚 Darslar:\n- Dasturlash (Python, JavaScript)\n- Grafik dizayn\n- Video montaj\n- Robotexnika\n\n🎯 Barcha o'quvchilar uchun bepul!\n\nRo'yxatdan o'tish: Informatika o'qituvchisida",
                'type' => 'news',
                'status' => 'published',
                'published_at' => Carbon::now()->subHours(12),
            ],
            [
                'title' => 'Sport musobaqalari',
                'content' => "⚽ Sport musobaqalari e'lon qilinadi!\n\n🏆 Turlar:\n- Futbol (o'g'il bolalar)\n- Voleybol (qizlar)\n- Basketbol (aralash)\n- Shaxmat\n- Stol tennisi\n\n📅 Sana: 10-15 may\n📍 Manzil: Maktab sport majmuasi\n\n🎁 Mukofotlar:\n- Diplom va medallar\n- Sovg'alar\n- Kubok (g'oliblar uchun)\n\n✍️ Ro'yxatdan o'tish:\nJismoniy tarbiya o'qituvchisida\nMuddat: 5-may gacha\n\nHammani kutamiz! 💪",
                'type' => 'event',
                'status' => 'published',
                'published_at' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create(array_merge($announcement, [
                'school_id' => $school->id,
                'created_by' => $admin->id,
            ]));
        }

        $this->command->info('✓ Created ' . count($announcements) . ' announcements');
    }
}
