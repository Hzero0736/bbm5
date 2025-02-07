@extends('layouts.app')

@section('title', 'Persetujuan User')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-user-check"></i> Persetujuan User</h1>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar User</h3>
        <div class="card-tools">
            <ul class="nav nav-pills ml-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#menunggu" data-toggle="tab">Menunggu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#disetujui" data-toggle="tab">Disetujui</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#ditolak" data-toggle="tab">Ditolak</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="menunggu">
                @include('admin.partials.user-table', ['users' => $pendingUsers, 'status' => 'menunggu'])
            </div>
            <div class="tab-pane" id="disetujui">
                @include('admin.partials.user-table', ['users' => $approvedUsers, 'status' => 'disetujui'])
            </div>
            <div class="tab-pane" id="ditolak">
                @include('admin.partials.user-table', ['users' => $rejectedUsers, 'status' => 'ditolak'])
            </div>
        </div>
    </div>
</div>

@foreach($pendingUsers as $user)
<div class="modal fade" id="rejectModal{{ $user->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.reject', $user->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tolak Pendaftaran User</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
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
        showConfirmButton: false
    });
    @endif
</script>
@endpush