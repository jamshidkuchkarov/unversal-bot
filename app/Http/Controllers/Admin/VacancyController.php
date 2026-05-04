<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VacancyRequest;
use App\Models\Vacancy;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VacancyController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(): View
    {
        $school = $this->schoolContext->current(request()->user());

        return view('admin.vacancies.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'vacancies' => Vacancy::query()
                ->when($school, fn ($query) => $query->where('school_id', $school->id))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.vacancies.form', ['vacancy' => new Vacancy()]);
    }

    public function store(VacancyRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404);

        Vacancy::query()->create([
            ...$request->validated(),
            'school_id' => $school->id,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.vacancies.index')->with('status', 'Vakansiya yaratildi.');
    }

    public function edit(Vacancy $vacancy): View
    {
        $this->schoolContext->authorizeModel(request()->user(), $vacancy);

        return view('admin.vacancies.form', compact('vacancy'));
    }

    public function update(VacancyRequest $request, Vacancy $vacancy): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $vacancy);

        $vacancy->update($request->validated());

        return redirect()->route('admin.vacancies.index')->with('status', 'Vakansiya yangilandi.');
    }

    public function destroy(Vacancy $vacancy): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $vacancy);

        $vacancy->delete();

        return back()->with('status', 'Vakansiya o`chirildi.');
    }
}
