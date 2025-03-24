@extends('layouts.app')

@section('title', 'Data Klaim BBM')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-file-invoice-dollar mr-2"></i> Data Klaim BBM</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-sm-right">
            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#exportModal">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </button>
            <a href="{{ route('claims.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Buat Klaim Baru
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Klaim BBM</h3>
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
            <div class="tab-pane fade show active" id="semua">
                <div class="table-responsive">
                    @include('claims.partials.table-semua', ['claims' => $claims])
                </div>
            </div>
            @endif

            <div class="tab-pane fade {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'show active' : '' }}" id="saya">
                <div class="table-responsive">
                    @include('claims.partials.table-saya', ['claims' => $claims->where('user_id', Auth::id())])
                </div>
            </div>

            @if(Auth::user()->roles->contains('nama', 'Admin'))
            <div class="tab-pane fade" id="departemen">
                @include('claims.partials.table-departemen', ['departments' => $departments, 'claims' => $claims])
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Export -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('claims.export') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Data Klaim BBM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filter">Filter Data</label>
                        <select class="form-control" id="filter" name="filter">
                            <option value="semua">Semua Data</option>
                            <option value="saya">Data Saya</option>
                            @if(Auth::user()->roles->contains('nama', 'Admin'))
                            <option value="departemen">Per Departemen</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group" id="departemenGroup" style="display: none;">
                        <label for="department_id">Departemen</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="periode">Periode (Opsional)</label>
                        <input type="month" class="form-control" id="periode" name="periode">
                    </div>

                    @if(Auth::user()->roles->contains('nama', 'Admin'))
                    <div class="form-group">
                        <label for="status">Status (Opsional)</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="normal">Normal</option>
                            <option value="melebihi_batas">Melebihi Batas</option>
                        </select>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('claims.partials.modal-detail', ['claims' => $claims])
@endsection

@push('css')
<style>
    .table th {
        font-weight: 600;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    .table-warning {
        background-color: #fff3cd !important;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .text-truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Responsif untuk tabel */
    @media (max-width: 767.98px) {
        .table {
            font-size: 0.85rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .text-truncate {
            max-width: 80px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [2, 'desc'], // Urutkan berdasarkan periode (kolom ke-3)
                [0, 'desc'] // Kemudian berdasarkan ID klaim (kolom ke-1)
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            columnDefs: [{
                    responsivePriority: 1,
                    targets: [0, 2, 9, 10]
                }, // Prioritaskan kolom penting
                {
                    responsivePriority: 2,
                    targets: [3, 7, 8]
                },
                {
                    responsivePriority: 3,
                    targets: '_all'
                }
            ]
        });

        $('[data-toggle="tooltip"]').tooltip();

        // Tampilkan/sembunyikan field departemen berdasarkan filter
        $('#filter').change(function() {
            if ($(this).val() === 'departemen') {
                $('#departemenGroup').show();
            } else {
                $('#departemenGroup').hide();
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

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        showConfirmButton: true
    })
    @endif
</script>
@endpush