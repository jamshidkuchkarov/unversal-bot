@extends('layouts.admin')

@php
    $statusOptions = [
        'pending' => ['label' => 'Yangi', 'badge' => 'secondary', 'icon' => 'mdi mdi-timer-sand'],
        'reviewing' => ['label' => 'Ko`rib chiqilmoqda', 'badge' => 'warning', 'icon' => 'mdi mdi-eye-check-outline'],
        'accepted' => ['label' => 'Qabul qilindi', 'badge' => 'success', 'icon' => 'mdi mdi-check-decagram-outline'],
        'rejected' => ['label' => 'Rad etildi', 'badge' => 'danger', 'icon' => 'mdi mdi-close-octagon-outline'],
        'waitlist' => ['label' => 'Kutish ro`yxati', 'badge' => 'info', 'icon' => 'mdi mdi-clock-outline'],
    ];
@endphp

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Qabul arizalari',
                        'subtitle' => 'Bot orqali kelgan barcha qabul arizalarini boshqaring.',
                    ])

                    @include('admin.partials.flash')

                    <div class="card">
                        <div class="card-body">
                            <form method="get" class="row g-3 align-items-end">
                                <div class="col-xl-4 col-md-6">
                                    <label class="form-label">Qidiruv</label>
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        value="{{ $filters['search'] }}"
                                        placeholder="O'quvchi yoki ota-ona ismi, telefon"
                                    >
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Qabul kampaniyasi</label>
                                    <select name="admission_id" class="form-select">
                                        <option value="">Barchasi</option>
                                        @foreach ($admissions as $admission)
                                            <option value="{{ $admission->id }}" @selected((string) $filters['admission_id'] === (string) $admission->id)>
                                                {{ $admission->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Barchasi</option>
                                        @foreach ($statusOptions as $value => $status)
                                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-2 col-md-6 d-flex gap-2">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.admission-applications.index') }}" class="btn btn-light border w-100">
                                        Tozalash
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="card-title mb-1">Arizalar ro`yxati</h5>
                                    <p class="text-muted mb-0">Har bir arizada o'quvchi, ota-ona, maktab va o`tish sababi ma'lumotlari ko'rinadi.</p>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="badge rounded-pill bg-light text-dark border">
                                        Jami: {{ $applications->total() }} ta ariza
                                    </span>
                                    <a href="{{ route('admin.admission-applications.export', request()->query()) }}" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-microsoft-excel me-1"></i> Excel
                                    </a>
                                </div>
                            </div>

                            <div class="row g-3">
                                @forelse ($applications as $application)
                                    @php($status = $statusOptions[$application->status] ?? $statusOptions['pending'])
                                    <div class="col-12">
                                        <div class="border rounded-4 p-3 p-md-4 shadow-sm h-100">
                                            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-3">
                                                <div class="d-flex gap-3">
                                                    <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-school-outline font-size-24"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1">{{ $application->student_full_name ?: 'Qabul arizasi' }}</h5>
                                                        <div class="text-muted">
                                                            <i class="mdi mdi-book-education-outline me-1"></i>
                                                            {{ $application->admission?->title ?? 'Qabul topilmadi' }}
                                                            @if ($application->target_variant)
                                                                <span class="mx-1">•</span>{{ $application->target_variant }}
                                                            @elseif ($application->target_class)
                                                                <span class="mx-1">•</span>{{ $application->target_class }}-sinf
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $status['badge'] }}-subtle text-{{ $status['badge'] }} border border-{{ $status['badge'] }} border-opacity-25">
                                                        <i class="{{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                                                    </span>
                                                    <div class="text-muted font-size-12 mt-2">
                                                        {{ $application->created_at?->format('d.m.Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-account-outline me-1 text-primary"></i> O'quvchi
                                                        </div>
                                                        <div class="text-muted mb-1">{{ $application->student_full_name ?: 'Ko\'rsatilmagan' }}</div>
                                                        <div class="text-muted mb-1">Tug`ilgan sana: {{ $application->student_birth_date?->format('d.m.Y') ?: 'Ko\'rsatilmagan' }}</div>
                                                        <div class="text-muted mb-0">Maktab: {{ $application->previous_school ?: 'Ko\'rsatilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-school-outline me-1"></i> Sinf va til</div>
                                                        <div class="fw-medium">{{ $application->target_class ? $application->target_class . '-sinf' : '-' }}</div>
                                                        <div class="text-muted">{{ $application->education_language === 'ru' ? 'Rus tili' : ($application->education_language === 'uz' ? 'O`zbek tili' : '-') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-calendar-check-outline me-1"></i> Review vaqti</div>
                                                        <div class="fw-medium">{{ $application->reviewed_at?->format('d.m.Y H:i') ?: 'Hali yo`q' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-account-heart-outline me-1 text-primary"></i> Ota-ona
                                                        </div>
                                                        <div class="text-muted mb-1">{{ $application->parent_full_name ?: 'Ko\'rsatilmagan' }}</div>
                                                        <div class="text-muted mb-1">Asosiy telefon: {{ $application->parent_phone ?: '-' }}</div>
                                                        <div class="text-muted mb-0">Qo`shimcha telefon: {{ $application->parent_phone_2 ?: '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-map-marker-outline me-1 text-primary"></i> Manzil va sabab
                                                        </div>
                                                        <div class="text-muted mb-2">{{ $application->address ?: 'Manzil ko`rsatilmagan' }}</div>
                                                        <div class="text-muted mb-0">{{ $application->transition_reason ?: 'Sabab kiritilmagan' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <form method="post" action="{{ route('admin.admission-applications.update', $application) }}" class="row g-3 align-items-end">
                                                @csrf
                                                @method('put')

                                                <div class="col-lg-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status">
                                                        @foreach ($statusOptions as $value => $option)
                                                            <option value="{{ $value }}" @selected($application->status === $value)>{{ $option['label'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-7">
                                                    <label class="form-label">Admin izohi</label>
                                                    <input class="form-control" name="admin_notes" value="{{ $application->admin_notes }}" placeholder="Masalan: Hujjatlar to'liq, test o'tkazildi">
                                                </div>
                                                <div class="col-lg-2">
                                                    <button class="btn btn-primary w-100" type="submit">
                                                        <i class="mdi mdi-content-save-outline me-1"></i> Saqlash
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-account-search-outline font-size-28 text-muted"></i>
                                            </div>
                                            <h5>Arizalar topilmadi</h5>
                                            <p class="text-muted mb-0">Hozirgi filterlar bo`yicha ma`lumot yo`q yoki bot orqali hali ariza kelmagan.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-4">{{ $applications->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
