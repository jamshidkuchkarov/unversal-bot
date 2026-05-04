@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => 'E`lonlar',
                'subtitle' => 'Kanal, guruh yoki foydalanuvchilarga yuboriladigan e`lonlar.',
                'action' => '<a href="'.route('admin.announcements.create').'" class="btn btn-primary waves-effect waves-light">E`lon qo`shish</a>',
            ])
            @include('admin.partials.flash')
            <div class="card"><div class="card-body"><div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Nomi</th><th>Target</th><th>Status</th><th>Scheduled</th><th class="text-end">Amallar</th></tr></thead>
                    <tbody>
                    @forelse ($announcements as $announcement)
                        <tr>
                            <td>{{ $announcement->title ?: \Illuminate\Support\Str::limit($announcement->message_text, 40) }}</td>
                            <td>{{ $announcement->target_type }}</td>
                            <td>
                                <span class="badge bg-{{ $announcement->status === 'sent' ? 'success' : ($announcement->status === 'sending' ? 'warning' : 'info') }}">
                                    {{ $announcement->status }}
                                </span>
                                @if($announcement->status === 'sending' && $announcement->total_recipients > 0)
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ ($announcement->sent_count / $announcement->total_recipients) * 100 }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $announcement->sent_count }}/{{ $announcement->total_recipients }}</small>
                                @endif
                                @if($announcement->status === 'sent')
                                    <br><small class="text-muted">✅ {{ $announcement->sent_count }} | ❌ {{ $announcement->failed_count }}</small>
                                @endif
                            </td>
                            <td>{{ $announcement->scheduled_at?->format('d.m.Y H:i') ?? '-' }}</td>
                            <td class="text-end">
                                @if(in_array($announcement->status, ['draft', 'scheduled', 'sent', 'failed']))
                                    <form class="d-inline" method="post" action="{{ route('admin.announcements.send-test', $announcement) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-info" type="submit">🧪 Test</button>
                                    </form>
                                @endif
                                @if(in_array($announcement->status, ['draft', 'scheduled', 'failed']))
                                    <form class="d-inline" method="post" action="{{ route('admin.announcements.send', $announcement) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit" onclick="return confirm('E\'lonni yuborishni tasdiqlaysizmi?')">📤 Yuborish</button>
                                    </form>
                                @endif
                                @if($announcement->status === 'sent')
                                    <form class="d-inline" method="post" action="{{ route('admin.announcements.send', $announcement) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-warning" type="submit" onclick="return confirm('E\'lonni qayta yuborishni tasdiqlaysizmi?')">🔄 Qayta yuborish</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-light">Tahrirlash</a>
                                <form class="d-inline" method="post" action="{{ route('admin.announcements.destroy', $announcement) }}">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">O`chirish</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">E`lonlar yo`q.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div><div class="mt-3">{{ $announcements->links() }}</div></div></div>
        </div></div></div>
    </div>

    <script>
        // Auto-refresh if there are sending announcements
        @if($announcements->where('status', 'sending')->isNotEmpty())
            setTimeout(() => {
                window.location.reload();
            }, 2000); // Refresh every 2 seconds for faster updates
        @endif
    </script>
@endsection
