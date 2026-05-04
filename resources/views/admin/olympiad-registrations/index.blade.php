@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Olimpiada arizalari',
                        'subtitle' => 'Avval olimpiadani tanlang, keyin ichidagi arizalarni boshqaring va Excel yuklab oling.',
                    ])
                    @include('admin.partials.flash')

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.olympiad-registrations.index', array_filter(['school_id' => $currentSchool?->id])) }}" class="btn {{ $selectedOlympiad ? 'btn-light' : 'btn-primary' }}">
                                    Barcha olimpiadalar
                                </a>
                                @foreach ($activeOlympiads as $olympiad)
                                    <a href="{{ route('admin.olympiad-registrations.index', array_filter(['school_id' => $currentSchool?->id, 'olympiad_id' => $olympiad->id])) }}"
                                       class="btn {{ (string) $filters['olympiad_id'] === (string) $olympiad->id ? 'btn-primary' : 'btn-light' }}">
                                        {{ $olympiad->title }}
                                        <span class="ms-1 badge bg-secondary">{{ $olympiad->registrations_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card h-100"><div class="card-body">
                                <div class="text-muted small">Jami arizalar</div>
                                <div class="fs-3 fw-semibold">{{ $summary['total'] }}</div>
                            </div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100"><div class="card-body">
                                <div class="text-muted small">Tasdiqlangan</div>
                                <div class="fs-3 fw-semibold text-success">{{ $summary['confirmed'] }}</div>
                            </div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100"><div class="card-body">
                                <div class="text-muted small">Qatnashgan</div>
                                <div class="fs-3 fw-semibold text-primary">{{ $summary['participated'] }}</div>
                            </div></div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100"><div class="card-body">
                                <div class="text-muted small">Kelmagan / bekor</div>
                                <div class="fs-3 fw-semibold text-danger">{{ $summary['absent'] + $summary['cancelled'] }}</div>
                            </div></div>
                        </div>
                    </div>

                    @if ($selectedOlympiad)
                        <div class="alert alert-info">
                            Ko`rilayotgan olimpiada: <strong>{{ $selectedOlympiad->title }}</strong>
                            @if($selectedOlympiad->subject)
                                • {{ $selectedOlympiad->subject }}
                            @endif
                            @if($selectedOlympiad->target_classes)
                                • Sinflar: {{ implode(', ', $selectedOlympiad->target_classes) }}-sinf
                            @endif
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="get" action="{{ route('admin.olympiad-registrations.index') }}" class="row g-3 align-items-end">
                                <input type="hidden" name="school_id" value="{{ $currentSchool?->id }}">
                                <div class="col-md-4">
                                    <label class="form-label">Qidiruv</label>
                                    <input class="form-control" name="search" value="{{ $filters['search'] }}" placeholder="FIO, telefon yoki maktab nomi">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Olimpiada</label>
                                    <select class="form-select" name="olympiad_id">
                                        <option value="">Barchasi</option>
                                        @foreach ($olympiads as $olympiad)
                                            <option value="{{ $olympiad->id }}" @selected((string) $filters['olympiad_id'] === (string) $olympiad->id)>{{ $olympiad->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">Barchasi</option>
                                        <option value="registered" @selected($filters['status'] === 'registered')>Yangi</option>
                                        <option value="confirmed" @selected($filters['status'] === 'confirmed')>Tasdiqlangan</option>
                                        <option value="participated" @selected($filters['status'] === 'participated')>Qatnashgan</option>
                                        <option value="absent" @selected($filters['status'] === 'absent')>Kelmagan</option>
                                        <option value="cancelled" @selected($filters['status'] === 'cancelled')>Bekor qilingan</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button class="btn btn-primary w-100" type="submit">Filtrlash</button>
                                    <a href="{{ route('admin.olympiad-registrations.export', array_filter(array_merge(request()->query(), ['school_id' => $currentSchool?->id]))) }}" class="btn btn-success w-100">Excel</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ishtirokchi</th>
                                            <th>Olimpiada</th>
                                            <th>Sinf</th>
                                            <th>Telefon</th>
                                            <th>Tuman / shahar</th>
                                            <th>Maktab</th>
                                            <th>Status</th>
                                            <th>Natija</th>
                                            <th>Izoh</th>
                                            <th class="text-end">Saqlash</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($registrations as $registration)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $registration->full_name }}</div>
                                                <div class="text-muted small">{{ $registration->created_at?->format('d.m.Y H:i') }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $registration->olympiad?->title ?? '-' }}</div>
                                                <div class="text-muted small">{{ $registration->olympiad?->subject ?? '' }}</div>
                                            </td>
                                            <td>{{ trim(collect([$registration->class_number, $registration->class_letter])->filter()->implode(' ')) ?: '-' }}</td>
                                            <td>{{ $registration->phone }}</td>
                                            <td>{{ $registration->district ?: '-' }}</td>
                                            <td>{{ $registration->school_name_custom ?: '-' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $registration->status }}</span>
                                            </td>
                                            <td>
                                                <div>Ball: {{ $registration->score ?? '-' }}</div>
                                                <div class="text-muted small">O`rin: {{ $registration->place ?? '-' }}</div>
                                            </td>
                                            <td>{{ $registration->notes ?: '-' }}</td>
                                            <td class="text-end" style="min-width: 340px;">
                                                <form method="post" action="{{ route('admin.olympiad-registrations.update', $registration) }}" class="row g-2">
                                                    @csrf
                                                    @method('put')
                                                    <div class="col-md-4">
                                                        <select class="form-select" name="status">
                                                            <option value="registered" @selected($registration->status==='registered')>Yangi</option>
                                                            <option value="confirmed" @selected($registration->status==='confirmed')>Tasdiqlangan</option>
                                                            <option value="participated" @selected($registration->status==='participated')>Qatnashgan</option>
                                                            <option value="absent" @selected($registration->status==='absent')>Kelmagan</option>
                                                            <option value="cancelled" @selected($registration->status==='cancelled')>Bekor qilingan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input class="form-control" name="notes" value="{{ $registration->notes }}" placeholder="Izoh yozing">
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <button class="btn btn-primary btn-sm w-100" type="submit">Saqlash</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="10" class="text-center text-muted py-4">Mos arizalar topilmadi.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $registrations->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
