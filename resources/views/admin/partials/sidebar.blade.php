@php
    $menuGroups = [
        [
            'title' => 'Boshqaruv',
            'items' => array_values(array_filter([
                [
                    'label' => 'Dashboard',
                    'route' => route('admin.dashboard'),
                    'match' => 'admin.dashboard',
                    'icon' => 'bx bx-home-circle',
                ],
                auth()->user()?->isSuperAdmin() ? [
                    'label' => 'Maktablar',
                    'route' => route('admin.schools.index'),
                    'match' => 'admin.schools.*',
                    'icon' => 'bx bx-buildings',
                ] : null,
                [
                    'label' => 'Maktab haqida',
                    'route' => route('admin.school-info.edit'),
                    'match' => 'admin.school-info.*',
                    'icon' => 'bx bx-info-circle',
                ],
                [
                    'label' => 'Bot sozlamalari',
                    'route' => route('admin.settings.edit'),
                    'match' => 'admin.settings.*',
                    'icon' => 'bx bx-cog',
                ],
            ])),
        ],
        [
            'title' => 'Vakansiya',
            'items' => [
                [
                    'label' => 'Vakansiyalar',
                    'route' => route('admin.vacancies.index'),
                    'match' => 'admin.vacancies.*',
                    'icon' => 'bx bx-briefcase-alt-2',
                ],
                [
                    'label' => 'Vakansiya arizalari',
                    'route' => route('admin.vacancy-applications.index'),
                    'match' => 'admin.vacancy-applications.*',
                    'icon' => 'bx bx-user-pin',
                ],
            ],
        ],
        [
            'title' => 'Olimpiada',
            'items' => [
                [
                    'label' => 'Olimpiadalar',
                    'route' => route('admin.olympiads.index'),
                    'match' => 'admin.olympiads.*',
                    'icon' => 'bx bx-trophy',
                ],
                [
                    'label' => 'Olimpiada arizalari',
                    'route' => route('admin.olympiad-registrations.index'),
                    'match' => 'admin.olympiad-registrations.*',
                    'icon' => 'bx bx-list-check',
                ],
            ],
        ],
        [
            'title' => 'Qabul',
            'items' => [
                [
                    'label' => 'Qabul',
                    'route' => route('admin.admissions.index'),
                    'match' => 'admin.admissions.*',
                    'icon' => 'bx bx-notepad',
                ],
                [
                    'label' => 'Qabul arizalari',
                    'route' => route('admin.admission-applications.index'),
                    'match' => 'admin.admission-applications.*',
                    'icon' => 'bx bx-user-check',
                ],
            ],
        ],
        [
            'title' => 'Qo`shimcha',
            'items' => [
                [
                    'label' => 'Foydalanuvchilar',
                    'route' => route('admin.telegram-users.index'),
                    'match' => 'admin.telegram-users.*',
                    'icon' => 'bx bx-user',
                ],
                [
                    'label' => 'E`lonlar',
                    'route' => route('admin.announcements.index'),
                    'match' => 'admin.announcements.*',
                    'icon' => 'mdi mdi-bullhorn-outline',
                ],
                [
                    'label' => 'Majburiy obuna',
                    'route' => route('admin.channels.index'),
                    'match' => 'admin.channels.*',
                    'icon' => 'bx bx-link',
                ],
            ],
        ],
    ];
@endphp

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div class="navbar-brand-box">
            <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('skote/assets/images/logo.svg') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('skote/assets/images/logo-dark.png') }}" alt="" height="17">
                </span>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('skote/assets/images/logo-light.svg') }}" alt="" height="22">
                </span>
                <span class="logo-lg text-white fw-bold fs-5">Larabot School</span>
            </a>
        </div>

        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                @foreach ($menuGroups as $group)
                    <li class="menu-title">{{ $group['title'] }}</li>

                    @foreach ($group['items'] as $item)
                        @php($isActive = $item['match'] ? request()->routeIs($item['match']) : false)

                        <li>
                            @if ($item['route'])
                                <a href="{{ $item['route'] }}" class="waves-effect {{ $isActive ? 'active' : '' }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                    @if (! empty($item['badge']))
                                        <span class="badge rounded-pill bg-secondary float-end">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            @else
                                <a href="javascript:void(0);" class="waves-effect">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                    @if (! empty($item['badge']))
                                        <span class="badge rounded-pill bg-secondary float-end">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
</div>
