<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Admin Panel' }} | Larabot School</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Universal school bot admin panel" name="description" />
    <link rel="shortcut icon" href="{{ asset('skote/assets/images/favicon.ico') }}">
    <link id="bootstrap-style" href="{{ asset('skote/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('skote/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link id="app-style" href="{{ asset('skote/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .page-content { padding: calc(70px + 24px) 12px 60px; }
        .page-title-box .page-title-right { float: none; }
        .table td, .table th { vertical-align: middle; }
        .auth-full-page-content { min-height: auto; }
        .metric-card .avatar-title { font-size: 1.15rem; }
        .form-check.form-switch { padding-left: 0; }
        .form-check.form-switch .form-check-input { margin-left: 0; float: none; }
        .bot-note { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; border-radius: .5rem; padding: .875rem 1rem; }
        .theme-toggle-btn {
            border: 1px solid rgba(85, 110, 230, .14);
            border-radius: 999px;
            padding: 0 .75rem;
            height: 44px;
            background: linear-gradient(135deg, rgba(85, 110, 230, .08), rgba(52, 195, 143, .08));
        }
        .theme-toggle-btn i { font-size: 1rem; }
        .theme-option.active {
            background-color: rgba(85, 110, 230, .1);
            color: #556ee6;
            font-weight: 600;
        }
        .tox-tinymce {
            border-radius: .75rem !important;
            border-color: #dbe4f0 !important;
        }
    </style>
    @stack('styles')
</head>
<body data-sidebar="dark">
@yield('body')

<script src="{{ asset('skote/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('skote/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('skote/assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('skote/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('skote/assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('skote/assets/js/app.js') }}"></script>
    <script>
        (function () {
            const bootstrapLink = document.getElementById('bootstrap-style');
            const appLink = document.getElementById('app-style');
            const storageKey = 'admin-theme-mode';
            const darkValue = 'dark';
            const lightValue = 'light';

            function applyTheme(theme) {
                const isDark = theme === darkValue;

                bootstrapLink.setAttribute('href', isDark
                    ? "{{ asset('skote/assets/css/bootstrap-dark.min.css') }}"
                    : "{{ asset('skote/assets/css/bootstrap.min.css') }}");
                appLink.setAttribute('href', isDark
                    ? "{{ asset('skote/assets/css/app-dark.min.css') }}"
                    : "{{ asset('skote/assets/css/app.min.css') }}");

                document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                document.body.classList.toggle('theme-dark', isDark);
                localStorage.setItem(storageKey, theme);
                sessionStorage.setItem('is_visited', isDark ? 'dark-mode-switch' : 'light-mode-switch');

                const toggle = document.getElementById('theme-mode-toggle');
                if (toggle) {
                    toggle.setAttribute('data-theme', theme);

                    const icon = toggle.querySelector('i');
                    const label = toggle.querySelector('.theme-toggle-label');

                    if (icon) {
                        icon.className = isDark ? 'mdi mdi-weather-night' : 'mdi mdi-white-balance-sunny';
                    }

                    if (label) {
                        label.textContent = isDark ? 'Dark mode' : 'Light mode';
                    }
                }

                document.querySelectorAll('[data-theme-choice]').forEach(function (option) {
                    option.classList.toggle('active', option.getAttribute('data-theme-choice') === theme);
                });
            }

            window.applyAdminTheme = applyTheme;

            document.addEventListener('DOMContentLoaded', function () {
                const savedTheme = localStorage.getItem(storageKey) || lightValue;

                applyTheme(savedTheme);

                const toggle = document.getElementById('theme-mode-toggle');
                if (toggle) {
                    toggle.setAttribute('data-theme', savedTheme);
                }

                document.querySelectorAll('[data-theme-choice]').forEach(function (option) {
                    option.addEventListener('click', function () {
                        applyTheme(option.getAttribute('data-theme-choice') || lightValue);
                    });
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
