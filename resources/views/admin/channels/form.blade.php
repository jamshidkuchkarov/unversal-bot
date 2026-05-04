@extends('layouts.admin')

@section('body')
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('admin.partials.page-header', [
                        'title' => $channel->exists ? 'Kanalni tahrirlash' : 'Kanal qo`shish',
                        'subtitle' => 'Majburiy obuna uchun kanal ma`lumotlari.',
                    ])
                    @include('admin.partials.flash')
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ $channel->exists ? route('admin.channels.update', $channel) : route('admin.channels.store') }}">
                                @csrf
                                @if($channel->exists) @method('put') @endif
                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Nomi</label><input class="form-control" name="title" value="{{ old('title', $channel->title) }}" required></div></div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Chat ID</label>
                                            <input class="form-control" id="chat_id_input" name="chat_id" value="{{ old('chat_id', $channel->chat_id) }}" required>
                                            <small class="text-muted">Username orqali Chat ID ni olish uchun quyidagi tugmani bosing</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Username (Chat ID olish uchun)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">@</span>
                                                <input type="text" class="form-control" id="username_lookup" placeholder="kanal_username">
                                                <button type="button" class="btn btn-primary" onclick="getChatId()">Chat ID olish</button>
                                            </div>
                                            <div id="chat_id_result" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                function getChatId() {
                                    const username = document.getElementById('username_lookup').value.replace('@', '');
                                    const resultDiv = document.getElementById('chat_id_result');

                                    if (!username) {
                                        resultDiv.innerHTML = '<div class="alert alert-warning">Username kiriting</div>';
                                        return;
                                    }

                                    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Tekshirilmoqda...';

                                    fetch('/admin/channels/get-chat-id?username=' + username)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                document.getElementById('chat_id_input').value = data.chat_id;
                                                document.getElementById('username').value = '@' + username;
                                                resultDiv.innerHTML = '<div class="alert alert-success">✅ Topildi: ' + data.title + '<br>Chat ID: <strong>' + data.chat_id + '</strong></div>';
                                            } else {
                                                resultDiv.innerHTML = '<div class="alert alert-danger">❌ ' + data.message + '</div>';
                                            }
                                        })
                                        .catch(error => {
                                            resultDiv.innerHTML = '<div class="alert alert-danger">❌ Xatolik yuz berdi</div>';
                                        });
                                }
                                </script>
                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Username</label><input class="form-control" id="username" name="username" value="{{ old('username', $channel->username) }}"></div></div>
                                    <div class="col-md-6"><div class="mb-3"><label class="form-label">Invite link</label><input class="form-control" name="invite_link" value="{{ old('invite_link', $channel->invite_link) }}"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Majburiy</label><select class="form-select" name="is_required"><option value="1" @selected(old('is_required', $channel->is_required) == 1)>Ha</option><option value="0" @selected(old('is_required', $channel->is_required) == 0)>Yo`q</option></select></div></div>
                                    <div class="col-md-4"><div class="mb-3"><label class="form-label">Faol</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active', $channel->is_active) == 1)>Ha</option><option value="0" @selected(old('is_active', $channel->is_active) == 0)>Yo`q</option></select></div></div>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Saqlash</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
