<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchoolRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        return view('admin.schools.index', [
            'schools' => School::query()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        return view('admin.schools.form', ['school' => new School()]);
    }

    public function store(SchoolRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $school = School::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $school->bot()->create([
            'bot_name' => $school->name,
            'main_menu_text' => 'Asosiy menyu',
            'welcome_text' => 'Assalomu alaykum. Xush kelibsiz.',
            'menu_buttons' => [
                ['label' => 'Olimpiadalar', 'action' => 'olympiads'],
                ['label' => 'Vakansiyalar', 'action' => 'vacancies'],
                ['label' => 'Qabul', 'action' => 'admissions'],
                ['label' => 'E`lonlar', 'action' => 'announcements'],
            ],
            'is_active' => true,
        ]);

        return redirect()->route('admin.schools.index')->with('status', 'Maktab yaratildi.');
    }

    public function edit(School $school): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        return view('admin.schools.form', compact('school'));
    }

    public function update(SchoolRequest $request, School $school): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $school->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.schools.index')->with('status', 'Maktab yangilandi.');
    }

    public function destroy(School $school): RedirectResponse
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        $school->delete();

        return back()->with('status', 'Maktab o`chirildi.');
    }
}
