@extends('layouts.admin')

@section('body')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Xush kelibsiz</h5>
                                        <p>Maktab botini boshqarish uchun tizimga kiring.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('skote/assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <div class="avatar-md profile-user-wid mb-4">
                                    <span class="avatar-title rounded-circle bg-light">
                                        <img src="{{ asset('skote/assets/images/logo.svg') }}" alt="" class="rounded-circle" height="34">
                                    </span>
                                </div>
                            </div>
                            <div class="p-2">
                                @include('admin.partials.flash')

                                <form class="form-horizontal" method="post" action="{{ route('login.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Parol</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Parolni kiriting" required>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Kirish</button>
                                    </div>

                            
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <p>{{ now()->year }} Larabot School Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
