@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Telegram foydalanuvchilari',
                        'subtitle' => 'Bot orqali ro\'yxatdan o\'tgan barcha foydalanuvchilar va ularning statistikasi.',
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
                                                <i class="mdi mdi-account-multiple font-size-24"></i>
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
                                            <p class="text-muted fw-medium">Obuna bo'lgan</p>
                                            <h4 class="mb-0">{{ $stats['subscribed'] }}</h4>
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
                                            <p class="text-muted fw-medium">Faol</p>
                                            <h4 class="mb-0">{{ $stats['active'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-info align-self-center">
                                            <span class="avatar-title rounded-circle bg-info">
                                                <i class="mdi mdi-account-check font-size-24"></i>
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
                                            <p class="text-muted fw-medium">Bugun</p>
                                            <h4 class="mb-0">{{ $stats['today'] }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-warning align-self-center">
                                            <span class="avatar-title rounded-circle bg-warning">
                                                <i class="mdi mdi-calendar-today font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">📋 Foydalanuvchilar ro'yxati ({{ $users->total() }} ta)</h5>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                                        <i class="mdi mdi-file-excel me-1"></i>Excel
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick="window.print()">
                                        <i class="mdi mdi-printer me-1"></i>Chop etish
                                    </button>
                                </div>
                            </div>

                            <form method="get" class="row g-3 mb-4">
                                <div class="col-xl-4 col-md-6">
                                    <label class="form-label">🔍 Qidiruv</label>
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        value="{{ $filters['search'] }}"
                                        placeholder="Ism, username, telegram ID"
                                    >
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">📢 Obuna holati</label>
                                    <select name="is_subscribed" class="form-select">
                                        <option value="">Barchasi</option>
                                        <option value="1" @selected($filters['is_subscribed'] === '1')>✅ Obuna bo'lgan</option>
                                        <option value="0" @selected($filters['is_subscribed'] === '0')>❌ Obuna bo'lmagan</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <label class="form-label">⚡ Faollik holati</label>
                                    <select name="is_active" class="form-select">
                                        <option value="">Barchasi</option>
                                        <option value="1" @selected($filters['is_active'] === '1')>✅ Faol</option>
                                        <option value="0" @selected($filters['is_active'] === '0')>❌ Nofaol</option>
                                    </select>
                                </div>
                                <div class="col-xl-2 col-md-6">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" type="submit">
                                            <i class="mdi mdi-filter-variant"></i>
                                        </button>
                                        <a href="{{ route('admin.telegram-users.index') }}" class="btn btn-light border flex-fill" title="Tozalash">
                                            <i class="mdi mdi-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="usersTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>👤 Foydalanuvchi</th>
                                            <th>🆔 Telegram ID</th>
                                            <th>📢 Obuna</th>
                                            <th>⚡ Faollik</th>
                                            <th>🕐 Oxirgi faollik</th>
                                            <th>📅 Ro'yxatdan o'tgan</th>
                                            <th class="text-end">Amallar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $index => $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold">
                                                                {{ mb_substr($user->first_name ?? 'U', 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                                            @if($user->username)
                                                                <small class="text-muted">@{{ $user->username }}</small>
                                                            @else
                                                                <small class="text-muted fst-italic">Username yo'q</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><code class="text-dark">{{ $user->telegram_id }}</code></td>
                                                <td>
                                                    @if($user->is_subscribed)
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="mdi mdi-check-circle me-1"></i>Obuna
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            <i class="mdi mdi-close-circle me-1"></i>Yo'q
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->is_active)
                                                        <span class="badge bg-info-subtle text-info">
                                                            <i class="mdi mdi-check me-1"></i>Faol
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">
                                                            <i class="mdi mdi-minus me-1"></i>Nofaol
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->last_seen_at)
                                                        <small class="text-muted" title="{{ $user->last_seen_at->format('d.m.Y H:i') }}">
                                                            {{ $user->last_seen_at->diffForHumans() }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted fst-italic">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted" title="{{ $user->created_at?->format('d.m.Y H:i:s') }}">
                                                        {{ $user->created_at?->format('d.m.Y') }}
                                                    </small>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.telegram-users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> Ko'rish
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-account-search-outline font-size-28 text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted">Foydalanuvchilar topilmadi</h5>
                                                    <p class="text-muted mb-0">Hozirgi filterlar bo'yicha ma'lumot yo'q.</p>
                                                    @if($filters['search'] || $filters['is_subscribed'] !== '' || $filters['is_active'] !== '')
                                                        <a href="{{ route('admin.telegram-users.index') }}" class="btn btn-sm btn-primary mt-3">
                                                            <i class="mdi mdi-refresh me-1"></i>Filtrlarni tozalash
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">{{ $users->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportToExcel() {
            // Get table
            const table = document.getElementById('usersTable');
            const rows = [];

            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            rows.push(headers);

            // Get data rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const cells = tr.querySelectorAll('td');
                if (cells.length > 1) { // Skip empty state row
                    const row = [];
                    cells.forEach((td, index) => {
                        // Clean up cell content
                        let text = td.textContent.trim().replace(/\s+/g, ' ');
                        row.push(text);
                    });
                    rows.push(row);
                }
            });

            // Create CSV content
            let csvContent = '\uFEFF'; // UTF-8 BOM for Excel
            rows.forEach(row => {
                csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
            });

            // Download
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'foydalanuvchilar_' + new Date().toISOString().split('T')[0] + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
