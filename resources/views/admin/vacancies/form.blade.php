@extends('layouts.admin')

@php
    $categoryOptions = [
        'teacher' => 'O`qituvchi',
        'technical' => 'Texnik xodim',
        'management' => 'Boshqaruv',
        'other' => 'Boshqa',
    ];

    $statusOptions = [
        'draft' => 'Qoralama',
        'published' => 'Faol',
        'closed' => 'Yopilgan',
        'archived' => 'Arxiv',
    ];

    $wizardFields = [
        ['icon' => 'mdi mdi-account-outline', 'label' => 'F.I.O va aloqa', 'text' => 'Ism familiya va telefon'],
        ['icon' => 'mdi mdi-target-account', 'label' => 'Lavozim', 'text' => 'Qaysi yo`nalish uchun ariza berayotgani'],
        ['icon' => 'mdi mdi-cake-variant-outline', 'label' => 'Tug`ilgan sana', 'text' => 'Nomzodning tug`ilgan sanasi'],
        ['icon' => 'mdi mdi-map-marker-outline', 'label' => 'Manzil', 'text' => 'Yashash manzili'],
        ['icon' => 'mdi mdi-briefcase-account-outline', 'label' => 'Ish tajribasi', 'text' => 'Qayerda ishlagani va necha yil tajribasi borligi'],
        ['icon' => 'mdi mdi-school-outline', 'label' => 'Ta`lim va sertifikatlar', 'text' => 'Universitet, yo`nalish, IELTS, CEFR va boshqalar'],
        ['icon' => 'mdi mdi-brain', 'label' => 'Ko`nikma va yutuqlar', 'text' => 'Til bilimi, kompyuter, fan va yutuqlari'],
        ['icon' => 'mdi mdi-star-outline', 'label' => 'O`zi haqida', 'text' => '1-2 gap bilan o`zini tanishtiradi'],
    ];
@endphp

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => $vacancy->exists ? 'Vakansiyani tahrirlash' : 'Vakansiya yaratish',
                        'subtitle' => 'Vakansiya matni, talablari va botdagi ariza oqimini bir joyda tayyorlang.',
                    ])
                    @include('admin.partials.flash')

                    <form method="post" action="{{ $vacancy->exists ? route('admin.vacancies.update', $vacancy) : route('admin.vacancies.store') }}">
                        @csrf
                        @if($vacancy->exists) @method('put') @endif

                        <div class="row g-4">
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-briefcase-edit-outline font-size-24"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">Asosiy ma`lumotlar</h5>
                                                <p class="text-muted mb-0">Vakansiya kanalga chiqadigan va bot ichida ko`rinadigan asosiy tavsiflar.</p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><i class="mdi mdi-format-title me-1 text-primary"></i> Sarlavha</label>
                                            <input class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $vacancy->title) }}" placeholder="Masalan: Matematika o`qituvchisi" required>
                                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><i class="mdi mdi-text-box-outline me-1 text-primary"></i> Tavsif</label>
                                            <textarea id="description-editor" class="form-control @error('description') is-invalid @enderror" name="description" rows="10" placeholder="Vakansiya haqida batafsil ma`lumot yozing">{{ old('description', $vacancy->description) }}</textarea>
                                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-shape-outline me-1 text-primary"></i> Kategoriya</label>
                                                    <select class="form-select @error('category') is-invalid @enderror" name="category">
                                                        @foreach ($categoryOptions as $value => $label)
                                                            <option value="{{ $value }}" @selected(old('category', $vacancy->category) === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-book-open-page-variant-outline me-1 text-primary"></i> Fan</label>
                                                    <input class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject', $vacancy->subject) }}" placeholder="Masalan: Matematika">
                                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-calendar-clock-outline me-1 text-primary"></i> Ish jadvali</label>
                                                    <input class="form-control @error('work_schedule') is-invalid @enderror" name="work_schedule" value="{{ old('work_schedule', $vacancy->work_schedule) }}" placeholder="To`liq stavka / Part-time">
                                                    @error('work_schedule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i> Talablar</label>
                                            <textarea class="form-control @error('requirements') is-invalid @enderror" name="requirements" rows="4" placeholder="Nomzoddan nimalar talab qilinadi">{{ old('requirements', $vacancy->requirements) }}</textarea>
                                            @error('requirements')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label"><i class="mdi mdi-office-building-cog-outline me-1 text-primary"></i> Ish sharoitlari</label>
                                            <textarea class="form-control @error('conditions') is-invalid @enderror" name="conditions" rows="4" placeholder="Ish sharoiti, bonuslar, qo`shimcha qulayliklar">{{ old('conditions', $vacancy->conditions) }}</textarea>
                                            @error('conditions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-cash-register font-size-24"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">Taklif va muddatlar</h5>
                                                <p class="text-muted mb-0">Maosh, deadline va vakansiya holatini to`ldiring.</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-currency-usd me-1 text-success"></i> Min maosh</label>
                                                    <input class="form-control @error('salary_min') is-invalid @enderror" type="number" step="0.01" name="salary_min" value="{{ old('salary_min', $vacancy->salary_min) }}" placeholder="0">
                                                    @error('salary_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-currency-usd me-1 text-success"></i> Max maosh</label>
                                                    <input class="form-control @error('salary_max') is-invalid @enderror" type="number" step="0.01" name="salary_max" value="{{ old('salary_max', $vacancy->salary_max) }}" placeholder="0">
                                                    @error('salary_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-note-text-outline me-1 text-success"></i> Maosh izohi</label>
                                                    <input class="form-control @error('salary_note') is-invalid @enderror" name="salary_note" value="{{ old('salary_note', $vacancy->salary_note) }}" placeholder="Kelishiladi / Bonus bor">
                                                    @error('salary_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label"><i class="mdi mdi-calendar-end me-1 text-success"></i> Deadline</label>
                                                    <input class="form-control @error('deadline') is-invalid @enderror" type="date" name="deadline" value="{{ old('deadline', $vacancy->deadline?->format('Y-m-d')) }}">
                                                    @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-0">
                                                    <label class="form-label"><i class="mdi mdi-radar me-1 text-success"></i> Holat</label>
                                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                                        @foreach ($statusOptions as $value => $label)
                                                            <option value="{{ $value }}" @selected(old('status', $vacancy->status) === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="card border-primary border-opacity-25">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-robot-love-outline font-size-24"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">Bot nimalarni so`raydi?</h5>
                                                <p class="text-muted mb-0">Ariza boshlanganda foydalanuvchidan quyidagi ma`lumotlar olinadi.</p>
                                            </div>
                                        </div>

                                        <div class="vstack gap-2">
                                            @foreach ($wizardFields as $field)
                                                <div class="border rounded-3 px-3 py-2">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <i class="{{ $field['icon'] }} text-primary font-size-18"></i>
                                                        <span class="fw-semibold">{{ $field['label'] }}</span>
                                                    </div>
                                                    <div class="text-muted font-size-13">{{ $field['text'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Tezkor tavsiya</h5>
                                        <div class="alert alert-light border mb-0">
                                            <div class="d-flex gap-2">
                                                <i class="mdi mdi-lightbulb-on-outline text-warning font-size-22"></i>
                                                <div class="text-muted">
                                                    `Published` holatidagi vakansiyalar botda ko`rinadi. Ro`yxat oxirida bot avtomatik `Zahira vakansiya` tugmasini ham chiqaradi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light">
                                        <i class="mdi mdi-content-save-outline me-1"></i> Saqlash
                                    </button>
                                    <a href="{{ route('admin.vacancies.index') }}" class="btn btn-light border">
                                        <i class="mdi mdi-arrow-left me-1"></i> Ro`yxatga qaytish
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('skote/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof tinymce === 'undefined') {
                return;
            }

            tinymce.remove('#description-editor');
            tinymce.init({
                selector: '#description-editor',
                height: 320,
                menubar: false,
                branding: false,
                plugins: 'lists link table code autoresize fullscreen',
                toolbar: 'undo redo | blocks | bold italic underline forecolor | alignleft aligncenter alignright | bullist numlist | link table | removeformat code fullscreen',
                toolbar_mode: 'sliding',
                content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.65; }',
                skin: document.body.classList.contains('theme-dark') ? 'oxide-dark' : 'oxide',
                content_css: document.body.classList.contains('theme-dark') ? 'dark' : 'default',
                setup: function (editor) {
                    const syncTheme = function () {
                        const isDark = document.body.classList.contains('theme-dark');

                        editor.options.set('skin', isDark ? 'oxide-dark' : 'oxide');
                        editor.options.set('content_css', isDark ? 'dark' : 'default');
                    };

                    document.getElementById('theme-mode-toggle')?.addEventListener('click', function () {
                        setTimeout(function () {
                            tinymce.remove('#description-editor');
                            tinymce.init({
                                selector: '#description-editor',
                                height: 320,
                                menubar: false,
                                branding: false,
                                plugins: 'lists link table code autoresize fullscreen',
                                toolbar: 'undo redo | blocks | bold italic underline forecolor | alignleft aligncenter alignright | bullist numlist | link table | removeformat code fullscreen',
                                toolbar_mode: 'sliding',
                                content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.65; }',
                                skin: document.body.classList.contains('theme-dark') ? 'oxide-dark' : 'oxide',
                                content_css: document.body.classList.contains('theme-dark') ? 'dark' : 'default',
                            });
                        }, 120);
                    });

                    editor.on('init', syncTheme);
                },
            });
        });
    </script>
@endpush
