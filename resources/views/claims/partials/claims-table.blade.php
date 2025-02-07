<div class="table-responsive">
    <table class="table table-hover claims-table">
        <thead>
            <tr>
                <th>No ACC</th>
                <th>Periode</th>
                <th>Kendaraan</th>
                <th>BBM</th>
                <th>User</th>
                <th class="text-right">Saldo</th>
                <th class="text-right">Penggunaan</th>
                <th class="text-right">Dana</th>
                <th class="text-right">Sisa</th>
                <th class="text-center" style="width: 120px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($claims as $claim)
            <tr>
                <td>{{ $claim->no_acc }}</td>
                <td>{{ Carbon\Carbon::parse($claim->periode)->format('M Y') }}</td>
                <td>{{ $claim->kendaraan->nama_kendaraan }}</td>
                <td>{{ $claim->bbm->nama_bbm }}</td>
                <td>{{ $claim->user->nama }}</td>
                <td class="text-right">{{ number_format($claim->saldo_liter, 1) }}</td>
                <td class="text-right">{{ number_format($claim->total_penggunaan_liter, 1) }}</td>
                <td class="text-right">Rp {{ number_format($claim->jumlah_dana) }}</td>
                <td class="text-right">{{ number_format($claim->sisa_saldo_liter, 1) }}</td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="{{ route('claims.show', $claim->id) }}" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Preview">
                            <i class="fas fa-search"></i>
                        </a>
                        <a href="{{ route('claims.pdf', $claim->id) }}" class="btn btn-danger btn-sm" target="_blank" data-toggle="tooltip" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @if($claim->user_id === Auth::id() || Auth::user()->roles->contains('nama', 'Admin'))
                        <a href="{{ route('claims.edit', $claim->id) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-3 mt-3 d-block"></i>
                    Belum ada data klaim
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>