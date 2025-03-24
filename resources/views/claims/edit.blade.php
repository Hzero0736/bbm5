@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-file-invoice-dollar me-2"></i> Edit Klaim BBM
            </h5>
        </div>

        <form action="{{ route('claims.update', $claim->id) }}" method="POST" id="claimForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="alert alert-info d-flex justify-content-between align-items-center" id="saldo-alert">
                    <div>
                        <i class="fas fa-info-circle mr-2"></i> <span id="sisa-saldo">Sisa Saldo: <strong><span id="saldo-periode">0</span> liter</strong></span>
                    </div>
                    <div>
                        <span class="badge">Batas Maksimal: 200 liter per periode</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="periode"><i class="fas fa-calendar-alt mr-1"></i> Periode <span class="text-danger">*</span></label>
                            <input type="month" name="periode" id="periode" class="form-control"
                                value="{{ $claim->periode instanceof \Carbon\Carbon ? $claim->periode->format('Y-m') : $claim->periode }}" required>

                            <small class="form-text text-muted">Format: Bulan-Tahun</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kendaraan_id"><i class="fas fa-car mr-1"></i> Kendaraan <span class="text-danger">*</span></label>
                            <select name="kendaraan_id" id="kendaraan_id" class="form-control select2" required>
                                <option value="">Pilih Kendaraan</option>
                                @foreach($kendaraans as $kendaraan)
                                <option value="{{ $kendaraan->id }}" {{ $kendaraan->id == $claim->kendaraan_id ? 'selected' : '' }}>
                                    {{ $kendaraan->nama_kendaraan }} ({{ $kendaraan->no_plat }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bbm_id"><i class="fas fa-gas-pump mr-1"></i> BBM <span class="text-danger">*</span></label>
                            <select name="bbm_id" id="bbm_id" class="form-control select2" required>
                                <option value="">Pilih BBM</option>
                                @foreach($bbms as $bbm)
                                <option value="{{ $bbm->id }}" data-harga="{{ $bbm->harga_bbm }}" {{ $bbm->id == $claim->bbm_id ? 'selected' : '' }}>
                                    {{ $bbm->nama_bbm }} - Rp {{ number_format($bbm->harga_bbm) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="catatan">
                        <i class="fas fa-sticky-note mr-1"></i> Catatan
                        <span id="catatan-required" class="text-danger d-none">*</span>
                    </label>
                    <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2"
                        placeholder="Tambahkan catatan jika diperlukan">{{ $claim->catatan }}</textarea>
                    @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small id="catatan-help" class="form-text text-muted d-none">
                        <i class="fas fa-info-circle"></i> Catatan wajib diisi karena penggunaan BBM melebihi batas saldo
                    </small>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-list-ul mr-1"></i> Detail Penggunaan BBM</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th width="20%">Tanggal</th>
                                        <th width="20%">KM</th>
                                        <th width="20%">Liter</th>
                                        <th width="25%">Total Harga</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-container">
                                    @foreach($claim->details as $index => $detail)
                                    <tr class="detail-row" data-index="{{ $index }}">
                                        <td class="text-center row-number">{{ $index + 1 }}</td>
                                        <td>
                                            <input type="date" name="details[{{ $index }}][tanggal]" class="form-control" value="{{ $detail->tanggal->format('Y-m-d') }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.001" name="details[{{ $index }}][km]" class="form-control" min="0" value="{{ $detail->km }}" required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="details[{{ $index }}][liter]" class="form-control liter" value="{{ $detail->liter }}" min="0.01" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">L</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="details[{{ $index }}][total_harga]" class="total-harga" value="{{ $detail->total_harga }}">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" class="form-control total-harga-display" value="{{ number_format($detail->total_harga) }}" readonly>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm delete-row" {{ $loop->first && count($claim->details) == 1 ? 'disabled' : '' }} title="Hapus Baris">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-right font-weight-bold">
                                            <span id="total-liter">{{ $claim->total_penggunaan_liter }}</span> L
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            Rp <span id="total-harga">{{ number_format($claim->jumlah_dana) }}</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" id="add-row">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                        <label class="form-check-label" for="confirmCheck">
                            Saya menyatakan data yang diinput sudah benar
                        </label>
                    </div>
                </div>
                <div>
                    <a href="{{ route('claims.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .detail-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });
        }

        // Variabel untuk menghitung jumlah baris
        let rowIndex = {
            {
                count($claim -> details)
            }
        };

        // Fungsi untuk memuat sisa saldo
        function loadSisaSaldo(periode) {
            if (!periode) return;

            $.get('/claims/sisa-saldo/' + periode, function(response) {
                $('#saldo-periode').text(response.sisa_saldo);
                hitungTotal();
            }).fail(function() {
                alert('Gagal mengambil data saldo');
            });
        }

        // Fungsi untuk menghitung total per baris
        function hitungTotalBaris(input) {
            let row = $(input).closest('tr');
            let liter = parseFloat($(input).val()) || 0;
            let harga = parseFloat($('#bbm_id option:selected').data('harga')) || 0;
            let total = liter * harga;

            row.find('.total-harga').val(total);
            row.find('.total-harga-display').val(total.toLocaleString('id'));
        }

        // Fungsi untuk menghitung total keseluruhan
        function hitungTotal() {
            let totalLiter = 0;
            let totalHarga = 0;

            $('.liter').each(function() {
                let liter = parseFloat($(this).val()) || 0;
                let total = parseFloat($(this).closest('tr').find('.total-harga').val()) || 0;

                totalLiter += liter;
                totalHarga += total;
            });

            $('#total-liter').text(totalLiter.toFixed(2));
            $('#total-harga').text(totalHarga.toLocaleString('id'));

            // Cek saldo
            let sisaSaldo = parseFloat($('#saldo-periode').text()) || 0;
            if (totalLiter > sisaSaldo) {
                $('#saldo-alert').removeClass('alert-info').addClass('alert-warning');
                $('#catatan-required').removeClass('d-none');
                $('#catatan-help').removeClass('d-none');
            } else {
                $('#saldo-alert').removeClass('alert-warning').addClass('alert-info');
                $('#catatan-required').addClass('d-none');
                $('#catatan-help').addClass('d-none');
            }
        }

        // Event handlers

        // Handler untuk perubahan periode
        $('#periode').on('change', function() {
            let periode = $(this).val();
            loadSisaSaldo(periode);

            // Update batasan tanggal
            if (periode) {
                let [year, month] = periode.split('-');
                let lastDay = new Date(year, month, 0).getDate();

                $('input[type="date"]').attr({
                    'min': periode + '-01',
                    'max': periode + '-' + lastDay
                });
            }
        });

        // Handler untuk input liter
        $(document).on('input', '.liter', function() {
            hitungTotalBaris(this);
            hitungTotal();
        });

        // Handler untuk perubahan BBM
        $('#bbm_id').on('change', function() {
            $('.liter').each(function() {
                hitungTotalBaris(this);
            });
            hitungTotal();
        });

        // Handler untuk tambah baris
        $('#add-row').on('click', function() {
            let newRow = $('.detail-row').first().clone();

            // Reset nilai
            newRow.find('input').val('');
            newRow.find('.delete-row').prop('disabled', false);

            // Update indeks
            newRow.find('[name]').each(function() {
                let name = $(this).attr('name');
                $(this).attr('name', name.replace(/\d+/, rowIndex));
            });

            // Update nomor
            newRow.find('.row-number').text(rowIndex + 1);

            // Tambahkan ke tabel
            $('#detail-container').append(newRow);
            rowIndex++;

            // Update batasan tanggal
            let periode = $('#periode').val();
            if (periode) {
                let [year, month] = periode.split('-');
                let lastDay = new Date(year, month, 0).getDate();

                newRow.find('input[type="date"]').attr({
                    'min': periode + '-01',
                    'max': periode + '-' + lastDay
                });
            }
        });

        // Handler untuk hapus baris
        $(document).on('click', '.delete-row', function() {
            if ($('.detail-row').length > 1) {
                $(this).closest('tr').remove();

                // Update nomor baris
                $('.row-number').each(function(index) {
                    $(this).text(index + 1);
                });

                hitungTotal();
            } else {
                alert('Minimal harus ada satu baris detail');
            }
        });

        // Validasi form
        $('#claimForm').on('submit', function(e) {
            // Cek field wajib
            if (!$('#periode').val() || !$('#kendaraan_id').val() || !$('#bbm_id').val()) {
                alert('Silakan lengkapi data periode, kendaraan, dan BBM');
                e.preventDefault();
                return false;
            }

            // Cek detail
            let valid = true;
            $('.detail-row input[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                alert('Silakan lengkapi semua detail penggunaan BBM');
                e.preventDefault();
                return false;
            }

            // Cek catatan jika melebihi saldo
            let totalLiter = parseFloat($('#total-liter').text()) || 0;
            let sisaSaldo = parseFloat($('#saldo-periode').text()) || 0;

            if (totalLiter > sisaSaldo && !$('#catatan').val().trim()) {
                alert('Anda harus mengisi catatan ketika penggunaan BBM melebihi batas saldo');
                $('#catatan').addClass('is-invalid').focus();
                e.preventDefault();
                return false;
            }

            // Konfirmasi jika melebihi 200 liter
            if (totalLiter > 200 && !confirm('Total penggunaan BBM melebihi batas 200 liter. Apakah Anda yakin ingin melanjutkan?')) {
                e.preventDefault();
                return false;
            }
        });

        // Inisialisasi
        let periode = $('#periode').val();
        if (periode) {
            loadSisaSaldo(periode);

            // Set batasan tanggal
            let [year, month] = periode.split('-');
            let lastDay = new Date(year, month, 0).getDate();

            $('input[type="date"]').attr({
                'min': periode + '-01',
                'max': periode + '-' + lastDay
            });
        }

        // Hitung total awal
        $('.liter').each(function() {
            hitungTotalBaris(this);
        });
        hitungTotal();
    });
</script>
@endpush