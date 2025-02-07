@extends('layouts.app')

@section('title', 'Data Klaim BBM')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                Data Klaim BBM
            </h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('claims.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i>
                Buat Klaim Baru
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-tabs card-header-tabs">
                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#semua">
                        <i class="fas fa-list mr-1"></i>
                        Semua Data
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'active' : '' }}" data-toggle="tab" href="#saya">
                        <i class="fas fa-user mr-1"></i>
                        Data Saya
                    </a>
                </li>
                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#departemen">
                        <i class="fas fa-building mr-1"></i>
                        Per Departemen
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <div class="tab-pane fade show active" id="semua">
                    @include('claims.partials.claims-table', ['claims' => $claims])
                </div>
                @endif

                <div class="tab-pane fade {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'show active' : '' }}" id="saya">
                    @include('claims.partials.claims-table', ['claims' => $claims->where('user_id', Auth::id())])
                </div>

                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <div class="tab-pane fade" id="departemen">
                    @foreach($departments as $department)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ $department->nama_department }}</h6>
                        </div>
                        <div class="card-body p-0">
                            @include('claims.partials.claims-table', ['claims' => $claims->whereIn('user_id', $department->users->pluck('id'))])
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .table th {
        font-weight: 600;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    .card-header-tabs {
        margin-bottom: -1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const dataTableConfig = {
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            order: [
                [1, 'desc']
            ],
            pageLength: 10,
            columnDefs: [{
                targets: -1,
                orderable: false
            }]
        };

        $('#dataTableAll').DataTable(dataTableConfig);
        $('#dataTableSaya').DataTable(dataTableConfig);

        @foreach($departments as $department)
        $('#dataTable{{ $department->id }}').DataTable(dataTableConfig);
        @endforeach

        $('[data-toggle="tooltip"]').tooltip();
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        showConfirmButton
    });
    @endif
</script>
@endpush