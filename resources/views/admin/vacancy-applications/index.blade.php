@extends('layouts.admin')

@php
    $statusOptions = [
        'pending' => ['label' => 'Yangi', 'badge' => 'secondary', 'icon' => 'mdi mdi-timer-sand'],
        'reviewing' => ['label' => 'Ko`rib chiqilmoqda', 'badge' => 'warning', 'icon' => 'mdi mdi-eye-check-outline'],
        'invited' => ['label' => 'Suhbatga chaqirilgan', 'badge' => 'info', 'icon' => 'mdi mdi-account-voice'],
        'hired' => ['label' => 'Ishga olingan', 'badge' => 'success', 'icon' => 'mdi mdi-check-decagram-outline'],
        'rejected' => ['label' => 'Rad etilgan', 'badge' => 'danger', 'icon' => 'mdi mdi-close-octagon-outline'],
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
                        'title' => 'Vakansiya arizalari',
                        'subtitle' => 'Bot orqali kelgan barcha arizalarni filterlab, statusini boshqaring.',
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
                                        placeholder="FIO, telefon yoki tajriba bo`yicha qidiring"
                                    >
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">Vakansiya</label>
                                    <select name="vacancy_id" class="form-select">
                                        <option value="">Barchasi</option>
                                        <option value="reserve" @selected((string) $filters['vacancy_id'] === 'reserve')>
                                            Zahira vakansiya
                                        </option>
                                        @foreach ($vacancies as $vacancy)
                                            <option value="{{ $vacancy->id }}" @selected((string) $filters['vacancy_id'] === (string) $vacancy->id)>
                                                {{ $vacancy->title }}
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
                                    <a href="{{ route('admin.vacancy-applications.index') }}" class="btn btn-light border w-100">
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
                                    <p class="text-muted mb-0">Har bir arizada qaysi vakansiyaga topshirgani va kiritilgan to`liq ma`lumotlar ko`rinadi.</p>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="badge rounded-pill bg-light text-dark border">
                                        Jami: {{ $applications->total() }} ta ariza
                                    </span>
                                    <a href="{{ route('admin.vacancy-applications.export', request()->query()) }}" class="btn btn-success btn-sm">
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
                                                        <i class="mdi mdi-account-tie-outline font-size-24"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1">{{ $application->full_name }}</h5>
                                                        <div class="text-muted">
                                                            <i class="mdi mdi-briefcase-outline me-1"></i>
                                                            {{ $application->vacancy?->title ?? 'Zahira vakansiya' }}
                                                            @if ($application->vacancy?->subject)
                                                                <span class="mx-1">•</span>{{ $application->vacancy->subject }}
                                                            @endif
                                                            <span class="mx-1">•</span>{{ $application->application_type === 'reserve' ? 'Zahira' : 'Mavjud' }}
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
                                                <div class="col-xl-2 col-md-4">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-phone-outline me-1"></i> Telefon</div>
                                                        <div class="fw-medium">{{ $application->phone ?: '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-send-outline me-1"></i> Telegram</div>
                                                        <div class="fw-medium">{{ $application->telegram_contact ?: '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-4">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-cake-variant-outline me-1"></i> Tug`ilgan sana</div>
                                                        <div class="fw-medium">{{ $application->birth_date?->format('d.m.Y') ?: '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-map-marker-outline me-1"></i> Manzil</div>
                                                        <div class="fw-medium">{{ $application->address ?: '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-account-check-outline me-1"></i> Ko`rib chiqqan admin</div>
                                                        <div class="fw-medium">{{ $application->reviewer?->name ?: 'Hali ko`rilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-calendar-check-outline me-1"></i> Review vaqti</div>
                                                        <div class="fw-medium">{{ $application->reviewed_at?->format('d.m.Y H:i') ?: 'Hali yo`q' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-text-box-search-outline me-1 text-primary"></i> Tajriba
                                                        </div>
                                                        <div class="text-muted mb-0">{{ $application->experience ?: 'Ma`lumot kiritilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-school-outline me-1 text-primary"></i> Ta`lim va sertifikatlar
                                                        </div>
                                                        <div class="text-muted mb-2">{{ $application->education ?: 'Kiritilmagan' }}</div>
                                                        <div class="text-muted mb-0">{{ $application->certificates ?: 'Sertifikat ko`rsatilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-brain me-1 text-primary"></i> Ko`nikmalar va yutuqlar
                                                        </div>
                                                        <div class="text-muted mb-2">{{ $application->skills ?: 'Ko`nikmalar kiritilmagan' }}</div>
                                                        <div class="text-muted mb-0">{{ $application->achievements ?: 'Yutuqlar ko`rsatilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-star-outline me-1 text-primary"></i> O`zi haqida
                                                        </div>
                                                        <div class="text-muted mb-0">{{ $application->about_self ?: 'Ma`lumot yo`q' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-note-edit-outline me-1 text-primary"></i> Admin izohi
                                                        </div>
                                                        <div class="text-muted mb-0">{{ $application->admin_notes ?: 'Izoh yo`q' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($application->cv_file_path || $application->photo_file_path)
                                            <div class="row g-3 mb-3">
                                                @if($application->cv_file_path)
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-file-document-outline me-1 text-primary"></i> CV / Rezyume
                                                        </div>
                                                        <a href="{{ asset('storage/' . $application->cv_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="mdi mdi-download me-1"></i> Yuklab olish
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($application->photo_file_path)
                                                <div class="col-lg-6">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="fw-semibold mb-2">
                                                            <i class="mdi mdi-image-outline me-1 text-primary"></i> Rasm
                                                        </div>
                                                        <a href="{{ asset('storage/' . $application->photo_file_path) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $application->photo_file_path) }}" alt="Photo" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            @endif

                                            <form method="post" action="{{ route('admin.vacancy-applications.update', $application) }}" class="row g-3 align-items-end">
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
                                                    <input class="form-control" name="admin_notes" value="{{ $application->admin_notes }}" placeholder="Masalan: Suhbatga chaqirildi, tajribasi mos">
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
