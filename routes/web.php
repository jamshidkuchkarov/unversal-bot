<?php

use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OlympiadController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SchoolInfoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VacancyController;
use App\Http\Controllers\Admin\AdmissionApplicationController;
use App\Http\Controllers\Admin\TelegramUserController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Telegram\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/telegram/webhook/{schoolBot}', TelegramWebhookController::class)->name('telegram.webhook');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/school-info', [SchoolInfoController::class, 'edit'])->name('school-info.edit');
    Route::put('/school-info', [SchoolInfoController::class, 'update'])->name('school-info.update');
    Route::delete('/school-info/gallery', [SchoolInfoController::class, 'deleteGalleryImage'])->name('school-info.gallery.delete');
    Route::resource('schools', SchoolController::class)->except('show');
    Route::resource('schools.admins', \App\Http\Controllers\Admin\SchoolAdminController::class)->except('show');
    Route::resource('vacancies', VacancyController::class)->except('show');
    Route::get('/vacancy-applications', [\App\Http\Controllers\Admin\VacancyApplicationController::class, 'index'])->name('vacancy-applications.index');
    Route::get('/vacancy-applications/export', [\App\Http\Controllers\Admin\VacancyApplicationController::class, 'export'])->name('vacancy-applications.export');
    Route::put('/vacancy-applications/{vacancyApplication}', [\App\Http\Controllers\Admin\VacancyApplicationController::class, 'update'])->name('vacancy-applications.update');
    Route::resource('olympiads', OlympiadController::class)->except('show');
    Route::get('/olympiad-registrations', [\App\Http\Controllers\Admin\OlympiadRegistrationController::class, 'index'])->name('olympiad-registrations.index');
    Route::get('/olympiad-registrations/export', [\App\Http\Controllers\Admin\OlympiadRegistrationController::class, 'export'])->name('olympiad-registrations.export');
    Route::put('/olympiad-registrations/{olympiadRegistration}', [\App\Http\Controllers\Admin\OlympiadRegistrationController::class, 'update'])->name('olympiad-registrations.update');
    Route::resource('admissions', AdmissionController::class)->except('show');
    Route::get('/admission-applications', [AdmissionApplicationController::class, 'index'])->name('admission-applications.index');
    Route::get('/admission-applications/export', [AdmissionApplicationController::class, 'export'])->name('admission-applications.export');
    Route::put('/admission-applications/{admissionApplication}', [AdmissionApplicationController::class, 'update'])->name('admission-applications.update');
    Route::resource('announcements', AnnouncementController::class)->except('show');
    Route::post('/announcements/{announcement}/send', [AnnouncementController::class, 'send'])->name('announcements.send');
    Route::post('/announcements/{announcement}/send-test', [AnnouncementController::class, 'sendTest'])->name('announcements.send-test');
    Route::resource('channels', ChannelController::class)->except('show');
    Route::get('/channels/get-chat-id', [ChannelController::class, 'getChatId'])->name('channels.get-chat-id');
    Route::get('/telegram-users', [TelegramUserController::class, 'index'])->name('telegram-users.index');
    Route::get('/telegram-users/{telegramUser}', [TelegramUserController::class, 'show'])->name('telegram-users.show');
    Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
});
