# IMPLEMENTATSIYA REJASI
## Laravel Universal School Bot - Development Roadmap

**Loyiha:** Maktablar uchun Universal Telegram Bot  
**Sana:** 27.04.2026

---

## 📊 HOZIRGI HOLAT TAHLILI

### ✅ Mavjud Funksiyalar

**Database:**
- ✅ schools, school_admins, school_bots
- ✅ telegram_users, bot_sessions
- ✅ vacancies, vacancy_applications
- ✅ olympiads, olympiad_registrations
- ✅ admissions, admission_applications
- ✅ announcements, announcement_recipients
- ✅ channels

**Bot Handlers:**
- ✅ Webhook controller
- ✅ BotUpdateHandler (asosiy logic)
- ✅ MandatorySubscriptionService
- ✅ TelegramBotService
- ✅ Vakansiya arizalari (to'liq)
- ✅ Olimpiada ro'yxati (to'liq)
- ⚠️ Qabul (faqat ko'rsatish, ariza yo'q)
- ⚠️ E'lonlar (faqat ko'rsatish)

**Admin Panel:**
- ✅ Dashboard
- ✅ Schools CRUD
- ✅ Vacancies CRUD
- ✅ Vacancy Applications
- ✅ Olympiads CRUD
- ✅ Olympiad Registrations
- ✅ Admissions CRUD
- ⚠️ Admission Applications (controller mavjud, view yo'q)
- ✅ Announcements CRUD
- ✅ Channels CRUD
- ✅ Settings

### ❌ Yo'q Funksiyalar

**Bot:**
- ❌ Maktab haqida bo'limi
- ❌ Ko'p tillilik (uz/ru)
- ❌ Sozlamalar bo'limi
- ❌ Qabul arizalari (bot flow)
- ❌ CV/Foto yuklash (vakansiya)
- ❌ Media yuborish (e'lonlar)
- ❌ Profil va mening arizalarim

**Admin:**
- ❌ School Info CRUD
- ❌ Foydalanuvchilar bo'limi
- ❌ Hisobotlar
- ❌ Export funksiyalari
- ❌ Grafiklar (dashboard)
- ❌ Media upload interfeysi

---

## 🎯 PRIORITETLAR

### P0 - Critical (1-2 hafta)
1. **Maktab haqida bo'limi** - Asosiy funksiya
2. **Ko'p tillilik** - Zarur xususiyat
3. **Qabul arizalari bot flow** - To'liq qilish kerak

### P1 - High (2-3 hafta)
4. **Vakansiya CV/Foto yuklash**
5. **E'lonlar media yuborish**
6. **Sozlamalar bo'limi**
7. **Admin: School Info CRUD**

### P2 - Medium (3-4 hafta)
8. **Foydalanuvchilar bo'limi**
9. **Export funksiyalari**
10. **Dashboard grafiklar**
11. **Hisobotlar**

### P3 - Low (4-5 hafta)
12. **Olimpiada natijalar import**
13. **Sertifikat generatsiya**
14. **Scheduled broadcast**
15. **Advanced analytics**

---

## 📅 HAFTALIK REJA

### HAFTA 1: Asosiy Funksiyalar

#### Kun 1-2: Maktab Haqida
**Backend:**
```bash
# Migration
php artisan make:migration create_school_info_table

# Model
php artisan make:model SchoolInfo

# Controller
php artisan make:controller Admin/SchoolInfoController --resource
```

**Tasks:**
- [ ] Migration yaratish
- [ ] Model va relationships
- [ ] Admin CRUD views
- [ ] Form validation
- [ ] Image upload (logo, gallery)
- [ ] Routes qo'shish

**Bot Handler:**
- [ ] `showSchoolInfo()` method
- [ ] Inline keyboard navigation
- [ ] Subpages (about, history, contacts, etc.)

#### Kun 3-4: Ko'p Tillilik
**Backend:**
```bash
# Migrations
php artisan make:migration create_user_preferences_table
php artisan make:migration create_bot_translations_table

# Lang files
resources/lang/uz/bot.php
resources/lang/ru/bot.php
```

**Tasks:**
- [ ] Migrations yaratish
- [ ] UserPreference model
- [ ] Language middleware
- [ ] Helper functions (trans_bot())
- [ ] Lang fayllar yaratish
- [ ] Barcha bot matnlarni tarjima qilish

**Bot Handler:**
- [ ] Language selection handler
- [ ] Save user preference
- [ ] Dynamic language loading

#### Kun 5: Qabul Arizalari Bot Flow
**Tasks:**
- [ ] Conversation states qo'shish
- [ ] Multi-step form handler
- [ ] File upload (documents)
- [ ] Validation
- [ ] Save to admission_applications
- [ ] Confirmation message

---

### HAFTA 2: Media va Yaxshilashlar

#### Kun 1-2: Vakansiya CV/Foto Yuklash
**Tasks:**
- [ ] File upload handler
- [ ] File validation (size, type)
- [ ] Storage configuration
- [ ] Update vacancy_applications
- [ ] Admin view files

#### Kun 3-4: E'lonlar Media
**Tasks:**
- [ ] Photo upload handler
- [ ] Video upload handler
- [ ] Document upload handler
- [ ] Inline buttons builder
- [ ] Admin media upload UI
- [ ] Preview funksiyasi

#### Kun 5: Sozlamalar Bo'limi
**Tasks:**
- [ ] Settings menu handler
- [ ] Profile view
- [ ] My applications view
- [ ] Help page
- [ ] Notifications toggle

---

### HAFTA 3: Admin Panel

#### Kun 1-2: School Info Admin
**Tasks:**
- [ ] CRUD views
- [ ] Rich text editor (TinyMCE/CKEditor)
- [ ] Image gallery manager
- [ ] Map integration (Google Maps/Yandex)
- [ ] Social links manager

#### Kun 3-4: Foydalanuvchilar Bo'limi
**Tasks:**
- [ ] Users list view
- [ ] Filters (subscribed, active, etc.)
- [ ] Search functionality
- [ ] User profile view
- [ ] User applications view
- [ ] Send message to user

#### Kun 5: Export Funksiyalari
**Tasks:**
- [ ] Excel export (maatwebsite/excel)
- [ ] Vacancy applications export
- [ ] Olympiad registrations export
- [ ] Admission applications export
- [ ] Users export

---

### HAFTA 4: Dashboard va Hisobotlar

#### Kun 1-2: Dashboard Grafiklar
**Tasks:**
- [ ] Chart.js integration
- [ ] Users growth chart
- [ ] Applications chart
- [ ] Activity chart
- [ ] Real-time stats

#### Kun 3-4: Hisobotlar
**Tasks:**
- [ ] Reports page
- [ ] Date range filter
- [ ] Statistics calculations
- [ ] Export reports
- [ ] PDF generation (optional)

#### Kun 5: Testing va Bug Fixes
**Tasks:**
- [ ] Manual testing
- [ ] Bug fixes
- [ ] Code review
- [ ] Performance optimization

---

### HAFTA 5: Advanced Features

#### Kun 1-2: Olimpiada Natijalar
**Tasks:**
- [ ] Results import (Excel)
- [ ] Bulk update scores
- [ ] Certificate template
- [ ] Certificate generation
- [ ] Send results to users

#### Kun 3-4: Scheduled Broadcast
**Tasks:**
- [ ] Queue setup
- [ ] Scheduled jobs
- [ ] Cron configuration
- [ ] Broadcast status tracking
- [ ] Retry failed messages

#### Kun 5: Final Testing
**Tasks:**
- [ ] Full system testing
- [ ] Load testing
- [ ] Security audit
- [ ] Documentation update

---

## 🛠️ TEXNIK IMPLEMENTATSIYA

### 1. Maktab Haqida Migration

```php
// database/migrations/xxxx_create_school_info_table.php
Schema::create('school_info', function (Blueprint $table) {
    $table->id();
    $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
    $table->longText('about_text_uz')->nullable();
    $table->longText('about_text_ru')->nullable();
    $table->longText('history_text_uz')->nullable();
    $table->longText('history_text_ru')->nullable();
    $table->text('mission_text_uz')->nullable();
    $table->text('mission_text_ru')->nullable();
    $table->text('vision_text_uz')->nullable();
    $table->text('vision_text_ru')->nullable();
    $table->string('director_name')->nullable();
    $table->string('director_photo')->nullable();
    $table->text('director_message_uz')->nullable();
    $table->text('director_message_ru')->nullable();
    $table->json('achievements')->nullable();
    $table->json('gallery_images')->nullable();
    $table->string('video_url')->nullable();
    $table->string('contact_phone')->nullable();
    $table->string('contact_email')->nullable();
    $table->text('address_uz')->nullable();
    $table->text('address_ru')->nullable();
    $table->decimal('map_latitude', 10, 8)->nullable();
    $table->decimal('map_longitude', 11, 8)->nullable();
    $table->json('working_hours')->nullable();
    $table->json('social_links')->nullable();
    $table->timestamps();
});
```

### 2. Ko'p Tillilik

```php
// app/Services/Telegram/LanguageService.php
class LanguageService
{
    public function getUserLanguage(TelegramUser $user): string
    {
        return $user->preference?->language ?? 'uz';
    }
    
    public function setUserLanguage(TelegramUser $user, string $lang): void
    {
        $user->preference()->updateOrCreate(
            ['telegram_user_id' => $user->id],
            ['language' => $lang]
        );
    }
    
    public function trans(string $key, array $replace = [], ?string $lang = null): string
    {
        return __("bot.{$key}", $replace, $lang);
    }
}
```

```php
// resources/lang/uz/bot.php
return [
    'welcome' => 'Assalomu alaykum! :name',
    'main_menu' => 'Asosiy menyu',
    'school_info' => '🏫 Maktab haqida',
    'vacancies' => '📋 Vakansiyalar',
    'olympiads' => '🏆 Olimpiadalar',
    'admissions' => '🎓 Qabul',
    'announcements' => '📢 E\'lonlar',
    'settings' => '⚙️ Sozlamalar',
    // ...
];

// resources/lang/ru/bot.php
return [
    'welcome' => 'Здравствуйте! :name',
    'main_menu' => 'Главное меню',
    'school_info' => '🏫 О школе',
    'vacancies' => '📋 Вакансии',
    'olympiads' => '🏆 Олимпиады',
    'admissions' => '🎓 Прием',
    'announcements' => '📢 Объявления',
    'settings' => '⚙️ Настройки',
    // ...
];
```

### 3. Bot Handler Yangilanishi

```php
// app/Services/Telegram/BotUpdateHandler.php

private function handleMessage(Update $update, SchoolBot $schoolBot): void
{
    // Get user language
    $lang = $this->languageService->getUserLanguage($user);
    app()->setLocale($lang);
    
    // Handle text commands
    match ($text) {
        __('bot.school_info') => $this->showSchoolInfo($user, $schoolBot),
        __('bot.vacancies') => $this->sendVacancies($user->chat_id, $schoolBot),
        __('bot.olympiads') => $this->sendOlympiads($user->chat_id, $schoolBot),
        __('bot.admissions') => $this->sendAdmissions($user->chat_id, $schoolBot),
        __('bot.announcements') => $this->sendAnnouncements($user->chat_id, $schoolBot),
        __('bot.settings') => $this->showSettings($user, $schoolBot),
        default => $this->sendMainMenu($user->chat_id, $schoolBot),
    };
}

private function showSchoolInfo(TelegramUser $user, SchoolBot $schoolBot): void
{
    $info = $schoolBot->school->info;
    $lang = $this->languageService->getUserLanguage($user);
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => __('bot.about'), 'callback_data' => 'school_info:about'],
                ['text' => __('bot.history'), 'callback_data' => 'school_info:history'],
            ],
            [
                ['text' => __('bot.management'), 'callback_data' => 'school_info:management'],
                ['text' => __('bot.achievements'), 'callback_data' => 'school_info:achievements'],
            ],
            [
                ['text' => __('bot.gallery'), 'callback_data' => 'school_info:gallery'],
                ['text' => __('bot.video'), 'callback_data' => 'school_info:video'],
            ],
            [
                ['text' => __('bot.contacts'), 'callback_data' => 'school_info:contacts'],
                ['text' => __('bot.location'), 'callback_data' => 'school_info:location'],
            ],
            [
                ['text' => __('bot.back'), 'callback_data' => 'main_menu'],
            ],
        ],
    ];
    
    $text = __('bot.school_info_menu', ['school' => $schoolBot->school->name]);
    
    $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
}
```

### 4. File Upload Handler

```php
// app/Services/Telegram/FileUploadService.php
class FileUploadService
{
    public function handleDocument(Update $update, SchoolBot $schoolBot): ?string
    {
        $document = $update->getMessage()->document;
        
        // Validate file
        if ($document->fileSize > 10 * 1024 * 1024) { // 10MB
            throw new \Exception('File too large');
        }
        
        $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($document->mimeType, $allowedMimes)) {
            throw new \Exception('Invalid file type');
        }
        
        // Download file
        $file = $this->telegramBotService->api($schoolBot)->getFile(['file_id' => $document->fileId]);
        $filePath = $file->filePath;
        
        // Save to storage
        $content = file_get_contents("https://api.telegram.org/file/bot{$schoolBot->bot_token}/{$filePath}");
        $filename = uniqid() . '_' . $document->fileName;
        Storage::put("documents/{$filename}", $content);
        
        return "documents/{$filename}";
    }
    
    public function handlePhoto(Update $update, SchoolBot $schoolBot): ?string
    {
        $photos = $update->getMessage()->photo;
        $photo = end($photos); // Get largest photo
        
        $file = $this->telegramBotService->api($schoolBot)->getFile(['file_id' => $photo->fileId]);
        $filePath = $file->filePath;
        
        $content = file_get_contents("https://api.telegram.org/file/bot{$schoolBot->bot_token}/{$filePath}");
        $filename = uniqid() . '.jpg';
        Storage::put("photos/{$filename}", $content);
        
        return "photos/{$filename}";
    }
}
```

---

## 🧪 TESTING CHECKLIST

### Bot Testing
- [ ] /start - Welcome message
- [ ] Majburiy obuna tekshiruvi
- [ ] Maktab haqida - Barcha subpages
- [ ] Vakansiyalar - Ro'yxat va ariza
- [ ] Vakansiya - CV yuklash
- [ ] Vakansiya - Foto yuklash
- [ ] Olimpiadalar - Ro'yxat va ro'yxatdan o'tish
- [ ] Qabul - Ariza topshirish
- [ ] Qabul - Hujjat yuklash
- [ ] E'lonlar - Ko'rish
- [ ] Sozlamalar - Til almashtirish
- [ ] Sozlamalar - Profil
- [ ] Sozlamalar - Mening arizalarim
- [ ] Xato holatlari
- [ ] Noto'g'ri input

### Admin Testing
- [ ] Login/Logout
- [ ] Dashboard - Statistika
- [ ] Dashboard - Grafiklar
- [ ] School Info - CRUD
- [ ] School Info - Media upload
- [ ] Vacancies - CRUD
- [ ] Vacancy Applications - View
- [ ] Vacancy Applications - Export
- [ ] Olympiads - CRUD
- [ ] Olympiad Registrations - View
- [ ] Olympiad Registrations - Export
- [ ] Admissions - CRUD
- [ ] Admission Applications - View
- [ ] Admission Applications - Export
- [ ] Announcements - CRUD
- [ ] Announcements - Media upload
- [ ] Announcements - Broadcast
- [ ] Channels - CRUD
- [ ] Users - List
- [ ] Users - Profile
- [ ] Users - Export
- [ ] Reports - View
- [ ] Reports - Export
- [ ] Settings - Update

---

## 📦 DEPENDENCIES

### Yangi Packages

```bash
# Excel export
composer require maatwebsite/excel

# Image processing
composer require intervention/image

# PDF generation (optional)
composer require barryvdh/laravel-dompdf

# Charts (frontend)
npm install chart.js
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-deployment
- [ ] All tests passing
- [ ] Code review completed
- [ ] Database migrations ready
- [ ] Seeders updated
- [ ] .env.example updated
- [ ] Documentation updated

### Deployment
- [ ] Backup current database
- [ ] Pull latest code
- [ ] Run migrations
- [ ] Clear cache
- [ ] Restart queue workers
- [ ] Test webhook
- [ ] Monitor logs

### Post-deployment
- [ ] Smoke testing
- [ ] Monitor errors
- [ ] Check performance
- [ ] User feedback

---

## 📝 NOTES

### Important
- Barcha file upload lar validate qilinishi kerak
- User input sanitize qilinishi kerak
- Rate limiting qo'shish (spam prevention)
- Error handling to'liq bo'lishi kerak
- Logging comprehensive bo'lishi kerak

### Performance
- Database indexes tekshirish
- N+1 query muammolarini hal qilish
- Cache strategiyasi
- Queue workers monitoring

### Security
- CSRF protection
- XSS prevention
- SQL injection prevention
- File upload validation
- Rate limiting
- Input sanitization

---

**Tayyorlagan:** Claude AI  
**Sana:** 27.04.2026  
**Status:** Ready for Implementation
