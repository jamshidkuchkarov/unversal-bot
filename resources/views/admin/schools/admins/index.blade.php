@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => $school->name . ' - Adminlar',
                'subtitle' => 'Maktab adminlarini boshqarish.',
                'action' => '<a href="'.route('admin.schools.index').'" class="btn btn-light waves-effect me-2">Orqaga</a><a href="'.route('admin.schools.admins.create', $school).'" class="btn btn-primary waves-effect waves-light">Admin qo`shish</a>',
            ])
            @include('admin.partials.flash')
            <div class="card"><div class="card-body"><div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ism</th>
                            <th>Email</th>
                            <th>Ruxsatlar</th>
                            <th>Holat</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td><code>{{ $admin->email }}</code></td>
                            <td>
                                @php
                                    $permissions = is_string($admin->pivot->permissions)
                                        ? json_decode($admin->pivot->permissions, true)
                                        : $admin->pivot->permissions;
                                @endphp
                                @if($permissions && is_array($permissions) && count($permissions) > 0)
                                    @foreach($permissions as $perm)
                                        <span class="badge bg-info">{{ $perm }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $admin->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $admin->is_active ? 'Faol' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.schools.admins.edit', [$school, $admin]) }}" class="btn btn-sm btn-light">Tahrirlash</a>
                                <form class="d-inline" method="post" action="{{ route('admin.schools.admins.destroy', [$school, $admin]) }}" onsubmit="return confirm('Rostdan ham o`chirmoqchimisiz?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-danger" type="submit">O`chirish</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Adminlar yo`q.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $admins->links() }}</div>
            </div></div>
        </div></div></div>
    </div>
@endsection
