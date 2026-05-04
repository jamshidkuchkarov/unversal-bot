<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VacancyApplicationStatusRequest;
use App\Models\VacancyApplication;
use App\Support\AdminSchoolContext;
use App\Exports\VacancyApplicationsExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VacancyApplicationController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(): View
    {
        $school = $this->schoolContext->current(request()->user());
        $status = request('status');
        $vacancyId = request('vacancy_id');
        $search = trim((string) request('search'));

        return view('admin.vacancy-applications.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'filters' => [
                'status' => $status,
                'vacancy_id' => $vacancyId,
                'search' => $search,
            ],
            'vacancies' => $school
                ? $school->vacancies()->orderBy('title')->get(['id', 'title'])
                : collect(),
            'applications' => VacancyApplication::query()
                ->with(['vacancy:id,title,subject', 'reviewer:id,name'])
                ->when($school, fn ($query) => $query->where('school_id', $school->id))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->when($vacancyId, function ($query) use ($vacancyId) {
                    if ($vacancyId === 'reserve') {
                        $query->where('application_type', 'reserve');

                        return;
                    }

                    $query->where('vacancy_id', $vacancyId);
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search): void {
                        $subQuery
                            ->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('telegram_contact', 'like', '%'.$search.'%')
                            ->orWhere('experience', 'like', '%'.$search.'%')
                            ->orWhere('skills', 'like', '%'.$search.'%');
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function update(VacancyApplicationStatusRequest $request, VacancyApplication $vacancyApplication): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $vacancyApplication);

        $vacancyApplication->update([
            'status' => $request->validated('status'),
            'admin_notes' => $request->validated('admin_notes'),
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Vakansiya arizasi statusi yangilandi.');
    }

    public function export(): BinaryFileResponse
    {
        $school = $this->schoolContext->current(request()->user());
        $status = request('status');
        $vacancyId = request('vacancy_id');
        $search = trim((string) request('search'));

        $query = VacancyApplication::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($vacancyId, function ($query) use ($vacancyId) {
                if ($vacancyId === 'reserve') {
                    $query->where('application_type', 'reserve');

                    return;
                }

                $query->where('vacancy_id', $vacancyId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('telegram_contact', 'like', '%'.$search.'%')
                        ->orWhere('experience', 'like', '%'.$search.'%')
                        ->orWhere('skills', 'like', '%'.$search.'%');
                });
            })
            ->latest();

        return Excel::download(
            new VacancyApplicationsExport($query),
            'vakansiya-arizalari-' . date('Y-m-d') . '.xlsx'
        );
    }
}
