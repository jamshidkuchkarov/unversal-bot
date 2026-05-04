<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use App\Support\AdminSchoolContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TelegramUserController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(Request $request): View
    {
        $school = $this->schoolContext->current($request->user());

        $filters = [
            'search' => $request->input('search', ''),
            'is_subscribed' => $request->input('is_subscribed', ''),
            'is_active' => $request->input('is_active', ''),
        ];

        $users = TelegramUser::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['search'], fn ($query) => $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['search']}%")
                  ->orWhere('last_name', 'like', "%{$filters['search']}%")
                  ->orWhere('username', 'like', "%{$filters['search']}%")
                  ->orWhere('telegram_id', 'like', "%{$filters['search']}%");
            }))
            ->when($filters['is_subscribed'] !== '', fn ($query) => $query->where('is_subscribed', (bool) $filters['is_subscribed']))
            ->when($filters['is_active'] !== '', fn ($query) => $query->where('is_active', (bool) $filters['is_active']))
            ->latest('last_seen_at')
            ->paginate(50);

        $stats = [
            'total' => TelegramUser::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->count(),
            'subscribed' => TelegramUser::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->where('is_subscribed', true)->count(),
            'active' => TelegramUser::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->where('is_active', true)->count(),
            'today' => TelegramUser::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->whereDate('created_at', today())->count(),
        ];

        return view('admin.telegram-users.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools($request->user()),
            'users' => $users,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, TelegramUser $telegramUser): View
    {
        $this->schoolContext->authorizeModel($request->user(), $telegramUser);

        $telegramUser->load(['preference']);

        // Get user's applications
        $vacancyApplications = $telegramUser->vacancyApplications()->with('vacancy')->latest()->get();
        $olympiadRegistrations = $telegramUser->olympiadRegistrations()->with('olympiad')->latest()->get();
        $admissionApplications = $telegramUser->admissionApplications()->with('admission')->latest()->get();

        return view('admin.telegram-users.show', [
            'currentSchool' => $this->schoolContext->current($request->user()),
            'availableSchools' => $this->schoolContext->schools($request->user()),
            'user' => $telegramUser,
            'vacancyApplications' => $vacancyApplications,
            'olympiadRegistrations' => $olympiadRegistrations,
            'admissionApplications' => $admissionApplications,
        ]);
    }
}
