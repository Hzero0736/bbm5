@foreach($claims as $claim)
<div class="modal fade" id="detail-{{ $claim->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Klaim BBM</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <img src="{{ asset('dist/img/image001.png') }}" alt="Logo" style="height: 50px;">
                    <div>
                        <p class="mb-0">Periode: {{ Carbon\Carbon::parse($claim->periode)->format('M-Y') }}</p>
                    </div>
                </div>

                <h4 class="text-center mb-4">Form Claim BBM</h4>

                <!-- Informasi Pemohon -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-2">Nama Pemohon: <strong>{{ $claim->user->nama }}</strong></p>
                        <p class="mb-2">Posisi / Dept: {{ $claim->user->roles->pluck('nama')->implode(', ') }} / {{ $claim->user->department->nama_department }}</p>
                        <p class="mb-2">Jumlah Dana: <strong>Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</strong></p>
                        <p class="mb-2">Keperluan: BBM {{ $claim->kendaraan->keperluan }} {{ $claim->kendaraan->no_plat }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">NIK: {{ $claim->user->nik }}</p>
                        <p class="mb-2">Cost Center: {{ $claim->user->department->cost_center }}</p>
                        <p class="mb-2">Jenis BBM: {{ $claim->bbm->nama_bbm }}</p>
                    </div>
                </div>

                <!-- Tabel Data BBM -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Uraian</th>
                                <th>Tanggal</th>
                                <th>Km</th>
                                <th>Rp/Liter</th>
                                <th>Liter</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claim->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>BBM mobil ({{ $claim->kendaraan->no_plat }})</td>
                                <td>{{ Carbon\Carbon::parse($detail->tanggal)->format('d.m.Y') }}</td>
                                <td>{{ number_format($detail->km, 3, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->bbm->harga_bbm) }}</td>
                                <td>{{ number_format($detail->liter, 2) }} L</td>
                                <td>Rp {{ number_format($detail->total_harga) }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-weight-bold">
                                <td colspan="5" class="text-right">JUMLAH YANG DITAGIHKAN</td>
                                <td>{{ number_format($claim->total_penggunaan_liter, 2) }} L</td>
                                <td>Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Ringkasan Saldo -->
                @php
                // Kelompokkan klaim berdasarkan periode dan user
                $claimsByPeriodAndUser = $claims->groupBy(function($c) {
                return $c->periode . '-' . $c->user_id;
                });

                // Hitung saldo untuk klaim ini
                $saldoAwal = 200.0;
                $sisaSaldo = 0;

                // Cek apakah ada periode sebelumnya dan hitung saldo awal
                $currentDate = Carbon\Carbon::parse($claim->periode);
                $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

                // Cari saldo dari periode sebelumnya
                $previousSaldo = App\Models\SaldoBBM::where('user_id', $claim->user_id)
                ->where('periode', $previousPeriode)
                ->first();

                // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awal
                if ($previousSaldo) {
                $previousTotalPenggunaan = App\Models\KlaimBBM::where('periode', $previousPeriode)
                ->where('user_id', $claim->user_id)
                ->sum('total_penggunaan_liter');
                $saldoAwal = 200.0 - $previousTotalPenggunaan;
                if ($saldoAwal < 0) $saldoAwal=0;
                    }

                    // Hitung total penggunaan sebelum klaim ini
                    $claimsBeforeThis=$claims->where('periode', $claim->periode)
                    ->where('user_id', $claim->user_id)
                    ->where('created_at', '<', $claim->created_at);

                        $totalPenggunaanSebelumnya = $claimsBeforeThis->sum('total_penggunaan_liter');
                        $saldoSebelumKlaim = $saldoAwal - $totalPenggunaanSebelumnya;
                        if ($saldoSebelumKlaim < 0) $saldoSebelumKlaim=0;

                            $sisaSaldo=$saldoSebelumKlaim - $claim->total_penggunaan_liter;
                            if ($sisaSaldo < 0) $sisaSaldo=0;
                                @endphp

                                <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Saldo Awal</td>
                                            <td>{{ number_format($saldoSebelumKlaim, 1) }} L</td>
                                        </tr>
                                        <tr>
                                            <td>Penggunaan BBM</td>
                                            <td>{{ number_format($claim->total_penggunaan_liter, 1) }} L</td>
                                        </tr>
                                        <tr>
                                            <td>Sisa Saldo</td>
                                            <td>
                                                {{ number_format($sisaSaldo, 1) }} L
                                                @if($sisaSaldo < 0 || ($claim->saldoBbm && $claim->saldoBbm->status == 'melebihi_batas'))
                                                    <span class="badge badge-danger">Melebihi Batas</span>
                                                    @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
            </div>

            <!-- Tanda Tangan -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <p class="mb-2">Diminta oleh:</p>
                    <div class="border-bottom p-3 mb-2" style="height: 60px;"></div>
                    <p class="mb-0"><strong>{{ $claim->user->nama }}</strong></p>
                </div>
                <div class="col-md-8">
                    @if($claim->catatan)
                    <p class="text-muted font-italic mb-1">*Catatan: {{ $claim->catatan }}</p>
                    @endif
                    <p class="text-muted font-italic mb-1">*Minimal setingkat section head</p>
                    <p class="text-muted font-italic mb-0">*Wajib melampirkan seluruh nota print pembelian</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <a href="{{ route('claims.pdf', $claim->id) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </a>
        </div>
    </div>
</div>
</div>
@endforeach

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus klaim ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>