<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OlympiadRegistrationsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OlympiadRegistrationStatusRequest;
use App\Models\Olympiad;
use App\Models\OlympiadRegistration;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OlympiadRegistrationController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(Request $request): View
    {
        $school = $this->schoolContext->current($request->user());
        $filters = [
            'olympiad_id' => $request->input('olympiad_id', ''),
            'status' => $request->input('status', ''),
            'search' => trim((string) $request->input('search', '')),
        ];

        $olympiads = Olympiad::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->withCount('registrations')
            ->latest()
            ->get();

        $activeOlympiads = $olympiads
            ->filter(function (Olympiad $olympiad): bool {
                return $olympiad->status === 'published'
                    && (! $olympiad->registration_end || $olympiad->registration_end->isFuture());
            })
            ->values();

        $selectedOlympiad = $filters['olympiad_id']
            ? $olympiads->firstWhere('id', (int) $filters['olympiad_id'])
            : null;

        $registrationsQuery = OlympiadRegistration::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['olympiad_id'], fn ($query) => $query->where('olympiad_id', $filters['olympiad_id']))
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['search'], function ($query) use ($filters) {
                $query->where(function ($subQuery) use ($filters) {
                    $subQuery
                        ->where('full_name', 'like', "%{$filters['search']}%")
                        ->orWhere('phone', 'like', "%{$filters['search']}%")
                        ->orWhere('district', 'like', "%{$filters['search']}%")
                        ->orWhere('school_name_custom', 'like', "%{$filters['search']}%");
                });
            })
            ->with(['olympiad'])
            ->latest();

        $registrations = $registrationsQuery->paginate(20)->withQueryString();
        $statusCounts = (clone $registrationsQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total' => (clone $registrationsQuery)->count(),
            'confirmed' => (int) ($statusCounts['confirmed'] ?? 0),
            'participated' => (int) ($statusCounts['participated'] ?? 0),
            'absent' => (int) ($statusCounts['absent'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];

        return view('admin.olympiad-registrations.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools($request->user()),
            'activeOlympiads' => $activeOlympiads,
            'olympiads' => $olympiads,
            'selectedOlympiad' => $selectedOlympiad,
            'filters' => $filters,
            'registrations' => $registrations,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $school = $this->schoolContext->current($request->user());

        $query = OlympiadRegistration::query()
            ->when($school, fn ($builder) => $builder->where('school_id', $school->id))
            ->when($request->input('olympiad_id'), fn ($builder, $olympiadId) => $builder->where('olympiad_id', $olympiadId))
            ->when($request->input('status'), fn ($builder, $status) => $builder->where('status', $status))
            ->when(trim((string) $request->input('search', '')), function ($builder) use ($request) {
                $search = trim((string) $request->input('search', ''));

                $builder->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('school_name_custom', 'like', "%{$search}%");
                });
            })
            ->latest();

        return Excel::download(
            new OlympiadRegistrationsExport($query),
            'olympiada-registrations-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function update(OlympiadRegistrationStatusRequest $request, OlympiadRegistration $olympiadRegistration): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $olympiadRegistration);

        $olympiadRegistration->update([
            'status' => $request->validated('status'),
            'notes' => $request->validated('notes'),
        ]);

        return back()->with('status', 'Olimpiada ro`yxati statusi yangilandi.');
    }
}
