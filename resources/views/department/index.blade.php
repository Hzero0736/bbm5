@extends('layouts.app')

@section('title', 'Manajemen Department')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"> <i class="fas fa-building"></i> Manajemen Department</h1>
    </div>
</div>
@endsection

@section('content')


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus"></i> Tambah Department
                </button>
            </div>
            <div class="card-body">
                <table id="departmentTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Department</th>
                            <th>Nama Department</th>
                            <th>Cost Center</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $index => $department)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $department->kode_department }}</td>
                            <td>{{ $department->nama_department }}</td>
                            <td>{{ $department->cost_center }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" onclick="editDepartment({{ $department->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $department->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $department->id }}" action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kode_department">Kode Department</label>
                        <input type="text" class="form-control @error('kode_department') is-invalid @enderror" id="kode_department" name="kode_department" required>
                        @error('kode_department')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_department">Nama Department</label>
                        <input type="text" class="form-control @error('nama_department') is-invalid @enderror" id="nama_department" name="nama_department" required>
                        @error('nama_department')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cost_center">Cost Center</label>
                        <input type="text" class="form-control @error('cost_center') is-invalid @enderror" id="cost_center" name="cost_center" required>
                        @error('cost_center')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#departmentTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });

        // Handle success message
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{!! session('success') !!}",
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        // Handle error message
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{!! session('error') !!}",
            timer: 3000,
            showConfirmButton: false
        });
        @endif
    });

    function editDepartment(id) {
        window.location.href = `/departments/${id}/edit`;
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin hapus data?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>@endpush

@endsection