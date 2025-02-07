<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>Email</th>
            <th>Department</th>
            <th>Posisi</th>
            <th>Role</th>
            <th>Status</th>
            @if($status == 'menunggu')
            <th>Aksi</th>
            @endif
            @if($status == 'ditolak')
            <th>Alasan Penolakan</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            <td>{{ $user->nama }}</td>
            <td>{{ $user->nik }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->department->nama_department }}</td>
            <td>{{ $user->posisi }}</td>
            <td>{{ $user->roles->implode('nama', ', ') }}</td>
            <td>
                @if($user->status == 'menunggu')
                <span class="badge badge-warning">Menunggu</span>
                @elseif($user->status == 'disetujui')
                <span class="badge badge-success">Disetujui</span>
                @else
                <span class="badge badge-danger">Ditolak</span>
                @endif
            </td>
            @if($status == 'menunggu')
            <td>
                <form action="{{ route('users.approve', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $user->id }}">
                    <i class="fas fa-times"></i> Tolak
                </button>
            </td>
            @endif
            @if($status == 'ditolak')
            <td>{{ $user->rejection_reason }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data user</td>
        </tr>
        @endforelse
    </tbody>
</table>