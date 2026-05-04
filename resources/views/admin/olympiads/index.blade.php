@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Olimpiadalar',
                        'subtitle' => 'Sarlavha, vaqt va registration linklarni boshqarish.',
                        'action' => '<a href="'.route('admin.olympiads.create').'" class="btn btn-primary waves-effect waves-light">Olimpiada qo`shish</a>',
                    ])
                    @include('admin.partials.flash')
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="get" action="{{ route('admin.olympiads.index') }}" class="row g-3 align-items-end">
                                <input type="hidden" name="school_id" value="{{ $currentSchool?->id }}">
                                <div class="col-md-4">
                                    <label class="form-label">Qidiruv</label>
                                    <input class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Sarlavha, fan yoki joylashuv">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">Barchasi</option>
                                        <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
                                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option>
                                        <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
                                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
                                        <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Yil</label>
                                    <select class="form-select" name="academic_year">
                                        <option value="">Barchasi</option>
                                        @foreach ($availableYears as $year)
                                            <option value="{{ $year }}" @selected((string) ($filters['academic_year'] ?? '') === (string) $year)>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button class="btn btn-primary w-100" type="submit">Filtrlash</button>
                                    <a href="{{ route('admin.olympiads.index', array_filter(['school_id' => $currentSchool?->id])) }}" class="btn btn-light w-100">Tozalash</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr><th>Sarlavha</th><th>Sinflar</th><th>Registration</th><th>Status</th><th class="text-end">Amallar</th></tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($olympiads as $olympiad)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $olympiad->title }}</div>
                                                <div class="text-muted small">{{ $olympiad->subject ?: 'Fan ko`rsatilmagan' }}</div>
                                            </td>
                                            <td>{{ implode(', ', $olympiad->target_classes ?? []) ?: '-' }}</td>
                                            <td>{{ $olympiad->registration_start?->format('d.m.Y H:i') }} - {{ $olympiad->registration_end?->format('d.m.Y H:i') }}</td>
                                            <td><span class="badge bg-info">{{ $olympiad->status }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.olympiad-registrations.index', array_filter(['school_id' => $currentSchool?->id, 'olympiad_id' => $olympiad->id])) }}" class="btn btn-sm btn-primary">Arizalar</a>
                                                <a href="{{ route('admin.olympiads.edit', $olympiad) }}" class="btn btn-sm btn-light">Tahrirlash</a>
                                                <form class="d-inline" method="post" action="{{ route('admin.olympiads.destroy', $olympiad) }}">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">O`chirish</button></form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">Olimpiadalar yo`q.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $olympiads->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
