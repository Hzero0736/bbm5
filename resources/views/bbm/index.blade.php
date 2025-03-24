@extends('layouts.app')

@section('title', 'Data BBM')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-gas-pump"></i> Data BBM</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header">
            <h3 class="card-title mb-0">Data BBM</h3>
            <div class="card-tools">
                <a href="{{ route('bbm.create') }}" class="btn bg-primary btn-light btn-sm">
                    <i class="fas fa-plus"></i> Tambah BBM
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama BBM</th>
                            <th>Harga</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bbm as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $item->nama_bbm }}</td>
                            <td>Rp {{ number_format($item->harga_bbm, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('bbm.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('bbm.destroy', $item->id) }}" method="POST" class="d-inline" id="delete-form-{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
        text: "{!! session('success') !!}",
        showConfirmButton: false,
        timer: 1500
    })
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{!! session('error') !!}",
    })
    @endif
</script>
@endpush
@endsection