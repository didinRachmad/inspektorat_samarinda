@extends('layouts.auth')

@section('auth-content')
    <div class="mx-3 mx-lg-0">

        <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden p-4">
            <div class="row g-4">
                <div class="col-lg-6 d-lg-flex d-none">
                    <img src="{{ asset('images/auth/login1.png') }}" class="img-fluid rounded-4" alt="">
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon me-2">
                                <img src="{{ asset('images/logo.png') }}" class="logo-img" alt="" height="80" />
                            </div>
                            <div class="logo-name">
                                <h1 class="mb-0">Inspektorat Samarinda</h1>
                            </div>
                        </div>
                        <h4 class="fw-bold">Mulai Sekarang</h4>
                        <p class="mb-0">Masukkan kredensial Anda untuk masuk ke akun Anda</p>

                        <div class="form-body mt-4">
                            <!-- Session Status -->
                            <x-auth-session-status class="mb-3" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="row g-3">
                                @csrf

                                <!-- Email Address -->
                                <div class="col-12">
                                    <label class="form-label" for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" required autofocus autocomplete="username"
                                        placeholder="Masukkan Email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                                </div>

                                <!-- Password -->
                                <div class="col-12">
                                    <label class="form-label" for="password" class="form-label">Password</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control" id="password" name="password" required
                                            autocomplete="current-password" placeholder="Masukkan Password">
                                        <a href="javascript:;" class="input-group-text bg-transparent">
                                            <i class="bi bi-eye-slash-fill"></i>
                                        </a>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                                </div>

                                <!-- Remember Me -->
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                        <label class="form-label" class="form-check-label" for="remember_me">{{ __('Ingat saya') }}</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-grd-primary text-light">
                                            {{ __('Masuk') }}
                                        </button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-info btn-sm rounded-4" data-bs-toggle="modal"
                                            data-bs-target="#userListModal">
                                            Lihat Daftar User
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content rounded-4">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userListModalLabel">Daftar User Tersedia</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Password</th>
                                                    <th>Role</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $index = 1; @endphp
                                                @php $index = 1; @endphp
                                                @foreach (\App\Models\User::with('roles')->get() as $user)
                                                    <tr>
                                                        <td class="text-center">{{ $index++ }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            @php
                                                                $defaultPasswords = [
                                                                    'admin' => 'admin123',
                                                                    'user' => 'user123',
                                                                    'kasir' => 'kasir123',
                                                                    'manager' => 'manager123',
                                                                ];
                                                                $roleName = strtolower(
                                                                    $user->roles->first()?->name ?? '',
                                                                );
                                                                $passwordDisplay =
                                                                    $defaultPasswords[$roleName] ?? '12345678';
                                                            @endphp
                                                            <code>{{ $passwordDisplay }}</code>
                                                        </td>
                                                        <td>{{ $user->roles->first()?->name ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- <p class="text-muted small mt-2">⚠️ Password ditampilkan hanya jika diset default.
                                        Silakan sesuaikan jika tidak sesuai.</p> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!--end row-->
        </div>

    </div>
@endsection
