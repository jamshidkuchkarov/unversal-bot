@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => $admission->exists ? 'Qabulni tahrirlash' : 'Yangi qabul yaratish',
                        'subtitle' => 'Qabul kampaniyasi sozlamalari',
                    ])
                    @include('admin.partials.flash')

                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ $admission->exists ? route('admin.admissions.update', $admission) : route('admin.admissions.store') }}">
                                @csrf
                                @if($admission->exists) @method('put') @endif

                                <!-- Asosiy ma'lumotlar -->
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">📋 Asosiy ma'lumotlar</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kampaniya nomi <span class="text-danger">*</span></label>
                                                <input class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $admission->title) }}" placeholder="Masalan: 2024-2025 o'quv yili qabuli" required>
                                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">O'quv yili <span class="text-danger">*</span></label>
                                                <input class="form-control @error('academic_year') is-invalid @enderror" name="academic_year" value="{{ old('academic_year', $admission->academic_year) }}" placeholder="2024-2025" required>
                                                @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Kvota (ixtiyoriy)</label>
                                                <input class="form-control @error('quota') is-invalid @enderror" type="number" name="quota" value="{{ old('quota', $admission->quota) }}" placeholder="100">
                                                @error('quota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tavsif</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="Qabul kampaniyasi haqida qisqacha ma'lumot">{{ old('description', $admission->description) }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Qabul qilinadigan sinflar -->
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">🎓 Qabul qilinadigan sinflar</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Sinflar ro'yxati <span class="text-danger">*</span></label>
                                        <input class="form-control @error('target_classes') is-invalid @enderror" name="target_classes" value="{{ old('target_classes', implode(', ', $admission->target_classes ?? [])) }}" placeholder="1, 2, 3, 4, 5" required>
                                        <small class="text-muted">💡 Faqat raqamlarni vergul bilan ajrating. Masalan: <code>1, 2, 3</code></small>
                                        @error('target_classes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Ta'lim tili -->
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">🗣 Ta'lim tili</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Mavjud tillar <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="admission_options[]" value="uz" id="lang_uz"
                                                    @checked(in_array('uz', old('admission_options', $admission->admission_options ?? [])))>
                                                <label class="form-check-label" for="lang_uz">
                                                    🇺🇿 O'zbek tili
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="admission_options[]" value="ru" id="lang_ru"
                                                    @checked(in_array('ru', old('admission_options', $admission->admission_options ?? [])))>
                                                <label class="form-check-label" for="lang_ru">
                                                    🇷🇺 Rus tili
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted">💡 Kamida bitta tilni tanlang</small>
                                        @error('admission_options')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Talablar va hujjatlar -->
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">📄 Talablar va hujjatlar</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Talablar</label>
                                        <textarea class="form-control @error('requirements') is-invalid @enderror" name="requirements" rows="4" placeholder="Qabul uchun zarur talablar va shartlar">{{ old('requirements', $admission->requirements) }}</textarea>
                                        @error('requirements')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kerakli hujjatlar</label>
                                        <input class="form-control @error('required_documents') is-invalid @enderror" name="required_documents" value="{{ old('required_documents', implode(', ', $admission->required_documents ?? [])) }}" placeholder="passport_copy, birth_certificate, photo">
                                        <small class="text-muted">💡 Vergul bilan ajrating. Masalan: <code>passport_copy, birth_certificate, photo</code></small>
                                        @error('required_documents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Muddatlar va status -->
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">📅 Muddatlar va status</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Boshlanish sanasi <span class="text-danger">*</span></label>
                                                <input class="form-control @error('start_date') is-invalid @enderror" type="date" name="start_date" value="{{ old('start_date', $admission->start_date?->format('Y-m-d')) }}" required>
                                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Tugash sanasi <span class="text-danger">*</span></label>
                                                <input class="form-control @error('end_date') is-invalid @enderror" type="date" name="end_date" value="{{ old('end_date', $admission->end_date?->format('Y-m-d')) }}" required>
                                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                                    <option value="draft" @selected(old('status', $admission->status) === 'draft')>📝 Qoralama</option>
                                                    <option value="published" @selected(old('status', $admission->status) === 'published')>✅ Faol</option>
                                                    <option value="closed" @selected(old('status', $admission->status) === 'closed')>❌ Yopilgan</option>
                                                    <option value="completed" @selected(old('status', $admission->status) === 'completed')>📦 Yakunlangan</option>
                                                </select>
                                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light border">
                                    <div class="fw-semibold mb-2">Bot qabul formasida olinadigan ma`lumotlar</div>
                                    <div class="text-muted">
                                        O`quvchi F.I.O, topshirayotgan sinf, ta`lim tili, qaysi maktabdan kelayotgani, tug`ilgan sana, yashash manzili, ota-ona F.I.O, telefonlar va o`tish sababi.
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="mdi mdi-content-save me-1"></i>Saqlash
                                    </button>
                                    <a href="{{ route('admin.admissions.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i>Bekor qilish
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
