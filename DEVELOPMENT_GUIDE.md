# DEVELOPMENT GUIDE
## Maktab Bot - Dasturchilar uchun qo'llanma

**Versiya:** 2.0  
**Sana:** 27.04.2026

---

## 🚀 QUICK START

### 1. Loyihani Clone qilish

```bash
git clone <repository-url>
cd larabot-2
```

### 2. Dependencies o'rnatish

```bash
# Backend
composer install

# Frontend
npm install
```

### 3. Environment sozlash

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database sozlash

`.env` faylida:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=larabot
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migration va Seeder

```bash
php artisan migrate --seed
```

### 6. Storage link

```bash
php artisan storage:link
```

### 7. Development server

```bash
# Bitta terminal
php artisan serve

# Ikkinchi terminal
npm run dev

# Uchinchi terminal (queue worker)
php artisan queue:work
```

Yoki bitta buyruq bilan:
```bash
composer dev
```

---

## 📁 LOYIHA STRUKTURASI

```
larabot-2/
├── app/
│   ├── Enums/
│   │   └── UserRole.php                    # User rollari
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                      # Admin panel controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── SchoolController.php
│   │   │   │   ├── VacancyController.php
│   │   │   │   ├── OlympiadController.php
│   │   │   │   ├── AdmissionController.php
│   │   │   │   ├── AnnouncementController.php
│   │   │   │   ├── ChannelController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Auth/
│   │   │   │   └── AdminAuthenticatedSessionController.php
│   │   │   └── Telegram/
│   │   │       └── TelegramWebhookController.php
│   │   ├── Middleware/
│   │   │   └── EnsureAdmin.php
│   │   └── Requests/
│   │       └── Admin/                      # Form validation
│   ├── Models/
│   │   ├── School.php
│   │   ├── SchoolBot.php
│   │   ├── SchoolAdmin.php
│   │   ├── TelegramUser.php
│   │   ├── BotSession.php
│   │   ├── Vacancy.php
│   │   ├── VacancyApplication.php
│   │   ├── Olympiad.php
│   │   ├── OlympiadRegistration.php
│   │   ├── Admission.php
│   │   ├── Announcement.php
│   │   ├── Channel.php
│   │   └── User.php
│   ├── Services/
│   │   └── Telegram/
│   │       ├── TelegramBotService.php      # Telegram API wrapper
│   │       ├── BotUpdateHandler.php        # Asosiy bot logic
│   │       └── MandatorySubscriptionService.php
│   └── Support/
│       └── AdminSchoolContext.php          # Multi-tenant helper
├── database/
│   ├── migrations/                         # Database migrations
│   └── seeders/
│       └── DatabaseSeeder.php              # Demo data
├── resources/
│   ├── views/
│   │   ├── admin/                          # Admin panel views
│   │   │   ├── dashboard/
│   │   │   ├── schools/
│   │   │   ├── vacancies/
│   │   │   ├── olympiads/
│   │   │   ├── admissions/
│   │   │   ├── announcements/
│   │   │   ├── channels/
│   │   │   └── settings/
│   │   └── layouts/
│   └── lang/                               # Translations (qo'shish kerak)
│       ├── uz/
│       │   └── bot.php
│       └── ru/
│           └── bot.php
├── routes/
│   └── web.php                             # All routes
├── config/
│   └── telegram.php                        # Telegram config
├── TECHNICAL_SPECIFICATION.md              # To'liq TZ
├── IMPLEMENTATION_PLAN.md                  # Implementatsiya rejasi
├── TODO.md                                 # Task list
└── README.md                               # Asosiy README
```

---

## 🔑 ASOSIY KONTSEPTSIYALAR

### 1. Multi-Tenant Arxitektura

Har bir maktabning o'z bot tokeni va ma'lumotlari bor:

```php
// School model
$school = School::find(1);
$school->bot; // SchoolBot
$school->vacancies; // Vacancies
$school->olympiads; // Olympiads
```

### 2. Bot Session Management

Har bir foydalanuvchi uchun session saqlanadi:

```php
// BotSession model
$session = BotSession::where('telegram_user_id', $userId)->first();
$session->state; // 'idle', 'vacancy.full_name', etc.
$session->data; // JSON data
```

### 3. Conversation Flow

State-based conversation:

```php
// BotUpdateHandler.php
private function handleState(BotSession $session, TelegramUser $user, string $text): bool
{
    return match ($session->state) {
        'vacancy.full_name' => $this->advanceSession(...),
        'vacancy.age' => $this->advanceSession(...),
        'vacancy.phone' => $this->handlePhoneStep(...),
        // ...
    };
}
```

### 4. Webhook Flow

```
Telegram → Webhook → TelegramWebhookController → BotUpdateHandler → Response
```

---

## 🛠️ YANGI FUNKSIYA QO'SHISH

### Misol: "Maktab Haqida" bo'limini qo'shish

#### 1. Migration yaratish

```bash
php artisan make:migration create_school_info_table
```

```php
// database/migrations/xxxx_create_school_info_table.php
Schema::create('school_info', function (Blueprint $table) {
    $table->id();
    $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
    $table->longText('about_text_uz')->nullable();
    $table->longText('about_text_ru')->nullable();
    // ... other fields
    $table->timestamps();
});
```

#### 2. Model yaratish

```bash
php artisan make:model SchoolInfo
```

```php
// app/Models/SchoolInfo.php
class SchoolInfo extends Model
{
    protected $fillable = [
        'school_id',
        'about_text_uz',
        'about_text_ru',
        // ...
    ];
    
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
```

#### 3. Relationship qo'shish

```php
// app/Models/School.php
public function info(): HasOne
{
    return $this->hasOne(SchoolInfo::class);
}
```

#### 4. Controller yaratish

```bash
php artisan make:controller Admin/SchoolInfoController --resource
```

```php
// app/Http/Controllers/Admin/SchoolInfoController.php
class SchoolInfoController extends Controller
{
    public function edit(AdminSchoolContext $context)
    {
        $school = $context->current(auth()->user());
        $info = $school->info ?? new SchoolInfo();
        
        return view('admin.school-info.edit', compact('school', 'info'));
    }
    
    public function update(Request $request, AdminSchoolContext $context)
    {
        $school = $context->current(auth()->user());
        
        $school->info()->updateOrCreate(
            ['school_id' => $school->id],
            $request->validated()
        );
        
        return redirect()->back()->with('success', 'Saqlandi');
    }
}
```

#### 5. Routes qo'shish

```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/school-info', [SchoolInfoController::class, 'edit'])->name('school-info.edit');
    Route::put('/school-info', [SchoolInfoController::class, 'update'])->name('school-info.update');
});
```

#### 6. View yaratish

```blade
{{-- resources/views/admin/school-info/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<form method="POST" action="{{ route('admin.school-info.update') }}">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label>Maktab haqida (O'zbek)</label>
        <textarea name="about_text_uz" class="form-control">{{ old('about_text_uz', $info->about_text_uz) }}</textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Saqlash</button>
</form>
@endsection
```

#### 7. Bot Handler qo'shish

```php
// app/Services/Telegram/BotUpdateHandler.php

private function showSchoolInfo(TelegramUser $user, SchoolBot $schoolBot): void
{
    $info = $schoolBot->school->info;
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📖 Umumiy ma\'lumot', 'callback_data' => 'school_info:about'],
                ['text' => '📜 Tarix', 'callback_data' => 'school_info:history'],
            ],
            [
                ['text' => '⬅️ Orqaga', 'callback_data' => 'main_menu'],
            ],
        ],
    ];
    
    $text = "🏫 {$schoolBot->school->name}\n\nQaysi bo'limni ko'rmoqchisiz?";
    
    $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
}

private function handleCallback(Update $update, SchoolBot $schoolBot): void
{
    $callback = $update->callbackQuery;
    $data = $callback->data;
    
    if (str_starts_with($data, 'school_info:')) {
        $section = str_replace('school_info:', '', $data);
        $this->showSchoolInfoSection($user, $section, $schoolBot);
    }
}

private function showSchoolInfoSection(TelegramUser $user, string $section, SchoolBot $schoolBot): void
{
    $info = $schoolBot->school->info;
    $lang = $user->preference?->language ?? 'uz';
    
    $text = match($section) {
        'about' => $info->{"about_text_{$lang}"},
        'history' => $info->{"history_text_{$lang}"},
        default => 'Ma\'lumot topilmadi',
    };
    
    $keyboard = [
        'inline_keyboard' => [
            [['text' => '⬅️ Orqaga', 'callback_data' => 'school_info']],
        ],
    ];
    
    $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
}
```

---

## 🧪 TESTING

### Manual Testing

```bash
# Bot testing
# 1. Telegram da botga /start yuboring
# 2. Har bir menyuni test qiling
# 3. Xato holatlarni test qiling
```

### Unit Testing

```bash
php artisan test
```

### Feature Testing

```php
// tests/Feature/VacancyTest.php
public function test_user_can_apply_for_vacancy()
{
    $vacancy = Vacancy::factory()->create();
    
    $response = $this->post(route('vacancy.apply', $vacancy), [
        'full_name' => 'Test User',
        'phone' => '+998901234567',
        // ...
    ]);
    
    $response->assertStatus(200);
    $this->assertDatabaseHas('vacancy_applications', [
        'vacancy_id' => $vacancy->id,
        'full_name' => 'Test User',
    ]);
}
```

---

## 🐛 DEBUGGING

### Bot Debugging

```php
// Log qo'shish
Log::info('telegram.webhook.received', [
    'update' => $update->toArray(),
]);
```

### Database Queries

```php
// Query log
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());
```

### Telegram API Errors

```php
try {
    $this->telegramBotService->sendMessage(...);
} catch (TelegramSDKException $e) {
    Log::error('telegram.send.failed', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
```

---

## 📝 CODING STANDARDS

### PHP

```php
// ✅ Good
public function handleVacancyApplication(VacancyApplication $application): void
{
    $this->notificationService->send($application);
}

// ❌ Bad
public function handle($app) {
    $this->send($app);
}
```

### Naming Conventions

- **Models:** Singular, PascalCase (`Vacancy`, `SchoolBot`)
- **Controllers:** PascalCase + Controller (`VacancyController`)
- **Methods:** camelCase (`handleCallback`, `sendMessage`)
- **Variables:** camelCase (`$telegramUser`, `$schoolBot`)
- **Database tables:** Plural, snake_case (`vacancies`, `school_bots`)
- **Database columns:** snake_case (`full_name`, `created_at`)

### Comments

```php
/**
 * Handle vacancy application submission
 *
 * @param BotSession $session
 * @param TelegramUser $user
 * @param array $data
 * @param SchoolBot $schoolBot
 * @return bool
 */
private function finishVacancyApplication(
    BotSession $session,
    TelegramUser $user,
    array $data,
    SchoolBot $schoolBot
): bool {
    // Implementation
}
```

---

## 🔐 SECURITY

### Input Validation

```php
// ✅ Always validate
$validated = $request->validate([
    'full_name' => 'required|string|max:255',
    'phone' => 'required|regex:/^\+998\d{9}$/',
    'email' => 'nullable|email',
]);
```

### File Upload

```php
// ✅ Validate file uploads
$request->validate([
    'cv' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB
    'photo' => 'nullable|image|max:5120', // 5MB
]);
```

### SQL Injection Prevention

```php
// ✅ Use Eloquent or Query Builder
User::where('email', $email)->first();

// ❌ Never use raw queries with user input
DB::select("SELECT * FROM users WHERE email = '$email'");
```

---

## 🚀 DEPLOYMENT

### Production Checklist

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Strong `APP_KEY`
- [ ] Database credentials
- [ ] Telegram bot token
- [ ] Webhook URL
- [ ] Queue worker running
- [ ] Cron jobs configured
- [ ] SSL certificate
- [ ] Backup configured

### Deployment Commands

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
php artisan queue:restart
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

---

## 📚 RESOURCES

### Documentation
- [Laravel 13](https://laravel.com/docs/13.x)
- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Telegram Bot SDK](https://telegram-bot-sdk.readme.io/)

### Tools
- [Postman](https://www.postman.com/) - API testing
- [TablePlus](https://tableplus.com/) - Database GUI
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) - Debugging

---

## 💬 SUPPORT

### Issues
- GitHub Issues: [Link]
- Telegram: @your_username

### Contributing
1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

---

**Happy Coding! 🚀**
