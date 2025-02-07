@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Edit Kendaraan</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Edit Kendaraan</h3>
            </div>
            <form action="{{ route('kendaraan.update', $kendaraan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_kendaraan">Nama Kendaraan</label>
                        <input type="text" class="form-control @error('nama_kendaraan') is-invalid @enderror"
                            id="nama_kendaraan" name="nama_kendaraan" value="{{ old('nama_kendaraan', $kendaraan->nama_kendaraan) }}"
                            placeholder="Masukkan nama kendaraan">
                        @error('nama_kendaraan')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="no_plat">Nomor Plat</label>
                        <input type="text" class="form-control @error('no_plat') is-invalid @enderror"
                            id="no_plat" name="no_plat" value="{{ old('no_plat', $kendaraan->no_plat) }}"
                            placeholder="Contoh: B 1234 XYZ">
                        @error('no_plat')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keperluan">Keperluan</label>
                        <select name="keperluan" id="keperluan" class="form-control @error('keperluan') is-invalid @enderror" style="width: 100%;">
                            <option value="" disabled>Pilih keperluan</option>
                            <option value="Kendaraan Operasional" {{ old('keperluan', $kendaraan->keperluan) == 'Kendaraan Operasional' ? 'selected' : '' }}>Kendaraan Operasional</option>
                            <option value="Kendaraan Pribadi" {{ old('keperluan', $kendaraan->keperluan) == 'kendaraan_pribadi' ? 'selected' : '' }}>Kendaraan Pribadi</option>
                        </select>
                        @error('keperluan')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="user">Pemilik Kendaraan</label>
                        <select name="user_id" id="user" class="form-control select2 @error('user_id') is-invalid @enderror" style="width: 100%;">
                            <option value="" disabled>Pilih Pemilik Kendaraan</option>
                            @foreach($departments as $department)
                            <optgroup label="{{ $department->nama_department }}" style="font-weight: bold;">
                                @foreach($department->users->where('status', 'disetujui') as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $kendaraan->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection