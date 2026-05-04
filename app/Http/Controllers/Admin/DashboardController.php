<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\Announcement;
use App\Models\Channel;
use App\Models\Olympiad;
use App\Models\OlympiadRegistration;
use App\Models\School;
use App\Models\TelegramUser;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Support\AdminSchoolContext;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AdminSchoolContext $schoolContext): View
    {
        $currentSchool = $schoolContext->current(request()->user());

        // Basic stats
        $stats = [
            'schools' => School::query()->count(),
            'users' => TelegramUser::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->count(),
            'users_today' => TelegramUser::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->whereDate('created_at', today())->count(),
            'users_subscribed' => TelegramUser::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->where('is_subscribed', true)->count(),
            'vacancies' => Vacancy::query()->when($currentSchool, fn ($query) => $query->where('school_id', $currentSchool->id))->where('status', 'published')->count(),
            'vacancy_applications' => VacancyApplication::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->count(),
            'vacancy_applications_pending' => VacancyApplication::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->where('status', 'pending')->count(),
            'olympiads' => Olympiad::query()->when($currentSchool, fn ($query) => $query->where('school_id', $currentSchool->id))->where('status', 'published')->count(),
            'olympiad_registrations' => OlympiadRegistration::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->count(),
            'admissions' => Admission::query()->when($currentSchool, fn ($query) => $query->where('school_id', $currentSchool->id))->where('status', 'published')->count(),
            'admission_applications' => AdmissionApplication::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->count(),
            'admission_applications_pending' => AdmissionApplication::query()->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))->where('status', 'pending')->count(),
            'announcements' => Announcement::query()->when($currentSchool, fn ($query) => $query->where('school_id', $currentSchool->id))->count(),
            'required_channels' => Channel::query()->where('is_required', true)->where('is_active', true)->count(),
        ];

        // Users growth chart (last 30 days)
        $usersGrowth = TelegramUser::query()
            ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Applications chart (last 30 days)
        $applicationsData = [
            'vacancy' => VacancyApplication::query()
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray(),
            'olympiad' => OlympiadRegistration::query()
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray(),
            'admission' => AdmissionApplication::query()
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray(),
        ];

        // Recent applications
        $recentApplications = [
            'vacancy' => VacancyApplication::query()
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->with('vacancy')
                ->latest()
                ->limit(5)
                ->get(),
            'admission' => AdmissionApplication::query()
                ->when($currentSchool, fn ($q) => $q->where('school_id', $currentSchool->id))
                ->with('admission')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard.index', [
            'currentSchool' => $currentSchool,
            'availableSchools' => $schoolContext->schools(request()->user()),
            'stats' => $stats,
            'usersGrowth' => $usersGrowth,
            'applicationsData' => $applicationsData,
            'recentApplications' => $recentApplications,
        ]);
    }
}
