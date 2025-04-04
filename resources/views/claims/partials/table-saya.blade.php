<div class="tab-pane {{ !Auth::user()->roles->contains('nama', 'Admin') ? 'active' : '' }}" id="saya">
    <div class="table-responsive">
        <table class="table table-bordered table-striped small" id="dataTableSaya">
            <thead>
                <tr>
                    <th>ID Klaim</th>
                    <th>No ACC</th>
                    <th>Periode</th>
                    <th>Kendaraan</th>
                    <th>BBM</th>
                    <th class="text-right">Saldo Awal</th>
                    <th class="text-right">Penggunaan</th>
                    <th class="text-right">Dana</th>
                    <th class="text-right">Sisa Saldo</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                // Filter klaim untuk user yang sedang login
                $userClaims = $claims->where('user_id', Auth::id());

                // Kelompokkan klaim berdasarkan periode
                $claimsByPeriod = $userClaims->groupBy('periode');

                // Urutkan klaim berdasarkan tanggal pembuatan (terbaru dulu)
                $sortedClaims = $userClaims->sortByDesc('created_at');

                // Buat array untuk menyimpan saldo yang sudah digunakan per periode
                $usedBalanceByPeriod = [];

                // Siapkan data saldo untuk setiap periode
                foreach ($claimsByPeriod as $periode => $periodClaims) {
                // Cek apakah ada periode sebelumnya dan hitung saldo awal
                $currentDate = Carbon\Carbon::parse($periode);
                $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

                // Default saldo awal
                $saldoAwal = 200.0;

                // Cari saldo dari periode sebelumnya
                $previousSaldo = App\Models\SaldoBBM::where('user_id', Auth::id())
                ->where('periode', $previousPeriode)
                ->first();

                // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awal
                if ($previousSaldo) {
                $previousTotalPenggunaan = App\Models\KlaimBBM::where('periode', $previousPeriode)
                ->where('user_id', Auth::id())
                ->sum('total_penggunaan_liter');
                $saldoAwal = 200.0 - $previousTotalPenggunaan;
                if ($saldoAwal < 0) $saldoAwal=0;
                    }

                    // Urutkan klaim berdasarkan tanggal pembuatan (paling lama dulu)
                    $orderedClaims=$periodClaims->sortBy('created_at');

                    // Inisialisasi saldo yang tersedia
                    $availableBalance = $saldoAwal;

                    // Hitung saldo awal untuk setiap klaim
                    foreach ($orderedClaims as $claim) {
                    $claimId = $claim->id;
                    $usedBalanceByPeriod[$claimId] = [
                    'saldo_awal' => $availableBalance,
                    'sisa_saldo' => $availableBalance - $claim->total_penggunaan_liter
                    ];

                    // Update saldo yang tersedia untuk klaim berikutnya
                    $availableBalance -= $claim->total_penggunaan_liter;
                    if ($availableBalance < 0) $availableBalance=0;
                        }
                        }
                        @endphp

                        @forelse($sortedClaims as $claim)
                        <tr @if($claim->saldoBbm && $claim->saldoBbm->status == 'melebihi_batas') class="table-warning" @endif>
                        <td>{{ $claim->klaim_id }}</td>
                        <td>{{ $claim->no_acc }}</td>
                        <td>{{ Carbon\Carbon::parse($claim->periode)->format('M Y') }}</td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 150px;" data-toggle="tooltip" title="{{ $claim->kendaraan->nama_kendaraan }} ({{ $claim->kendaraan->no_plat }})">
                                {{ $claim->kendaraan->nama_kendaraan }}
                            </span>
                        </td>
                        <td>{{ $claim->bbm->nama_bbm }}</td>
                        <td class="text-right">{{ number_format($usedBalanceByPeriod[$claim->id]['saldo_awal'], 1) }} L</td>
                        <td class="text-right">{{ number_format($claim->total_penggunaan_liter, 1) }} L</td>
                        <td class="text-right">Rp {{ number_format($claim->jumlah_dana) }}</td>
                        <td class="text-right">
                            {{ number_format($usedBalanceByPeriod[$claim->id]['sisa_saldo'], 1) }} L
                            @if($usedBalanceByPeriod[$claim->id]['sisa_saldo'] < 0 || ($claim->saldoBbm && $claim->saldoBbm->status == 'melebihi_batas'))
                                <span class="badge badge-danger">Melebihi Batas</span>
                                @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detail-{{ $claim->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('claims.edit', $claim->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('claims.destroy', $claim->id) }}" method="POST" class="d-inline" id="delete-form-{{ $claim->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $claim->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <i class="fas fa-inbox fa-2x mb-3 mt-3 d-block"></i>
                                Belum ada data klaim
                            </td>
                        </tr>
                        @endforelse
            </tbody>
        </table>
    </div>
</div>