@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => ($admin ? 'Adminni tahrirlash' : 'Admin yaratish') . ' - ' . $school->name,
                'subtitle' => 'Maktab admini boshqarish.',
            ])
            @include('admin.partials.flash')
            <div class="card"><div class="card-body">
                <form method="post" action="{{ $admin ? route('admin.schools.admins.update', [$school, $admin]) : route('admin.schools.admins.store', $school) }}">
                    @csrf
                    @if($admin) @method('put') @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ism <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $admin?->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $admin?->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Parol
                                    @if($admin)
                                        <small class="text-muted">(bo`sh qoldiring, o`zgartirmaslik uchun)</small>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Kamida 6 ta belgi" {{ $admin ? '' : 'required' }}>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Holat</label>
                                <select class="form-select" name="is_active">
                                    <option value="1" @selected(old('is_active', $admin?->is_active ?? true) == 1)>Faol</option>
                                    <option value="0" @selected(old('is_active', $admin?->is_active ?? true) == 0)>Nofaol</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ruxsatlar</label>
                        <div class="row">
                            @php
                                $availablePermissions = [
                                    'olympiad' => 'Olimpiadalar',
                                    'vacancy' => 'Vakansiyalar',
                                    'admission' => 'Qabul',
                                    'announcement' => 'E`lonlar',
                                    'channel' => 'Kanallar',
                                    'settings' => 'Sozlamalar',
                                ];
                                $pivotPermissions = $admin?->schools()->where('school_id', $school->id)->first()?->pivot->permissions;
                                if (is_string($pivotPermissions)) {
                                    $pivotPermissions = json_decode($pivotPermissions, true);
                                }
                                $currentPermissions = old('permissions', $pivotPermissions ?? []);
                            @endphp
                            @foreach($availablePermissions as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}" @checked(in_array($key, $currentPermissions))>
                                        <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Saqlash</button>
                        <a href="{{ route('admin.schools.admins.index', $school) }}" class="btn btn-light waves-effect">Bekor qilish</a>
                    </div>
                </form>
            </div></div>
        </div></div></div>
    </div>
@endsection
