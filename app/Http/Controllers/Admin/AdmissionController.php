<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdmissionRequest;
use App\Models\Admission;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(Request $request): View
    {
        $school = $this->schoolContext->current($request->user());

        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'academic_year' => $request->input('academic_year', ''),
        ];

        $admissions = Admission::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['search'], fn ($query) => $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            }))
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['academic_year'], fn ($query) => $query->where('academic_year', $filters['academic_year']))
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Admission::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->count(),
            'published' => Admission::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->where('status', 'published')->count(),
            'draft' => Admission::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->where('status', 'draft')->count(),
            'closed' => Admission::query()->when($school, fn ($q) => $q->where('school_id', $school->id))->where('status', 'closed')->count(),
        ];

        return view('admin.admissions.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools($request->user()),
            'admissions' => $admissions,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        return view('admin.admissions.form', ['admission' => new Admission()]);
    }

    public function store(AdmissionRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404);

        Admission::query()->create([
            ...$request->validated(),
            'school_id' => $school->id,
            'created_by' => $request->user()->id,
            'target_classes' => $this->parseCsv($request->input('target_classes')),
            'admission_options' => $request->input('admission_options', []), // Array oladi
            'required_documents' => $this->parseCsv($request->input('required_documents')),
        ]);

        return redirect()->route('admin.admissions.index')->with('status', 'Qabul kampaniyasi yaratildi.');
    }

    public function edit(Admission $admission): View
    {
        $this->schoolContext->authorizeModel(request()->user(), $admission);

        return view('admin.admissions.form', compact('admission'));
    }

    public function update(AdmissionRequest $request, Admission $admission): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $admission);

        $admission->update([
            ...$request->validated(),
            'target_classes' => $this->parseCsv($request->input('target_classes')),
            'admission_options' => $request->input('admission_options', []), // Array oladi
            'required_documents' => $this->parseCsv($request->input('required_documents')),
        ]);

        return redirect()->route('admin.admissions.index')->with('status', 'Qabul kampaniyasi yangilandi.');
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $admission);

        $admission->delete();

        return back()->with('status', 'Qabul kampaniyasi o`chirildi.');
    }

    private function parseCsv(?string $value): array
    {
        return collect(explode(',', (string) $value))->map(fn (string $item) => trim($item))->filter()->values()->all();
    }
}
