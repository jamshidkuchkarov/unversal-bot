@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content"><div class="page-content"><div class="container-fluid">
            @include('admin.partials.page-header', [
                'title' => $announcement->exists ? 'E\'lonni tahrirlash' : 'Yangi e\'lon yaratish',
                'subtitle' => 'Foydalanuvchilarga, kanal yoki guruhga xabar yuborish',
            ])
            @include('admin.partials.flash')

            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($announcement->exists) @method('put') @endif

                        <!-- Asosiy ma'lumotlar -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">📝 Asosiy ma'lumotlar</h5>

                            <div class="mb-3">
                                <label class="form-label">E'lon nomi (ixtiyoriy)</label>
                                <input class="form-control" name="title" value="{{ old('title', $announcement->title) }}" placeholder="Masalan: Yangi dars jadvali">
                                <small class="text-muted">Bu nom faqat admin panelda ko'rinadi, foydalanuvchilarga ko'rinmaydi</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Xabar matni <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="message_text" rows="6" required placeholder="Foydalanuvchilarga yuboriladigan xabar matnini kiriting...">{{ old('message_text', $announcement->message_text) }}</textarea>
                                <small class="text-muted">Bu matn barcha foydalanuvchilarga yuboriladi</small>
                            </div>
                        </div>

                        <!-- Rasm va fayllar -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">🖼️ Rasm yoki fayl qo'shish (ixtiyoriy)</h5>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Rasm, video yoki hujjat yuklash</label>
                                        <input type="file" class="form-control" name="media_files[]" id="media_files" multiple accept="image/*,video/*,.pdf,.doc,.docx">
                                        <small class="text-muted">⚠️ Rasm kamida 200x200 pixel bo'lishi kerak. Aks holda faqat matn yuboriladi.</small>

                                        @if($announcement->exists && $announcement->media_files)
                                            <div class="mt-2 p-2 bg-light rounded">
                                                <strong>Hozirgi fayllar:</strong>
                                                <ul class="list-unstyled mb-2">
                                                    @foreach($announcement->media_files as $file)
                                                        <li class="mt-1">
                                                            📎 <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="replace_media" value="1" id="replace_media">
                                                    <label class="form-check-label" for="replace_media">
                                                        Eski fayllarni yangilariga almashtirish
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Media turi</label>
                                        <select class="form-select" name="media_type" id="media_type">
                                            <option value="none" @selected(old('media_type', $announcement->media_type) === 'none')>🚫 Yo'q</option>
                                            <option value="photo" @selected(old('media_type', $announcement->media_type) === 'photo')>🖼️ Rasm</option>
                                            <option value="video" @selected(old('media_type', $announcement->media_type) === 'video')>🎥 Video</option>
                                            <option value="document" @selected(old('media_type', $announcement->media_type) === 'document')>📄 Hujjat</option>
                                            <option value="animation" @selected(old('media_type', $announcement->media_type) === 'animation')>🎬 GIF</option>
                                        </select>
                                        <small class="text-muted">Avtomatik aniqlanadi</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tugmalar -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">🔘 Tugmalar qo'shish (ixtiyoriy)</h5>

                            <div class="mb-3">
                                <label class="form-label">Xabar ostidagi tugmalar</label>
                                <textarea class="form-control font-monospace" name="inline_buttons" rows="5" placeholder='[
  [{"text": "📖 Batafsil ma\'lumot", "url": "https://example.com"}],
  [{"text": "✍️ Ro\'yxatdan o\'tish", "url": "https://example.com/register"}]
]'>{{ old('inline_buttons', $announcement->inline_buttons ? json_encode($announcement->inline_buttons, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                                <small class="text-muted">
                                    💡 Har bir qator - alohida tugma qatori. <code>text</code> - tugma yozuvi, <code>url</code> - havola manzili
                                </small>
                            </div>
                        </div>

                        <!-- Kimga yuborish -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">👥 Kimga yuborish</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Qabul qiluvchilar <span class="text-danger">*</span></label>
                                        <select class="form-select" name="target_type" id="target_type" required>
                                            <option value="all_users" @selected(old('target_type', $announcement->target_type) === 'all_users')>📢 Barcha foydalanuvchilarga</option>
                                            <option value="channel" @selected(old('target_type', $announcement->target_type) === 'channel')>📺 Kanalga yuborish</option>
                                            <option value="group" @selected(old('target_type', $announcement->target_type) === 'group')>👥 Guruhga yuborish</option>
                                            <option value="specific_users" @selected(old('target_type', $announcement->target_type) === 'specific_users')>👤 Tanlangan foydalanuvchilarga</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Holat</label>
                                        <select class="form-select" name="status">
                                            <option value="draft" @selected(old('status', $announcement->status) === 'draft')>📝 Qoralama (yuborilmagan)</option>
                                            <option value="scheduled" @selected(old('status', $announcement->status) === 'scheduled')>⏰ Rejalashtirilgan</option>
                                        </select>
                                        <small class="text-muted">Odatda "Qoralama" tanlang</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="channel_group_fields" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Kanal yoki guruh ID raqami</label>
                                        <input class="form-control" name="target_channel" value="{{ old('target_channel', $announcement->target_channel) }}" placeholder="-1001234567890">
                                        <small class="text-muted">💡 Kanal ID raqamini olish uchun: Admin → Kanallar → "Chat ID olish" tugmasini bosing</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="specific_users_fields" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Foydalanuvchi ID raqamlari</label>
                                        <input class="form-control" name="target_user_ids" value="{{ old('target_user_ids', implode(', ', $announcement->target_user_ids ?? [])) }}" placeholder="123456789, 987654321">
                                        <small class="text-muted">Vergul bilan ajratib kiriting</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vaqt sozlamalari -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">⏰ Vaqt sozlamalari (ixtiyoriy)</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Yuborish vaqti</label>
                                        <input class="form-control" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $announcement->scheduled_at?->format('Y-m-d\\TH:i')) }}">
                                        <small class="text-muted">Bo'sh qoldirsangiz darhol yuboriladi</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Takrorlanuvchi e'lon</label>
                                        <select class="form-select" name="is_recurring" id="is_recurring">
                                            <option value="0" @selected(old('is_recurring', $announcement->is_recurring) == 0)>❌ Yo'q (bir marta)</option>
                                            <option value="1" @selected(old('is_recurring', $announcement->is_recurring) == 1)>✅ Ha (har kuni/hafta)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="recurring_schedule_field" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Takrorlanish jadvali (Cron format)</label>
                                        <input class="form-control font-monospace" name="recurring_schedule" value="{{ old('recurring_schedule', $announcement->recurring_schedule) }}" placeholder="0 9 * * 1">
                                        <small class="text-muted">
                                            Misollar: <code>0 9 * * *</code> = har kuni 9:00,
                                            <code>0 9 * * 1</code> = har dushanba 9:00
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-content-save"></i> Saqlash
                            </button>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Bekor qilish
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Show/hide fields based on target type
                document.getElementById('target_type').addEventListener('change', function() {
                    const channelFields = document.getElementById('channel_group_fields');
                    const specificFields = document.getElementById('specific_users_fields');

                    channelFields.style.display = 'none';
                    specificFields.style.display = 'none';

                    if (this.value === 'channel' || this.value === 'group') {
                        channelFields.style.display = 'block';
                    } else if (this.value === 'specific_users') {
                        specificFields.style.display = 'block';
                    }
                });

                // Show/hide recurring schedule field
                document.getElementById('is_recurring').addEventListener('change', function() {
                    const scheduleField = document.getElementById('recurring_schedule_field');
                    scheduleField.style.display = this.value === '1' ? 'block' : 'none';
                });

                // Auto-detect media type from file
                document.getElementById('media_files').addEventListener('change', function() {
                    const files = this.files;
                    const mediaTypeSelect = document.getElementById('media_type');

                    if (files.length > 0) {
                        const firstFile = files[0];
                        const fileType = firstFile.type;

                        if (fileType.startsWith('image/')) {
                            if (fileType === 'image/gif') {
                                mediaTypeSelect.value = 'animation';
                            } else {
                                mediaTypeSelect.value = 'photo';
                            }
                        } else if (fileType.startsWith('video/')) {
                            mediaTypeSelect.value = 'video';
                        } else {
                            mediaTypeSelect.value = 'document';
                        }
                    } else {
                        mediaTypeSelect.value = 'none';
                    }
                });

                // Trigger on page load
                document.getElementById('target_type').dispatchEvent(new Event('change'));
                document.getElementById('is_recurring').dispatchEvent(new Event('change'));
            </script>
        </div></div></div>
    </div>
@endsection
