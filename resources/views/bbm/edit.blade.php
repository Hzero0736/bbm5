@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit BBM</h3>
        </div>
        <form action="{{ route('bbm.update', $bbm->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama BBM</label>
                    <input type="text" name="nama_bbm" class="form-control @error('nama_bbm') is-invalid @enderror" value="{{ old('nama_bbm', $bbm->nama_bbm) }}">
                    @error('nama_bbm')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Harga BBM</label>
                    <input type="number" name="harga_bbm" class="form-control @error('harga_bbm') is-invalid @enderror" value="{{ old('harga_bbm', $bbm->harga_bbm) }}">
                    @error('harga_bbm')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('bbm.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection