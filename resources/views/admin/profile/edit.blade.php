@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => 'Profil sozlamalari',
                'subtitle' => 'Shaxsiy ma`lumotlar va parolni o`zgartirish.',
            ])
            @include('admin.partials.flash')

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Shaxsiy ma'lumotlar</h4>
                            <form method="post" action="{{ route('admin.profile.update') }}">
                                @csrf
                                @method('put')

                                <div class="mb-3">
                                    <label class="form-label">Ism <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <input type="text" class="form-control" value="{{ $user->role->value === 'super_admin' ? 'Super Admin' : 'Maktab Admini' }}" disabled>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Parolni o'zgartirish</h5>
                                <p class="text-muted mb-3">Parolni o'zgartirish ixtiyoriy. Agar o'zgartirmoqchi bo'lmasangiz, quyidagi maydonlarni bo'sh qoldiring.</p>

                                <div class="mb-3">
                                    <label class="form-label">Joriy parol</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" placeholder="Joriy parolingizni kiriting">
                                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Yangi parol</label>
                                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" placeholder="Kamida 6 ta belgi">
                                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Yangi parolni tasdiqlash</label>
                                            <input type="password" class="form-control" name="new_password_confirmation" placeholder="Parolni qayta kiriting">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="bx bx-save font-size-16 align-middle me-2"></i> Saqlash
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Hisob ma'lumotlari</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="ps-0" scope="row">Ism:</th>
                                            <td class="text-muted">{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Email:</th>
                                            <td class="text-muted">{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Rol:</th>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $user->role->value === 'super_admin' ? 'Super Admin' : 'Maktab Admini' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Holat:</th>
                                            <td>
                                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $user->is_active ? 'Faol' : 'Nofaol' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($user->last_login_at)
                                        <tr>
                                            <th class="ps-0" scope="row">Oxirgi kirish:</th>
                                            <td class="text-muted">{{ $user->last_login_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div></div></div>
    </div>
@endsection
