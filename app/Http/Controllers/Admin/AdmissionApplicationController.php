<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Support\AdminSchoolContext;
use App\Exports\AdmissionApplicationsExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdmissionApplicationController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(Request $request): View
    {
        $school = $this->schoolContext->current($request->user());

        $filters = [
            'search' => $request->input('search', ''),
            'admission_id' => $request->input('admission_id', ''),
            'status' => $request->input('status', ''),
        ];

        $applications = AdmissionApplication::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['search'], fn ($query) => $query->where(function ($q) use ($filters) {
                $q->where('student_full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('parent_full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('parent_phone', 'like', "%{$filters['search']}%")
                  ->orWhere('previous_school', 'like', "%{$filters['search']}%")
                  ->orWhere('transition_reason', 'like', "%{$filters['search']}%");
            }))
            ->when($filters['admission_id'], fn ($query) => $query->where('admission_id', $filters['admission_id']))
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->with(['admission'])
            ->latest()
            ->paginate(20);

        $admissions = Admission::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->get();

        return view('admin.admission-applications.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools($request->user()),
            'applications' => $applications,
            'admissions' => $admissions,
            'filters' => $filters,
        ]);
    }

    public function update(Request $request, AdmissionApplication $application): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $application);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,accepted,rejected,waitlist',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            ...$validated,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Ariza yangilandi.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $school = $this->schoolContext->current($request->user());

        $filters = [
            'search' => $request->input('search', ''),
            'admission_id' => $request->input('admission_id', ''),
            'status' => $request->input('status', ''),
        ];

        $query = AdmissionApplication::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($filters['search'], fn ($query) => $query->where(function ($q) use ($filters) {
                $q->where('student_full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('parent_full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('parent_phone', 'like', "%{$filters['search']}%")
                  ->orWhere('previous_school', 'like', "%{$filters['search']}%")
                  ->orWhere('transition_reason', 'like', "%{$filters['search']}%");
            }))
            ->when($filters['admission_id'], fn ($query) => $query->where('admission_id', $filters['admission_id']))
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->latest();

        return Excel::download(
            new AdmissionApplicationsExport($query),
            'qabul-arizalari-' . date('Y-m-d') . '.xlsx'
        );
    }
}
