@extends('layouts.app')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Edit Department</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('departments.update', $department->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Kode Department</label>
                        <input type="text" name="kode_department" class="form-control" value="{{ $department->kode_department }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Department</label>
                        <input type="text" name="nama_department" class="form-control" value="{{ $department->nama_department }}" required>
                    </div>
                    <div class="form-group">
                        <label>Cost Center</label>
                        <input type="text" name="cost_center" class="form-control" value="{{ $department->cost_center }}" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('departments.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection