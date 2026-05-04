@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Maktab haqida',
                        'subtitle' => 'Maktab ma\'lumotlarini tahrirlash',
                    ])

                    @include('admin.partials.flash')

                    <div class="alert alert-info">
                        Tahrir qilinayotgan maktab: <strong>{{ $school->name }}</strong>
                    </div>

                    <form method="post" action="{{ route('admin.school-info.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <!-- Tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#about" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">📖 Umumiy ma'lumot</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">📜 Tarix</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#mission" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">🎯 Missiya va Viziya</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#director" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">👨‍💼 Direktor</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#media" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">📸 Media</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#contact" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">📞 Kontaktlar</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <!-- About Tab -->
                            <div class="tab-pane active" id="about" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Maktab haqida umumiy ma'lumot</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Maktab haqida (O'zbek) <span class="text-danger">*</span></label>
                                            <textarea name="about_text_uz" class="form-control" rows="8">{{ old('about_text_uz', $info->about_text_uz) }}</textarea>
                                            @error('about_text_uz')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Maktab haqida (Rus)</label>
                                            <textarea name="about_text_ru" class="form-control" rows="8">{{ old('about_text_ru', $info->about_text_ru) }}</textarea>
                                            @error('about_text_ru')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane" id="history" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Maktab tarixi</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Tarix (O'zbek)</label>
                                            <textarea name="history_text_uz" class="form-control" rows="8">{{ old('history_text_uz', $info->history_text_uz) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tarix (Rus)</label>
                                            <textarea name="history_text_ru" class="form-control" rows="8">{{ old('history_text_ru', $info->history_text_ru) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mission & Vision Tab -->
                            <div class="tab-pane" id="mission" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Missiya va Viziya</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Missiya (O'zbek)</label>
                                                    <textarea name="mission_text_uz" class="form-control" rows="5">{{ old('mission_text_uz', $info->mission_text_uz) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Missiya (Rus)</label>
                                                    <textarea name="mission_text_ru" class="form-control" rows="5">{{ old('mission_text_ru', $info->mission_text_ru) }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Viziya (O'zbek)</label>
                                                    <textarea name="vision_text_uz" class="form-control" rows="5">{{ old('vision_text_uz', $info->vision_text_uz) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Viziya (Rus)</label>
                                                    <textarea name="vision_text_ru" class="form-control" rows="5">{{ old('vision_text_ru', $info->vision_text_ru) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Director Tab -->
                            <div class="tab-pane" id="director" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Direktor ma'lumotlari</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Direktor FIO</label>
                                            <input type="text" name="director_name" class="form-control" value="{{ old('director_name', $info->director_name) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Direktor rasmi</label>
                                            @if($info->director_photo)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $info->director_photo) }}" alt="Director" class="img-thumbnail" style="max-width: 200px;">
                                                </div>
                                            @endif
                                            <input type="file" name="director_photo" class="form-control" accept="image/*">
                                            <small class="text-muted">Max: 5MB. Format: JPG, PNG</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Direktor xabari (O'zbek)</label>
                                            <textarea name="director_message_uz" class="form-control" rows="5">{{ old('director_message_uz', $info->director_message_uz) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Direktor xabari (Rus)</label>
                                            <textarea name="director_message_ru" class="form-control" rows="5">{{ old('director_message_ru', $info->director_message_ru) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media Tab -->
                            <div class="tab-pane" id="media" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Galereya va Video</h5>

                                        <div class="mb-4">
                                            <label class="form-label">Galereya rasmlari</label>

                                            @if($info->gallery_images && count($info->gallery_images) > 0)
                                                <div class="row mb-3">
                                                    @foreach($info->gallery_images as $image)
                                                        <div class="col-md-3 mb-3">
                                                            <div class="card">
                                                                <img src="{{ asset('storage/' . $image) }}" class="card-img-top" alt="Gallery">
                                                                <div class="card-body p-2">
                                                                    <form method="post" action="{{ route('admin.school-info.gallery.delete') }}" class="d-inline">
                                                                        @csrf
                                                                        @method('delete')
                                                                        <input type="hidden" name="image_path" value="{{ $image }}">
                                                                        <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('O\'chirmoqchimisiz?')">
                                                                            <i class="fas fa-trash"></i> O'chirish
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                            <small class="text-muted">Bir nechta rasm tanlash mumkin. Max: 5MB har biri.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Video URL (YouTube, Vimeo)</label>
                                            <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $info->video_url) }}" placeholder="https://youtube.com/watch?v=...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Tab -->
                            <div class="tab-pane" id="contact" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Kontakt ma'lumotlari</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Telefon</label>
                                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $info->contact_phone) }}" placeholder="+998901234567">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $info->contact_email) }}" placeholder="info@school.uz">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Manzil (O'zbek)</label>
                                            <textarea name="address_uz" class="form-control" rows="3">{{ old('address_uz', $info->address_uz) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Manzil (Rus)</label>
                                            <textarea name="address_ru" class="form-control" rows="3">{{ old('address_ru', $info->address_ru) }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Kenglik (Latitude)</label>
                                                    <input type="number" step="0.00000001" name="map_latitude" class="form-control" value="{{ old('map_latitude', $info->map_latitude) }}" placeholder="41.2995">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Uzunlik (Longitude)</label>
                                                    <input type="number" step="0.00000001" name="map_longitude" class="form-control" value="{{ old('map_longitude', $info->map_longitude) }}" placeholder="69.2401">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Koordinatalarni <a href="https://www.google.com/maps" target="_blank">Google Maps</a> dan olishingiz mumkin.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="fas fa-save"></i> Saqlash
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Bekor qilish
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
