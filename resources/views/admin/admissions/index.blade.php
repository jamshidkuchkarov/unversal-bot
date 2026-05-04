@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Qabul kampaniyalari',
                        'subtitle' => 'Maktabga qabul qilish kampaniyalarini boshqaring.',
                        'action' => '<a href="'.route('admin.admissions.create').'" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-1"></i>Qabul qo\'shish</a>',
                    ])

                    @include('admin.partials.flash')

                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Jami</p>
                                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="mdi mdi-school font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Faol</p>
                                            <h4 class="mb-0">{{ $stats['published'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-success align-self-center">
                                            <span class="avatar-title rounded-circle bg-success">
                                                <i class="mdi mdi-check-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Qoralama</p>
                                            <h4 class="mb-0">{{ $stats['draft'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-warning align-self-center">
                                            <span class="avatar-title rounded-circle bg-warning">
                                                <i class="mdi mdi-pencil font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Yopilgan</p>
                                            <h4 class="mb-0">{{ $stats['closed'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-danger align-self-center">
                                            <span class="avatar-title rounded-circle bg-danger">
                                                <i class="mdi mdi-close-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form method="get" class="row g-3 mb-4">
                                <div class="col-xl-4 col-md-6">
                                    <label class="form-label">🔍 Qidiruv</label>
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        value="{{ $filters['search'] }}"
                                        placeholder="Kampaniya nomi yoki tavsifi"
                                    >
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">📌 Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Barchasi</option>
                                        <option value="draft" @selected($filters['status'] === 'draft')>📝 Qoralama</option>
                                        <option value="published" @selected($filters['status'] === 'published')>✅ Faol</option>
                                        <option value="closed" @selected($filters['status'] === 'closed')>❌ Yopilgan</option>
                                        <option value="completed" @selected($filters['status'] === 'completed')>📦 Yakunlangan</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">📅 O'quv yili</label>
                                    <input
                                        type="text"
                                        name="academic_year"
                                        class="form-control"
                                        value="{{ $filters['academic_year'] }}"
                                        placeholder="2024-2025"
                                    >
                                </div>
                                <div class="col-xl-2 col-md-6">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" type="submit">
                                            <i class="mdi mdi-filter-variant"></i>
                                        </button>
                                        <a href="{{ route('admin.admissions.index') }}" class="btn btn-light border flex-fill" title="Tozalash">
                                            <i class="mdi mdi-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">📚 Qabul kampaniyalari ro'yxati ({{ $admissions->total() }} ta)</h5>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>📋 Nomi</th>
                                            <th>📅 O'quv yili</th>
                                            <th>🎓 Sinflar</th>
                                            <th>🗣 Ta'lim tili</th>
                                            <th>📄 Hujjatlar</th>
                                            <th>📌 Status</th>
                                            <th>🕐 Yaratilgan</th>
                                            <th class="text-end">Amallar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($admissions as $index => $admission)
                                        <tr>
                                            <td>{{ $admissions->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            <i class="mdi mdi-school"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $admission->title }}</h6>
                                                        @if($admission->description)
                                                            <small class="text-muted">{{ Str::limit($admission->description, 40) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $admission->academic_year }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($admission->target_classes && count($admission->target_classes) > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($admission->target_classes, 0, 3) as $class)
                                                            <span class="badge bg-light text-dark border">{{ $class }}</span>
                                                        @endforeach
                                                        @if(count($admission->target_classes) > 3)
                                                            <span class="badge bg-light text-dark border">+{{ count($admission->target_classes) - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($admission->admission_options && count($admission->admission_options) > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($admission->admission_options as $lang)
                                                            @php
                                                                $langConfig = [
                                                                    'uz' => ['label' => '🇺🇿 O\'zbek', 'color' => 'primary'],
                                                                    'ru' => ['label' => '🇷🇺 Rus', 'color' => 'info'],
                                                                ];
                                                                $config = $langConfig[$lang] ?? ['label' => $lang, 'color' => 'secondary'];
                                                            @endphp
                                                            <span class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }}">{{ $config['label'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($admission->required_documents && count($admission->required_documents) > 0)
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        {{ count($admission->required_documents) }} ta
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'draft' => ['label' => 'Qoralama', 'color' => 'secondary'],
                                                        'published' => ['label' => 'Faol', 'color' => 'success'],
                                                        'closed' => ['label' => 'Yopilgan', 'color' => 'danger'],
                                                        'completed' => ['label' => 'Yakunlangan', 'color' => 'dark'],
                                                    ];
                                                    $status = $statusConfig[$admission->status] ?? ['label' => $admission->status, 'color' => 'info'];
                                                @endphp
                                                <span class="badge bg-{{ $status['color'] }}-subtle text-{{ $status['color'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted" title="{{ $admission->created_at?->format('d.m.Y H:i:s') }}">
                                                    {{ $admission->created_at?->format('d.m.Y') }}
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <a href="{{ route('admin.admissions.edit', $admission) }}" class="btn btn-sm btn-light" title="Tahrirlash">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <form class="d-inline" method="post" action="{{ route('admin.admissions.destroy', $admission) }}" onsubmit="return confirm('Qabul kampaniyasini o\'chirishni tasdiqlaysizmi?')">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-sm btn-danger" type="submit" title="O'chirish">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                    <i class="mdi mdi-school-outline font-size-28 text-muted"></i>
                                                </div>
                                                <h5 class="text-muted">Qabul kampaniyalari yo'q</h5>
                                                <p class="text-muted mb-3">Hali hech qanday qabul kampaniyasi yaratilmagan.</p>
                                                <a href="{{ route('admin.admissions.create') }}" class="btn btn-primary">
                                                    <i class="mdi mdi-plus me-1"></i>Birinchi kampaniyani yaratish
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">{{ $admissions->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
