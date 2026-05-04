@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => 'Bot sozlamalari',
                        'subtitle' => 'Tanlangan maktab uchun token, kanal, username va bot matnlarini boshqarish.',
                    ])

                    @include('admin.partials.flash')

                    <div class="alert alert-info">
                        Hozir tahrir qilinayotgan maktab: <strong>{{ $schoolModel->name }}</strong>
                    </div>

                    <div class="alert {{ $schoolBot->webhook_set ? 'alert-success' : 'alert-warning' }}">
                        Webhook holati:
                        <strong>{{ $schoolBot->webhook_set ? 'Ulangan' : 'Ulanmagan' }}</strong>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.settings.update') }}">
                                @csrf
                                @method('put')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Maktab nomi</label>
                                            <input id="name" name="name" class="form-control" value="{{ old('name', $schoolModel->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="slug" class="form-label">Slug</label>
                                            <input id="slug" name="slug" class="form-control" value="{{ old('slug', $schoolModel->slug) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Shahar</label><input class="form-control" name="city" value="{{ old('city', $schoolModel->city) }}"></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Tuman</label><input class="form-control" name="district" value="{{ old('district', $schoolModel->district) }}"></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Direktor</label><input class="form-control" name="director_name" value="{{ old('director_name', $schoolModel->director_name) }}"></div></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Telefon</label><input class="form-control" name="phone" value="{{ old('phone', $schoolModel->phone) }}"></div></div>
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" value="{{ old('email', $schoolModel->email) }}"></div></div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Manzil</label>
                                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $schoolModel->address) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="bot_name" class="form-label">Bot nomi</label>
                                    <input id="bot_name" name="bot_name" class="form-control" value="{{ old('bot_name', $schoolBot->bot_name) }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Bot token</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="bot_token" id="bot_token" value="{{ old('bot_token', $schoolBot->bot_token) }}" placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                                                <button class="btn btn-outline-secondary" type="button" id="toggleToken">
                                                    <i class="bx bx-show" id="toggleIcon"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">@BotFather dan olingan token. Xavfsizlik uchun yashirilgan.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Webhook URL</label>
                                            <input class="form-control" value="{{ $schoolBot->webhook_url ?: $resolvedWebhookUrl ?: 'Bot token saqlangandan keyin hosil bo`ladi' }}" readonly>
                                            @if($schoolBot->exists && $schoolBot->bot_token)
                                                <button type="button" class="btn btn-sm btn-info mt-2" id="testWebhook">
                                                    <i class="bx bx-test-tube me-1"></i> Webhook ni test qilish
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Admin Telegram ID</label>
                                    <input class="form-control" name="telegram_id" value="{{ old('telegram_id', $currentUser->telegram_id) }}" placeholder="Masalan: 123456789">
                                    <small class="text-muted">Test e`lon yuborish uchun shu adminning Telegram ID sini saqlang va botga `/start` yuboring.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Bot username</label><input class="form-control" name="bot_username" value="{{ old('bot_username', $schoolBot->bot_username) }}"></div></div>
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Asosiy kanal</label><input class="form-control" name="main_channel" value="{{ old('main_channel', $schoolBot->main_channel) }}"></div></div>
                                </div>

                                <div class="mb-3">
                                    <label for="main_group" class="form-label">Asosiy guruh</label>
                                    <input id="main_group" name="main_group" class="form-control" value="{{ old('main_group', $schoolBot->main_group) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="welcome_text" class="form-label">Welcome xabar</label>
                                    <textarea id="welcome_text" name="welcome_text" class="form-control" rows="4">{{ old('welcome_text', $schoolBot->welcome_text) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="main_menu_text" class="form-label">Main menu text</label>
                                    <textarea id="main_menu_text" name="main_menu_text" class="form-control" rows="3">{{ old('main_menu_text', $schoolBot->main_menu_text) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary waves-effect waves-light">Saqlash</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Token ko'rsatish/yashirish
        document.getElementById('toggleToken')?.addEventListener('click', function() {
            const tokenInput = document.getElementById('bot_token');
            const icon = document.getElementById('toggleIcon');

            if (tokenInput.type === 'password') {
                tokenInput.type = 'text';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                tokenInput.type = 'password';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        });

        // Webhook test
        document.getElementById('testWebhook')?.addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Test qilinmoqda...';

            try {
                const response = await fetch('{{ route("admin.settings.test-webhook") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ Webhook test muvaffaqiyatli!\n\n' + (data.message || 'Bot ishlayapti.'));
                } else {
                    alert('❌ Webhook test muvaffaqiyatsiz!\n\n' + (data.message || 'Xatolik yuz berdi.'));
                }
            } catch (error) {
                alert('❌ Xatolik: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
@endsection
