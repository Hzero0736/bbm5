@extends('layouts.app')

@section('title', 'Login')

@section('body-class', 'hold-transition login-page')

@section('content')
<div class="login-box">
    <div class="login-logo mb-4">
        <img src="{{ asset('dist/img/image001.png') }}" alt="Logo" style="max-width: 150px;">
    </div>
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h1 class="h1">Login</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="row mb-3">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>
            </form>

            <div class="social-auth-links text-center mt-4">
                <p class="mb-1">Don't have an account?</p>
                <a href="{{ route('register') }}" class="btn btn-block btn-success">
                    <i class="fas fa-user-plus mr-2"></i> Register New Account
                </a>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .login-logo {
        text-align: center;
    }

    .login-box {
        width: 400px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Memproses Login...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        this.submit();
    });

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal!',
        text: "{!! session('error') !!}",
        showConfirmButton: true,
        confirmButtonText: 'Coba Lagi',
        confirmButtonColor: '#dc3545',
        customClass: {
            popup: 'animated shake'
        }
    });
    @endif

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{!! session('success') !!}",
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
    });
    @endif
</script>
@endpush