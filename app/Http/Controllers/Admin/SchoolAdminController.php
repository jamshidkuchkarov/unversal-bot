<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchoolAdminRequest;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolAdminController extends Controller
{
    public function index(School $school): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        $admins = $school->admins()->withPivot('id', 'permissions')->paginate(10);

        return view('admin.schools.admins.index', compact('school', 'admins'));
    }

    public function create(School $school): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        return view('admin.schools.admins.form', [
            'school' => $school,
            'admin' => null,
        ]);
    }

    public function store(SchoolAdminRequest $request, School $school): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $user = User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => UserRole::SchoolAdmin,
            'is_active' => $request->boolean('is_active', true),
        ]);

        SchoolAdmin::query()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'permissions' => [],
        ]);

        return redirect()->route('admin.schools.admins.index', $school)
            ->with('status', 'Admin yaratildi.');
    }

    public function edit(School $school, User $admin): View
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        return view('admin.schools.admins.form', compact('school', 'admin'));
    }

    public function update(SchoolAdminRequest $request, School $school, User $admin): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $admin->update($data);

        $schoolAdmin = SchoolAdmin::query()
            ->where('user_id', $admin->id)
            ->where('school_id', $school->id)
            ->first();

        if ($schoolAdmin) {
            $schoolAdmin->update([
                'permissions' => [],
            ]);
        }

        return redirect()->route('admin.schools.admins.index', $school)
            ->with('status', 'Admin yangilandi.');
    }

    public function destroy(School $school, User $admin): RedirectResponse
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);

        SchoolAdmin::query()
            ->where('user_id', $admin->id)
            ->where('school_id', $school->id)
            ->delete();

        if ($admin->schools()->count() === 0) {
            $admin->delete();
        }

        return back()->with('status', 'Admin o`chirildi.');
    }
}
