@foreach($kendaraans as $kendaraan)
<div class="modal fade" id="detail-{{ $kendaraan->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Kendaraan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">No. Plat</th>
                        <td>{{ $kendaraan->no_plat }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kendaraan</th>
                        <td>{{ $kendaraan->nama_kendaraan }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $kendaraan->user->department->nama_department }}</td>
                    </tr>
                    <tr>
                        <th>Pemilik</th>
                        <td>{{ $kendaraan->user->nama }}</td>
                    </tr>
                    <tr>
                        <th>Keperluan</th>
                        <td>{{ $kendaraan->keperluan }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Ditambahkan</th>
                        <td>{{ $kendaraan->created_at->format('d/m/Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach