@extends('layouts.app')

@section('title', 'Profile')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="">Profile</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <!-- Card Update Profile -->
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Update Profile</h3>
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-user mr-1"></i>Nama</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', Auth::user()->nama) }}" placeholder="Masukkan nama lengkap">
                        @error('nama')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope mr-1"></i>Email</label>
                        <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" disabled>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-id-card mr-1"></i>NIK</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->nik }}" disabled>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i>Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Card Update Password -->
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-key mr-2"></i>Update Password</h3>
            </div>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-lock mr-1"></i>Password Baru</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password baru">
                        @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock mr-1"></i>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Konfirmasi password baru">
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i>Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('
        success ') }}',
        timer: 1500,
        showConfirmButton: false,
        customClass: {
            popup: 'animated fadeInDown faster'
        }
    });
    @endif
</script>
@endpush