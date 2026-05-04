@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Dashboard',
                        'subtitle' => 'Bot, admin panel va kontent boshqaruvi shu yerdan yuradi.',
                    ])

                    @include('admin.partials.flash')

                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Foydalanuvchilar</p>
                                            <h4 class="mb-0">{{ $stats['users'] }}</h4>
                                            <p class="text-muted mb-0"><span class="text-success me-2">+{{ $stats['users_today'] }}</span>Bugun</p>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-user-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Vakansiya arizalari</p>
                                            <h4 class="mb-0">{{ $stats['vacancy_applications'] }}</h4>
                                            <p class="text-muted mb-0"><span class="text-warning me-2">{{ $stats['vacancy_applications_pending'] }}</span>Yangi</p>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-success align-self-center">
                                            <span class="avatar-title rounded-circle bg-success">
                                                <i class="bx bx-briefcase font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Olimpiada ro'yxatlari</p>
                                            <h4 class="mb-0">{{ $stats['olympiad_registrations'] }}</h4>
                                            <p class="text-muted mb-0">Jami</p>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-info align-self-center">
                                            <span class="avatar-title rounded-circle bg-info">
                                                <i class="bx bx-medal font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-2">Qabul arizalari</p>
                                            <h4 class="mb-0">{{ $stats['admission_applications'] }}</h4>
                                            <p class="text-muted mb-0"><span class="text-warning me-2">{{ $stats['admission_applications_pending'] }}</span>Yangi</p>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-warning align-self-center">
                                            <span class="avatar-title rounded-circle bg-warning">
                                                <i class="bx bx-notepad font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Tezkor statistika</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>Obuna bo'lgan:</td>
                                                    <td class="text-end"><strong>{{ $stats['users_subscribed'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Faol vakansiyalar:</td>
                                                    <td class="text-end"><strong>{{ $stats['vacancies'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Faol olimpiadalar:</td>
                                                    <td class="text-end"><strong>{{ $stats['olympiads'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Faol qabullar:</td>
                                                    <td class="text-end"><strong>{{ $stats['admissions'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>E'lonlar:</td>
                                                    <td class="text-end"><strong>{{ $stats['announcements'] }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.settings.edit') }}" class="btn btn-primary btn-sm">Bot sozlamalari</a>
                                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-light btn-sm">Yangi e'lon</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">So'nggi vakansiya arizalari</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <tbody>
                                                @forelse($recentApplications['vacancy'] as $app)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $app->full_name }}</strong><br>
                                                            <small class="text-muted">{{ $app->vacancy?->title }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <small class="text-muted">{{ $app->created_at?->diffForHumans() }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="2" class="text-center text-muted">Arizalar yo'q</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('admin.vacancy-applications.index') }}" class="btn btn-sm btn-link">Barchasini ko'rish →</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">So'nggi qabul arizalari</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <tbody>
                                                @forelse($recentApplications['admission'] as $app)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $app->student_full_name ?: 'Qabul arizasi' }}</strong><br>
                                                            <small class="text-muted">{{ $app->admission?->title }} - {{ $app->target_variant ?: ($app->target_class . '-sinf') }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <small class="text-muted">{{ $app->created_at?->diffForHumans() }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="2" class="text-center text-muted">Arizalar yo'q</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('admin.admission-applications.index') }}" class="btn btn-sm btn-link">Barchasini ko'rish →</a>
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
