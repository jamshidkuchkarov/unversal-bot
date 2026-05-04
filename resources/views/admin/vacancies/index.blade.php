@extends('layouts.admin')

@php
    $statusStyles = [
        'draft' => ['label' => 'Qoralama', 'badge' => 'secondary', 'icon' => 'mdi mdi-file-document-outline'],
        'published' => ['label' => 'Faol', 'badge' => 'success', 'icon' => 'mdi mdi-check-decagram-outline'],
        'closed' => ['label' => 'Yopilgan', 'badge' => 'warning', 'icon' => 'mdi mdi-lock-outline'],
        'archived' => ['label' => 'Arxiv', 'badge' => 'dark', 'icon' => 'mdi mdi-archive-outline'],
    ];

    $categoryLabels = [
        'teacher' => 'O`qituvchi',
        'technical' => 'Texnik xodim',
        'management' => 'Boshqaruv',
        'other' => 'Boshqa',
    ];

    $publishedCount = $vacancies->getCollection()->where('status', 'published')->count();
    $draftCount = $vacancies->getCollection()->where('status', 'draft')->count();
@endphp

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Vakansiyalar',
                        'subtitle' => 'O`qituvchilar va xodimlar uchun ish o`rinlarini boshqaring.',
                        'action' => '<a href="'.route('admin.vacancies.create').'" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-plus-circle-outline me-1"></i> Vakansiya qo`shish</a>',
                    ])

                    @include('admin.partials.flash')

                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Jami vakansiyalar</p>
                                            <h4 class="mb-0">{{ $vacancies->total() }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-briefcase-outline font-size-24"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Faol e`lonlar</p>
                                            <h4 class="mb-0">{{ $publishedCount }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-bullhorn-outline font-size-24"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Qoralamalar</p>
                                            <h4 class="mb-0">{{ $draftCount }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-note-edit-outline font-size-24"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                                <div>
                                    <h5 class="card-title mb-1">Vakansiyalar ro`yxati</h5>
                                    <p class="text-muted mb-0">Bu yerda faqat real vakansiyalar yuritiladi, bot esa ro`yxat oxirida avtomatik zahira ariza tugmasini ham ko`rsatadi.</p>
                                </div>
                                <span class="badge rounded-pill border">
                                    <i class="mdi mdi-robot-outline me-1"></i> Bot: real vakansiyalar + oxirida zahira ariza tugmasi
                                </span>
                            </div>

                            <div class="row g-3">
                                @forelse ($vacancies as $vacancy)
                                    @php($status = $statusStyles[$vacancy->status] ?? $statusStyles['draft'])
                                    <div class="col-xl-6">
                                        <div class="card border shadow-sm h-100 mb-0">
                                            <div class="card-body p-3">
                                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-briefcase-account-outline font-size-22"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1">{{ $vacancy->title }}</h5>
                                                        <div class="text-muted">
                                                            {{ $categoryLabels[$vacancy->category] ?? ucfirst($vacancy->category) }}
                                                            @if ($vacancy->subject)
                                                                <span class="mx-1">•</span>{{ $vacancy->subject }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $status['badge'] }}-subtle text-{{ $status['badge'] }} border border-{{ $status['badge'] }} border-opacity-25">
                                                    <i class="{{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                                                </span>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-book-open-page-variant-outline me-1"></i> Fan</div>
                                                        <div class="fw-medium">{{ $vacancy->subject ?: 'Ko`rsatilmagan' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-clock-outline me-1"></i> Ish jadvali</div>
                                                        <div class="fw-medium">{{ $vacancy->work_schedule ?: 'Kelishiladi' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-cash-multiple me-1"></i> Maosh</div>
                                                        <div class="fw-medium">
                                                            @if ($vacancy->salary_min || $vacancy->salary_max)
                                                                {{ $vacancy->salary_min ? number_format((float) $vacancy->salary_min, 0, '.', ' ') : '?' }}
                                                                -
                                                                {{ $vacancy->salary_max ? number_format((float) $vacancy->salary_max, 0, '.', ' ') : '?' }}
                                                            @else
                                                                Kelishiladi
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded-3 px-3 py-2 h-100">
                                                        <div class="text-muted font-size-12 mb-1"><i class="mdi mdi-calendar-end me-1"></i> Deadline</div>
                                                        <div class="fw-medium">{{ $vacancy->deadline?->format('d.m.Y') ?? 'Belgilanmagan' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($vacancy->description)
                                                <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($vacancy->description), 160) }}</p>
                                            @endif

                                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                                <div class="text-muted font-size-13">
                                                    <i class="mdi mdi-account-details-outline me-1"></i> Bot arizada standart ma`lumotlarni so`raydi
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="btn btn-sm btn-light border">
                                                        <i class="mdi mdi-pencil-outline me-1"></i>Tahrirlash
                                                    </a>
                                                    <form method="post" action="{{ route('admin.vacancies.destroy', $vacancy) }}" onsubmit="return confirm('Vakansiya o`chirilsinmi?')">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-sm btn-soft-danger" type="submit">
                                                            <i class="mdi mdi-trash-can-outline me-1"></i>O`chirish
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-briefcase-search-outline font-size-28 text-muted"></i>
                                            </div>
                                            <h5>Vakansiyalar hali yo`q</h5>
                                            <p class="text-muted mb-3">Yangi vakansiya yarating, keyin bot foydalanuvchilarni shu e`longa ro`yxatdan o`tkazadi.</p>
                                            <a href="{{ route('admin.vacancies.create') }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus-circle-outline me-1"></i> Vakansiya yaratish
                                            </a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-4">{{ $vacancies->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
