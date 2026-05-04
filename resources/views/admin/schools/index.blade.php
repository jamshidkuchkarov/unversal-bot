@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => 'Maktablar',
                'subtitle' => 'Super admin uchun maktablar ro`yxati.',
                'action' => '<a href="'.route('admin.schools.create').'" class="btn btn-primary waves-effect waves-light">Maktab qo`shish</a>',
            ])
            @include('admin.partials.flash')
            <div class="card"><div class="card-body"><div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Nomi</th><th>Slug</th><th>Hudud</th><th>Holat</th><th class="text-end">Amallar</th></tr></thead>
                    <tbody>
                    @forelse ($schools as $school)
                        <tr>
                            <td>{{ $school->name }}</td>
                            <td><code>{{ $school->slug }}</code></td>
                            <td>{{ trim(($school->city ?? '').' '.($school->district ?? '')) }}</td>
                            <td><span class="badge {{ $school->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $school->is_active ? 'Faol' : 'Nofaol' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.schools.admins.index', $school) }}" class="btn btn-sm btn-info">Adminlar</a>
                                <a href="{{ route('admin.settings.edit', ['school_id' => $school->id]) }}" class="btn btn-sm btn-primary">Bot sozlamalari</a>
                                <a href="{{ route('admin.schools.edit', $school) }}" class="btn btn-sm btn-light">Tahrirlash</a>
                                <form class="d-inline" method="post" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Rostdan ham o`chirmoqchimisiz?')">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">O`chirish</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Maktablar yo`q.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div><div class="mt-3">{{ $schools->links() }}</div></div></div>
        </div></div></div>
    </div>
@endsection
