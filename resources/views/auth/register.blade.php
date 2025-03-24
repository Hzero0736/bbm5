@extends('layouts.app')

@section('title', 'Register')

@section('body-class', 'hold-transition register-page')

@section('content')
<div class="register-box">
    <div class="register-logo mb-4">
        <img src="{{ asset('dist/img/image001.png') }}" alt="Logo" style="max-width: 150px;">
    </div>
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <h1 class="h1">Register</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('register.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Full Name" value="{{ old('nama') }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            @error('nama')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="NIK" value="{{ old('nik') }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-id-card"></span>
                                </div>
                            </div>
                            @error('nik')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
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
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <select name="department_id" class="form-control @error('department_id') is-invalid @enderror">
                                <option value="">Select Department</option>
                                @foreach($department as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->nama_department }}
                                </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-building"></span>
                                </div>
                            </div>
                            @error('department_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <select name="role_id" class="form-control @error('role_id') is-invalid @enderror">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama }}
                                </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user-tag"></span>
                                </div>
                            </div>
                            @error('role_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-block mb-3">Register</button>
                    </div>
                </div>
            </form>

            <div class="text-center">
                <p class="mb-0">Already have an account?</p>
                <a href="{{ route('login') }}" class="btn btn-block btn-primary">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .register-box {
        width: 800px;
    }

    .register-logo {
        text-align: center;
    }

    .card-body {
        padding: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Memproses Registrasi...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        this.submit();
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Registrasi Berhasil!',
        text: "{!! session('success') !!}",
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745',
        timer: 5000,
        timerProgressBar: true,
        customClass: {
            popup: 'animated bounceIn'
        }
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Registrasi Gagal!',
        text: "{!! session('error') !!}",
        showConfirmButton: true,
        confirmButtonText: 'Coba Lagi',
        confirmButtonColor: '#dc3545',
        customClass: {
            popup: 'animated shake'
        }
    });
    @endif
</script>
@endpush
@endsection