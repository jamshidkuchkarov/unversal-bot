@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => $school->exists ? 'Maktabni tahrirlash' : 'Maktab yaratish',
                'subtitle' => 'Multi-school struktura uchun asosiy obyekt.',
            ])
            @include('admin.partials.flash')
            <div class="card"><div class="card-body">
                <form method="post" action="{{ $school->exists ? route('admin.schools.update', $school) : route('admin.schools.store') }}">
                    @csrf @if($school->exists) @method('put') @endif
                    <div class="row">
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Nomi</label><input class="form-control" name="name" value="{{ old('name', $school->name) }}" required></div></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $school->slug) }}" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="mb-3"><label class="form-label">Shahar</label><input class="form-control" name="city" value="{{ old('city', $school->city) }}"></div></div>
                        <div class="col-md-4"><div class="mb-3"><label class="form-label">Tuman</label><input class="form-control" name="district" value="{{ old('district', $school->district) }}"></div></div>
                        <div class="col-md-4"><div class="mb-3"><label class="form-label">Direktor</label><input class="form-control" name="director_name" value="{{ old('director_name', $school->director_name) }}"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Telefon</label><input class="form-control" name="phone" value="{{ old('phone', $school->phone) }}"></div></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" value="{{ old('email', $school->email) }}"></div></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Manzil</label><textarea class="form-control" name="address" rows="4">{{ old('address', $school->address) }}</textarea></div>
                    <div class="mb-3"><label class="form-label">Holat</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active', $school->is_active) == 1)>Faol</option><option value="0" @selected(old('is_active', $school->is_active) == 0)>Nofaol</option></select></div>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Saqlash</button>
                </form>
            </div></div>
        </div></div></div>
    </div>
@endsection
