@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-car mr-2"></i> Data Kendaraan</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-sm-right">
            @if(Auth::user()->roles->contains('nama', 'Admin'))
            <div class="btn-group">
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <h6 class="dropdown-header">Pilih Data Export</h6>
                    <a class="dropdown-item" href="{{ route('kendaraan.export', ['filter' => 'all']) }}">
                        <i class="fas fa-list mr-2"></i> Semua Data
                    </a>
                    <a class="dropdown-item" href="{{ route('kendaraan.export', ['filter' => 'my']) }}">
                        <i class="fas fa-user mr-2"></i> Data Saya
                    </a>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Export Per Departemen</h6>
                    @foreach($departments as $department)
                    <a class="dropdown-item" href="{{ route('kendaraan.export', ['filter' => 'department', 'department_id' => $department->id]) }}">
                        <i class="fas fa-building mr-2"></i> {{ $department->nama_department }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if(Auth::user()->roles->contains('nama', 'Admin'))
            <a href="{{ route('kendaraan.create') }}" class="btn btn-primary ml-2">
                <i class="fas fa-plus mr-1"></i> Tambah Kendaraan
            </a>
            @endif
        </div>
    </div>
</div> @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kendaraan</h3>
        <div class="card-tools">
            <ul class="nav nav-pills">
                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <li class="nav-item"><a class="nav-link active" href="#semua" data-toggle="tab">Semua</a></li>
                @endif
                <li class="nav-item"><a class="nav-link {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'active' : '' }}" href="#saya" data-toggle="tab">Data Saya</a></li>
                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <li class="nav-item"><a class="nav-link" href="#departemen" data-toggle="tab">Per Departemen</a></li>
                @endif
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            @if(Auth::user()->roles->contains('nama', 'Admin'))
            @include('kendaraan.partials.table-semua', ['kendaraans' => $kendaraans])
            @endif

            <div class="tab-pane {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'active' : '' }}" id="saya">
                @include('kendaraan.partials.table-saya', ['kendaraans' => $kendaraans])
            </div>

            @if(Auth::user()->roles->contains('nama', 'Admin'))
            @include('kendaraan.partials.table-departemen', ['departments' => $departments, 'kendaraans' => $kendaraans])
            @endif
        </div>
    </div>
</div>

@include('kendaraan.partials.modal-detail', ['kendaraans' => $kendaraans])
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'asc']
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 1500
    })
    @endif
</script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.dropdown-submenu a.dropdown-toggle').on("click", function(e) {
            $(this).next('div').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });
</script>
@endpush


@push('styles')
<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -1px;
    }

    .dropdown-submenu:hover .dropdown-menu {
        display: block;
    }
</style>
@endpush