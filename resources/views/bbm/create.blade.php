@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah BBM</h3>
        </div>
        <form action="{{ route('bbm.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama BBM</label>
                    <input type="text" name="nama_bbm" class="form-control @error('nama_bbm') is-invalid @enderror" value="{{ old('nama_bbm') }}">
                    @error('nama_bbm')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Harga BBM</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" min="0" step="1" name="harga_bbm" class="form-control @error('harga_bbm') is-invalid @enderror" value="{{ old('harga_bbm') }}" placeholder="Masukkan harga">
                        @error('harga_bbm')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('bbm.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection