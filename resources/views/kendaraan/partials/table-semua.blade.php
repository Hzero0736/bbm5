<div class="tab-pane active" id="semua">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Plat Nomor</th>
                <th>Merk</th>
                <th>Pemilik</th>
                <th>Departemen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraans as $kendaraan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kendaraan->no_plat }}</td>
                <td>{{ $kendaraan->nama_kendaraan }}</td>
                <td>{{ $kendaraan->user->nama }}</td>
                <td>{{ $kendaraan->user->department->nama_department }}</td>
                <td>
                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detail-{{ $kendaraan->id }}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="{{ route('kendaraan.edit', $kendaraan->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" class="d-inline" id="delete-form-{{ $kendaraan->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $kendaraan->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>