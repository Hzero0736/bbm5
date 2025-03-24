<table class="table table-bordered table-striped">
    <thead>
        <tr>
            @if(in_array($status, ['disetujui', 'ditolak']))
            <th>
                <input type="checkbox" id="select-all">
            </th>
            @endif
            <th>Nama</th>
            <th>NIK</th>
            <th>Email</th>
            <th>Department</th>
            <th>Role</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            @if(in_array($status, ['disetujui', 'ditolak']))
            <td>
                <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
            </td>
            @endif
            <td>{{ $user->nama }}</td>
            <td>{{ $user->nik }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->department->nama_department }}</td>
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
            <td>
                @if($status == 'menunggu')
                <button class="btn btn-success btn-sm approve-btn" data-id="{{ $user->id }}">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-danger btn-sm btn-reject" data-id="{{ $user->id }}">
                    <i class="fas fa-times"></i> Tolak
                </button>


                @else
                @if(Auth::id() !== $user->id)
                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $user->id }}">
                    <i class="fas fa-trash"></i>
                </button>
                @endif

                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>