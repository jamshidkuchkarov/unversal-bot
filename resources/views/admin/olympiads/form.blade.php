@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => $olympiad->exists ? 'Olimpiadani tahrirlash' : 'Olimpiada yaratish',
                        'subtitle' => 'Multi-school olympiad strukturasi bo`yicha form.',
                    ])
                    @include('admin.partials.flash')
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ $olympiad->exists ? route('admin.olympiads.update', $olympiad) : route('admin.olympiads.store') }}">
                                @csrf
                                @if($olympiad->exists) @method('put') @endif
                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Sarlavha</label><input class="form-control" name="title" value="{{ old('title', $olympiad->title) }}" required></div></div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fanlar</label>
                                            <input
                                                class="form-control @error('subjects') is-invalid @enderror"
                                                name="subjects"
                                                value="{{ old('subjects', implode(', ', $olympiad->subjects ?? [])) }}"
                                                placeholder="Masalan: Matematika, Fizika, Kimyo"
                                            >
                                            <small class="text-muted">Fanlarni vergul bilan ajrating. Masalan: Matematika, Fizika, Kimyo</small>
                                            @error('subjects')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3"><label class="form-label">Tavsif</label><textarea class="form-control" name="description" rows="6">{{ old('description', $olympiad->description) }}</textarea></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Sinflar</label>
                                            <input
                                                class="form-control @error('target_classes') is-invalid @enderror"
                                                name="target_classes"
                                                value="{{ old('target_classes', implode(', ', $olympiad->target_classes ?? [])) }}"
                                                placeholder="Masalan: 5, 6, 7"
                                                required
                                            >
                                            <small class="text-muted">Faqat raqamlarni vergul bilan ajrating. Masalan: 5, 6, 7</small>
                                            @error('target_classes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Min yosh</label><input class="form-control" type="number" name="min_age" value="{{ old('min_age', $olympiad->min_age) }}"></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Max yosh</label><input class="form-control" type="number" name="max_age" value="{{ old('max_age', $olympiad->max_age) }}"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Registration start</label><input class="form-control" type="datetime-local" name="registration_start" value="{{ old('registration_start', $olympiad->registration_start?->format('Y-m-d\\TH:i')) }}" required></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Registration end</label><input class="form-control" type="datetime-local" name="registration_end" value="{{ old('registration_end', $olympiad->registration_end?->format('Y-m-d\\TH:i')) }}" required></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Olimpiada sanasi</label><input class="form-control" type="date" name="olympiad_date" value="{{ old('olympiad_date', $olympiad->olympiad_date?->format('Y-m-d')) }}"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Joylashuv</label><input class="form-control" name="olympiad_location" value="{{ old('olympiad_location', $olympiad->olympiad_location) }}"></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Max ishtirokchi</label><input class="form-control" type="number" name="max_participants" value="{{ old('max_participants', $olympiad->max_participants) }}"></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Holat</label><select class="form-select" name="status"><option value="draft" @selected(old('status', $olympiad->status) === 'draft')>draft</option><option value="published" @selected(old('status', $olympiad->status) === 'published')>published</option><option value="closed" @selected(old('status', $olympiad->status) === 'closed')>closed</option><option value="completed" @selected(old('status', $olympiad->status) === 'completed')>completed</option><option value="cancelled" @selected(old('status', $olympiad->status) === 'cancelled')>cancelled</option></select></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Bepulmi</label><select class="form-select" name="is_free"><option value="1" @selected(old('is_free', $olympiad->is_free) == 1)>Ha</option><option value="0" @selected(old('is_free', $olympiad->is_free) == 0)>Yo`q</option></select></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Narx</label><input class="form-control" type="number" step="0.01" name="price" value="{{ old('price', $olympiad->price) }}"></div></div>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Saqlash</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
