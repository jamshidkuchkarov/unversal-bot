<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box d-lg-none">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('skote/assets/images/logo.svg') }}" alt="" height="22">
                    </span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('skote/assets/images/logo-light.svg') }}" alt="" height="22">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="d-none d-lg-flex align-items-center ms-3">
                <div>
                    <h5 class="mb-0">Universal School Bot</h5>
                    <p class="text-muted mb-0 font-size-12">
                        {{ isset($currentSchool) && $currentSchool ? $currentSchool->name : 'Admin panel va Telegram boshqaruvi' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button
                    type="button"
                    id="theme-mode-toggle"
                    class="btn header-item waves-effect theme-toggle-btn"
                    data-theme="light"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i class="mdi mdi-white-balance-sunny"></i>
                    <span class="d-none d-lg-inline-block ms-1 theme-toggle-label">Theme</span>
                    <i class="mdi mdi-chevron-down d-none d-lg-inline-block ms-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <button type="button" class="dropdown-item theme-option" data-theme-choice="light">
                        <i class="mdi mdi-white-balance-sunny font-size-16 align-middle me-1"></i> Light mode
                    </button>
                    <button type="button" class="dropdown-item theme-option" data-theme-choice="dark">
                        <i class="mdi mdi-weather-night font-size-16 align-middle me-1"></i> Dark mode
                    </button>
                </div>
            </div>

            @if (auth()->user()?->isSuperAdmin() && isset($availableSchools) && $availableSchools->count())
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-buildings me-1"></i>
                        <span class="d-none d-xl-inline-block">{{ $currentSchool?->name ?? 'Maktab tanlang' }}</span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @foreach ($availableSchools as $schoolOption)
                            <a href="{{ request()->fullUrlWithQuery(['school_id' => $schoolOption->id]) }}" class="dropdown-item">
                                {{ $schoolOption->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-xl-inline-block ms-1">{{ auth()->user()?->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-item-text">
                        <div class="fw-semibold">{{ auth()->user()?->email }}</div>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        <i class="bx bx-user font-size-16 align-middle me-1"></i> Profil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> Chiqish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
