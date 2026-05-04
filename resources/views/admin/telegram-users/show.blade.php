@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">👤 Foydalanuvchi profili</h4>
                                <div class="page-title-right">
                                    <a href="{{ route('admin.telegram-users.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-arrow-left me-1"></i>Orqaga
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin.partials.flash')

                    <div class="row">
                        <!-- Profil kartochkasi -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <span class="avatar-title rounded-circle bg-primary font-size-32 fw-bold">
                                                {{ mb_substr($user->first_name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                        @if($user->username)
                                            <p class="text-muted mb-3">
                                                <i class="mdi mdi-at"></i> {{ $user->username }}
                                            </p>
                                        @else
                                            <p class="text-muted mb-3 fst-italic">Username yo'q</p>
                                        @endif

                                        <div class="d-flex gap-2 justify-content-center mb-3">
                                            @if($user->is_subscribed)
                                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                                    <i class="mdi mdi-check-circle me-1"></i>Obuna bo'lgan
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                                    <i class="mdi mdi-close-circle me-1"></i>Obuna yo'q
                                                </span>
                                            @endif
                                            @if($user->is_active)
                                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                                    <i class="mdi mdi-check me-1"></i>Faol
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                                    <i class="mdi mdi-minus me-1"></i>Nofaol
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <h5 class="font-size-15 mb-3">📋 Asosiy ma'lumotlar</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0 text-muted" style="width: 45%;">
                                                        <i class="mdi mdi-identifier me-1"></i>Telegram ID:
                                                    </td>
                                                    <td class="text-end">
                                                        <code class="text-dark">{{ $user->telegram_id }}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 text-muted">
                                                        <i class="mdi mdi-message-text me-1"></i>Chat ID:
                                                    </td>
                                                    <td class="text-end">
                                                        <code class="text-dark">{{ $user->chat_id }}</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 text-muted">
                                                        <i class="mdi mdi-translate me-1"></i>Til:
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="badge bg-light text-dark">
                                                            {{ strtoupper($user->preference?->language ?? $user->language_code ?? 'uz') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 text-muted">
                                                        <i class="mdi mdi-bell me-1"></i>Bildirishnomalar:
                                                    </td>
                                                    <td class="text-end">
                                                        @if($user->preference?->notifications_enabled ?? true)
                                                            <span class="badge bg-success-subtle text-success">
                                                                <i class="mdi mdi-check me-1"></i>Yoniq
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger">
                                                                <i class="mdi mdi-close me-1"></i>O'chiq
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="my-4">

                                    <h5 class="font-size-15 mb-3">📅 Vaqt ma'lumotlari</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="ps-0 text-muted" style="width: 45%;">
                                                        <i class="mdi mdi-account-plus me-1"></i>Ro'yxatdan o'tgan:
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ $user->created_at?->format('d.m.Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->created_at?->format('H:i') }}</small>
                                                    </td>
                                                </tr>
                                                @if($user->subscribed_at)
                                                <tr>
                                                    <td class="ps-0 text-muted">
                                                        <i class="mdi mdi-check-circle me-1"></i>Obuna sanasi:
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ $user->subscribed_at->format('d.m.Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->subscribed_at->format('H:i') }}</small>
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($user->last_seen_at)
                                                <tr>
                                                    <td class="ps-0 text-muted">
                                                        <i class="mdi mdi-clock-outline me-1"></i>Oxirgi faollik:
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ $user->last_seen_at->format('d.m.Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->last_seen_at->diffForHumans() }}</small>
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Arizalar tarixi -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">📝 Arizalar va ro'yxatlar tarixi</h5>

                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#vacancies" role="tab">
                                                <span class="d-block d-sm-none"><i class="mdi mdi-briefcase"></i></span>
                                                <span class="d-none d-sm-block">
                                                    <i class="mdi mdi-briefcase me-1"></i>Vakansiyalar
                                                    <span class="badge bg-primary ms-1">{{ $vacancyApplications->count() }}</span>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#olympiads" role="tab">
                                                <span class="d-block d-sm-none"><i class="mdi mdi-trophy"></i></span>
                                                <span class="d-none d-sm-block">
                                                    <i class="mdi mdi-trophy me-1"></i>Olimpiadalar
                                                    <span class="badge bg-primary ms-1">{{ $olympiadRegistrations->count() }}</span>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#admissions" role="tab">
                                                <span class="d-block d-sm-none"><i class="mdi mdi-school"></i></span>
                                                <span class="d-none d-sm-block">
                                                    <i class="mdi mdi-school me-1"></i>Qabul
                                                    <span class="badge bg-primary ms-1">{{ $admissionApplications->count() }}</span>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content p-4">
                                        <!-- Vakansiyalar -->
                                        <div class="tab-pane active" id="vacancies" role="tabpanel">
                                            @forelse($vacancyApplications as $application)
                                                <div class="card border shadow-none mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <div>
                                                                <h5 class="mb-1">
                                                                    <i class="mdi mdi-briefcase text-primary me-1"></i>
                                                                    {{ $application->vacancy?->title ?? 'Vakansiya topilmadi' }}
                                                                </h5>
                                                                <small class="text-muted">
                                                                    <i class="mdi mdi-calendar me-1"></i>
                                                                    {{ $application->created_at?->format('d.m.Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}-subtle text-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }} px-3 py-2">
                                                                {{ $application->status }}
                                                            </span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-phone text-muted me-1"></i>
                                                                    <strong>Telefon:</strong> {{ $application->phone }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-cake text-muted me-1"></i>
                                                                    <strong>Yosh:</strong> {{ $application->age }} yosh
                                                                </p>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-briefcase-check text-muted me-1"></i>
                                                                    <strong>Tajriba:</strong> {{ $application->experience_years }} yil
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-briefcase-outline font-size-28 text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted">Vakansiya arizalari yo'q</h5>
                                                    <p class="text-muted mb-0">Foydalanuvchi hali hech qanday vakansiyaga ariza topshirmagan.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <!-- Olimpiadalar -->
                                        <div class="tab-pane" id="olympiads" role="tabpanel">
                                            @forelse($olympiadRegistrations as $registration)
                                                <div class="card border shadow-none mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <div>
                                                                <h5 class="mb-1">
                                                                    <i class="mdi mdi-trophy text-warning me-1"></i>
                                                                    {{ $registration->olympiad?->title ?? 'Olimpiada topilmadi' }}
                                                                </h5>
                                                                <small class="text-muted">
                                                                    <i class="mdi mdi-calendar me-1"></i>
                                                                    {{ $registration->created_at?->format('d.m.Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <span class="badge bg-{{ $registration->status === 'approved' ? 'success' : ($registration->status === 'rejected' ? 'danger' : 'warning') }}-subtle text-{{ $registration->status === 'approved' ? 'success' : ($registration->status === 'rejected' ? 'danger' : 'warning') }} px-3 py-2">
                                                                {{ $registration->status }}
                                                            </span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-school text-muted me-1"></i>
                                                                    <strong>Sinf:</strong> {{ $registration->class_number }}-{{ $registration->class_letter }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-phone text-muted me-1"></i>
                                                                    <strong>Telefon:</strong> {{ $registration->phone }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-trophy-outline font-size-28 text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted">Olimpiada ro'yxatlari yo'q</h5>
                                                    <p class="text-muted mb-0">Foydalanuvchi hali hech qanday olimpiadaga ro'yxatdan o'tmagan.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <!-- Qabul -->
                                        <div class="tab-pane" id="admissions" role="tabpanel">
                                            @forelse($admissionApplications as $application)
                                                <div class="card border shadow-none mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <div>
                                                                <h5 class="mb-1">
                                                                    <i class="mdi mdi-school text-info me-1"></i>
                                                                    {{ $application->admission?->title ?? 'Qabul topilmadi' }}
                                                                </h5>
                                                                <small class="text-muted">
                                                                    <i class="mdi mdi-calendar me-1"></i>
                                                                    {{ $application->created_at?->format('d.m.Y H:i') }}
                                                                </small>
                                                            </div>
                                                            <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}-subtle text-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }} px-3 py-2">
                                                                {{ $application->status }}
                                                            </span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-account text-muted me-1"></i>
                                                                    <strong>O'quvchi:</strong> {{ $application->student_full_name ?: '-' }}
                                                                </p>
                                                            </div>
                                                            @if($application->target_variant)
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-book-open-variant text-muted me-1"></i>
                                                                    <strong>Yo'nalish:</strong> {{ $application->target_variant }}
                                                                </p>
                                                            </div>
                                                            @elseif($application->target_class)
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-school text-muted me-1"></i>
                                                                    <strong>Sinf:</strong> {{ $application->target_class }}
                                                                </p>
                                                            </div>
                                                            @endif
                                                            <div class="col-md-4">
                                                                <p class="mb-2">
                                                                    <i class="mdi mdi-phone text-muted me-1"></i>
                                                                    <strong>Telefon:</strong> {{ $application->parent_phone ?: '-' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-school-outline font-size-28 text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted">Qabul arizalari yo'q</h5>
                                                    <p class="text-muted mb-0">Foydalanuvchi hali hech qanday qabul arizasi topshirmagan.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
