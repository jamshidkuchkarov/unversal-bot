<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OlympiadRequest;
use App\Models\Olympiad;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OlympiadController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(): View
    {
        $school = $this->schoolContext->current(request()->user());
        $filters = [
            'search' => trim((string) request('search', '')),
            'status' => (string) request('status', ''),
            'academic_year' => (string) request('academic_year', ''),
        ];

        $olympiadsQuery = Olympiad::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['search'], function ($query) use ($filters) {
                $query->where(function ($subQuery) use ($filters) {
                    $subQuery
                        ->where('title', 'like', "%{$filters['search']}%")
                        ->orWhere('olympiad_location', 'like', "%{$filters['search']}%")
                        ->orWhereJsonContains('subjects', $filters['search']);
                });
            })
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['academic_year'], fn ($query) => $query->whereYear('registration_start', $filters['academic_year']))
            ->latest();

        $availableYears = Olympiad::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->get(['registration_start'])
            ->pluck('registration_start')
            ->filter()
            ->map(fn ($date) => $date?->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('admin.olympiads.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'filters' => $filters,
            'availableYears' => $availableYears,
            'olympiads' => $olympiadsQuery->paginate(10)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.olympiads.form', ['olympiad' => new Olympiad()]);
    }

    public function store(OlympiadRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404);

        Olympiad::query()->create([
            ...$request->validated(),
            'school_id' => $school->id,
            'created_by' => $request->user()->id,
            'subjects' => $this->parseCsv($request->input('subjects')),
            'target_classes' => $this->parseCsv($request->input('target_classes')),
            'is_free' => $request->boolean('is_free', true),
        ]);

        return redirect()->route('admin.olympiads.index')->with('status', 'Olimpiada yaratildi.');
    }

    public function edit(Olympiad $olympiad): View
    {
        $this->schoolContext->authorizeModel(request()->user(), $olympiad);

        return view('admin.olympiads.form', compact('olympiad'));
    }

    public function update(OlympiadRequest $request, Olympiad $olympiad): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $olympiad);

        $olympiad->update([
            ...$request->validated(),
            'subjects' => $this->parseCsv($request->input('subjects')),
            'target_classes' => $this->parseCsv($request->input('target_classes')),
            'is_free' => $request->boolean('is_free', true),
        ]);

        return redirect()->route('admin.olympiads.index')->with('status', 'Olimpiada yangilandi.');
    }

    public function destroy(Olympiad $olympiad): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $olympiad);

        $olympiad->delete();

        return back()->with('status', 'Olimpiada o`chirildi.');
    }

    private function parseCsv(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
