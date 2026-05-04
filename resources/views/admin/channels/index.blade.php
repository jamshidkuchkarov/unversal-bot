@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Majburiy obuna',
                        'subtitle' => 'Kanal username, chat id va invite linklar.',
                        'action' => '<a href="'.route('admin.channels.create').'" class="btn btn-primary waves-effect waves-light">Kanal qo`shish</a>',
                    ])
                    @include('admin.partials.flash')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr><th>Nomi</th><th>Chat ID</th><th>Username</th><th>Majburiy</th><th class="text-end">Amallar</th></tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($channels as $channel)
                                        <tr>
                                            <td>{{ $channel->title }}</td>
                                            <td><code>{{ $channel->chat_id }}</code></td>
                                            <td>{{ $channel->username }}</td>
                                            <td><span class="badge {{ $channel->is_required ? 'bg-success' : 'bg-secondary' }}">{{ $channel->is_required ? 'Ha' : 'Yo`q' }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.channels.edit', $channel) }}" class="btn btn-sm btn-light">Tahrirlash</a>
                                                <form class="d-inline" method="post" action="{{ route('admin.channels.destroy', $channel) }}">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">O`chirish</button></form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">Kanallar yo`q.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $channels->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
