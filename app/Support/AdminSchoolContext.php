<?php

namespace App\Support;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AdminSchoolContext
{
    public function schools(User $user): Collection
    {
        return $user->isSuperAdmin()
            ? School::query()->orderBy('name')->get()
            : $user->schools()->orderBy('name')->get();
    }

    public function current(User $user): ?School
    {
        $schools = $this->schools($user);

        if ($schools->isEmpty()) {
            return null;
        }

        if ($user->isSuperAdmin()) {
            $requestedSchoolId = request('school_id') ?: session('admin_school_id');

            if ($requestedSchoolId && $schools->contains('id', (int) $requestedSchoolId)) {
                session(['admin_school_id' => (int) $requestedSchoolId]);

                return $schools->firstWhere('id', (int) $requestedSchoolId);
            }

            session(['admin_school_id' => $schools->first()->id]);
        }

        return $schools->first();
    }

    public function authorizeModel(User $user, Model $model, string $schoolColumn = 'school_id'): void
    {
        if ($user->isSuperAdmin()) {
            return;
        }

        $schoolIds = $this->schools($user)->pluck('id');

        abort_unless($schoolIds->contains((int) data_get($model, $schoolColumn)), 403);
    }
}
