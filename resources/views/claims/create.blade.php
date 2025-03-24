@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-file-invoice-dollar me-2"></i> Buat Klaim BBM
            </h5>
        </div>

        <form action="{{ route('claims.store') }}" method="POST" id="claimForm">
            @csrf
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
                            <input type="month" name="periode" id="periode" class="form-control" required>
                            <small class="form-text text-muted">Format: Bulan-Tahun</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kendaraan_id"><i class="fas fa-car mr-1"></i> Kendaraan <span class="text-danger">*</span></label>
                            <select name="kendaraan_id" id="kendaraan_id" class="form-control select2" required>
                                <option value="">Pilih Kendaraan</option>
                                @foreach($kendaraans->where('user_id', auth()->id()) as $kendaraan)
                                <option value="{{ $kendaraan->id }}">{{ $kendaraan->nama_kendaraan }} ({{ $kendaraan->no_plat }})</option>
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
                                <option value="{{ $bbm->id }}" data-harga="{{ $bbm->harga_bbm }}">
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
                        placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan') }}</textarea>
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
                                    <tr class="detail-row" data-index="0">
                                        <td class="text-center row-number">1</td>
                                        <td>
                                            <input type="date" name="details[0][tanggal]" class="form-control" required>
                                        </td>
                                        <!-- Ubah input KM untuk mendukung nilai desimal -->
                                        <td>
                                            <input type="number" step="0.001" name="details[0][km]" class="form-control" min="0" required>
                                        </td>

                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="details[0][liter]" class="form-control liter" min="0.01" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">L</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="details[0][total_harga]" class="total-harga">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" class="form-control total-harga-display" readonly>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm delete-row" title="Hapus Baris">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-right font-weight-bold">
                                            <span id="total-liter">0</span> L
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            Rp <span id="total-harga">0</span>
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
                        <i class="fas fa-save mr-1"></i> Simpan
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
        // Inisialisasi variabel rowIndex
        let rowIndex = 0;

        // Fungsi tambah baris baru
        $('#add-row').click(function() {
            rowIndex++;
            let template = $('.detail-row').first().clone();

            // Reset nilai input
            template.find('input').val('');

            // Update atribut name dengan index baru
            template.find('[name]').each(function() {
                let name = $(this).attr('name');
                $(this).attr('name', name.replace(/\d+/, rowIndex));
            });

            // Update nomor baris
            template.find('.row-number').text(rowIndex + 1);

            // Set batasan tanggal jika periode sudah dipilih
            let periode = $('input[name="periode"]').val();
            if (periode) {
                let [year, month] = periode.split('-');
                let lastDay = new Date(year, month, 0).getDate();

                template.find('input[type="date"]')
                    .attr('min', `${periode}-01`)
                    .attr('max', `${periode}-${lastDay}`);
            }

            // Tambahkan baris baru ke container
            $('#detail-container').append(template);

            // Update nomor baris untuk semua baris
            updateRowNumbers();
        });

        // Fungsi untuk memperbarui nomor baris
        function updateRowNumbers() {
            $('.row-number').each(function(index) {
                $(this).text(index + 1);
            });
        }

        // Handler untuk perubahan BBM
        $(document).on('change', 'select[name="bbm_id"]', function() {
            // Hitung ulang total untuk semua baris
            $('.liter').each(function() {
                calculateTotal($(this));
            });

            // Hitung total keseluruhan
            calculateTotals();
        });

        // Fungsi hitung total harga per baris
        function calculateTotal(literInput) {
            let harga = $('select[name="bbm_id"] option:selected').data('harga') || 0;
            let liter = parseFloat(literInput.val()) || 0;
            let total = harga * liter;

            // Update nilai total harga
            literInput.closest('tr').find('.total-harga').val(total);
            literInput.closest('tr').find('.total-harga-display').val('Rp ' + total.toLocaleString('id'));
        }

        // Handler untuk hapus baris
        $(document).on('click', '.delete-row', function() {
            if ($('.detail-row').length > 1) {
                $(this).closest('tr').remove();

                // Update nomor baris setelah penghapusan
                updateRowNumbers();

                // Hitung ulang total keseluruhan
                calculateTotals();
            } else {
                // Jika hanya ada satu baris, tampilkan pesan
                alert('Minimal harus ada satu baris detail');
            }
        });

        // Fungsi untuk menghitung total liter dan harga keseluruhan
        function calculateTotals() {
            let totalLiter = 0;
            let totalHarga = 0;

            $('.liter').each(function() {
                let liter = parseFloat($(this).val()) || 0;
                let harga = parseFloat($(this).closest('tr').find('.total-harga').val()) || 0;

                totalLiter += liter;
                totalHarga += harga;
            });

            // Update tampilan total
            $('#total-liter').text(totalLiter.toFixed(2));
            $('#total-harga').text(totalHarga.toLocaleString('id'));

            // Cek apakah melebihi saldo
            let sisaSaldo = parseFloat($('#saldo-periode').text()) || 200;
            if (totalLiter > sisaSaldo) {
                $('#total-liter').addClass('text-danger');
                $('#sisa-saldo').closest('.alert').removeClass('alert-info').addClass('alert-warning');
            } else {
                $('#total-liter').removeClass('text-danger');
                $('#sisa-saldo').closest('.alert').removeClass('alert-warning').addClass('alert-info');
            }
        }

        // Handler untuk input liter
        $(document).on('input', '.liter', function() {
            calculateTotal($(this));
            calculateTotals();
        });
    });
    $(document).ready(function() {
        initializeSelect2();
        initializeEventHandlers();

        let rowIndex = 0;

        function initializeSelect2() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...',
                allowClear: true
            });
        }

        function initializeEventHandlers() {
            $('#periode').on('change', handlePeriodeChange);
            $(document).on('input', '.liter', handleLiterInput);
            $('#bbm_id').on('change', handleBBMChange);
            $('#add-row').on('click', addNewRow);
            $(document).on('click', '.delete-row', deleteRow);
            $('#claimForm').on('submit', validateForm);
        }

        function handlePeriodeChange() {
            const periode = $(this).val();
            if (!periode) return;

            $.ajax({
                url: `/claims/sisa-saldo/${encodeURIComponent(periode)}`,
                method: 'GET',
                success: function(response) {
                    $('#saldo-periode').text(response.sisa_saldo);
                    calculateTotals();
                    updateDateConstraints(periode);
                },
                error: function(xhr) {
                    console.error('Error fetching saldo:', xhr);
                    alert('Gagal mengambil data saldo');
                }
            });
        }

        function updateDateConstraints(periode) {
            const [year, month] = periode.split('-');
            const lastDay = new Date(year, month, 0).getDate();
            const minDate = `${periode}-01`;
            const maxDate = `${periode}-${lastDay}`;

            $('.detail-row input[type="date"]').attr({
                'min': minDate,
                'max': maxDate
            });
        }

        function handleLiterInput() {
            calculateTotal($(this));
            calculateTotals();
        }

        function handleBBMChange() {
            $('.liter').each(function() {
                calculateTotal($(this));
            });
            calculateTotals();
        }

        function calculateTotal(literInput) {
            const harga = $('#bbm_id option:selected').data('harga') || 0;
            const liter = parseFloat(literInput.val()) || 0;
            const total = harga * liter;

            const row = literInput.closest('tr');
            row.find('.total-harga').val(total);
            row.find('.total-harga-display').val(total.toLocaleString('id'));
        }

        function calculateTotals() {
            let totalLiter = 0;
            let totalHarga = 0;

            $('.liter').each(function() {
                const liter = parseFloat($(this).val()) || 0;
                const harga = parseFloat($(this).closest('tr').find('.total-harga').val()) || 0;

                totalLiter += liter;
                totalHarga += harga;
            });

            updateTotalDisplay(totalLiter, totalHarga);
            checkLimits(totalLiter);
        }

        function updateTotalDisplay(totalLiter, totalHarga) {
            $('#total-liter').text(totalLiter.toFixed(2));
            $('#total-harga').text(totalHarga.toLocaleString('id'));
        }

        function checkLimits(totalLiter) {
            const sisaSaldo = parseFloat($('#saldo-periode').text()) || 200;
            const alertElement = $('#sisa-saldo').closest('.alert');

            if (totalLiter > sisaSaldo) {
                $('#total-liter').addClass('text-danger');
                alertElement.removeClass('alert-info').addClass('alert-warning');

                // Tampilkan tanda wajib pada catatan
                $('#catatan-required').removeClass('d-none');
                $('#catatan-help').removeClass('d-none');

                // Tambahkan validasi
                if (!$('#catatan').val().trim()) {
                    $('#catatan').addClass('is-invalid');
                }
            } else {
                $('#total-liter').removeClass('text-danger');
                alertElement.removeClass('alert-warning').addClass('alert-info');

                // Sembunyikan tanda wajib pada catatan
                $('#catatan-required').addClass('d-none');
                $('#catatan-help').addClass('d-none');
                $('#catatan').removeClass('is-invalid');
            }

            handleExcessWarning(totalLiter);
        }


        function handleExcessWarning(totalLiter) {
            const warningMessage = $('#warning-message');
            if (totalLiter > 200) {
                if (!warningMessage.length) {
                    $('#claimForm').prepend(`
                        <div id="warning-message" class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Perhatian!</strong> Total penggunaan BBM melebihi batas 200 liter. 
                            Klaim ini akan ditandai dengan catatan khusus.
                        </div>
                    `);
                }
            } else {
                warningMessage.remove();
            }
        }

        function addNewRow() {
            rowIndex++;
            const newRow = $('.detail-row').first().clone();

            newRow.find('input').val('');
            newRow.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace(/\d+/, rowIndex));
            });

            newRow.attr('data-index', rowIndex);

            const periode = $('#periode').val();
            if (periode) {
                updateDateConstraints(periode);
            }

            $('#detail-container').append(newRow);
            updateRowNumbers();
        }

        function deleteRow() {
            if ($('.detail-row').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
                calculateTotals();
            } else {
                alert('Minimal harus ada satu baris detail');
            }
        }

        function updateRowNumbers() {
            $('.row-number').each(function(index) {
                $(this).text(index + 1);
            });
        }

        function validateForm(e) {
            const totalLiter = parseFloat($('#total-liter').text()) || 0;
            const sisaSaldo = parseFloat($('#saldo-periode').text()) || 200;

            if (!validateRequiredFields()) {
                e.preventDefault();
                return false;
            }

            // Cek apakah melebihi batas dan catatan kosong
            if (totalLiter > sisaSaldo && !$('#catatan').val().trim()) {
                alert('Anda harus mengisi catatan ketika penggunaan BBM melebihi batas saldo');
                $('#catatan').addClass('is-invalid').focus();
                e.preventDefault();
                return false;
            }

            if (totalLiter > 200 && !confirm('Total penggunaan BBM melebihi batas 200 liter. Apakah Anda yakin ingin melanjutkan?')) {
                e.preventDefault();
                return false;
            }

            return true;
        }

        // Tambahkan validasi real-time untuk catatan
        $('#catatan').on('input', function() {
            const totalLiter = parseFloat($('#total-liter').text()) || 0;
            const sisaSaldo = parseFloat($('#saldo-periode').text()) || 200;

            if (totalLiter > sisaSaldo && !$(this).val().trim()) {
                $(this).addClass('is-invalid');
                if (!$('#catatan-feedback').length) {
                    $(this).after('<div id="catatan-feedback" class="invalid-feedback">Catatan wajib diisi ketika penggunaan BBM melebihi batas saldo</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $('#catatan-feedback').remove();
            }
        });

        function validateRequiredFields() {
            if (!$('#periode').val() || !$('#kendaraan_id').val() || !$('#bbm_id').val()) {
                alert('Silakan lengkapi data periode, kendaraan, dan BBM');
                return false;
            }

            let isValid = true;
            $('.detail-row input[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                alert('Silakan lengkapi semua detail penggunaan BBM');
                return false;
            }

            return true;
        }

        function appendExcessNote() {
            const catatan = $('#catatan');
            const warningText = '[PERHATIAN: Total penggunaan BBM periode ini melebihi batas 200 liter]';

            if (!catatan.val().includes(warningText)) {
                catatan.val((catatan.val() + ' ' + warningText).trim());
            }
        }
    });

    // Handler untuk sisa saldo
    function updateSisaSaldoDisplay(sisaSaldo) {
        $('#saldo-periode').text(sisaSaldo);

        // Tambahkan indikator visual berdasarkan jumlah sisa saldo
        const $saldoAlert = $('#saldo-alert');

        if (sisaSaldo <= 0) {
            $saldoAlert.removeClass('alert-info alert-warning').addClass('alert-danger');
            $saldoAlert.find('.badge').removeClass('badge-primary').addClass('badge-danger');
        } else if (sisaSaldo < 50) {
            $saldoAlert.removeClass('alert-info alert-danger').addClass('alert-warning');
            $saldoAlert.find('.badge').removeClass('badge-primary badge-danger').addClass('badge-warning');
        } else {
            $saldoAlert.removeClass('alert-warning alert-danger').addClass('alert-info');
            $saldoAlert.find('.badge').removeClass('badge-warning badge-danger').addClass('badge-primary');
        }

        // Perbarui total untuk validasi
        calculateTotals();
    }

    // Handler untuk perubahan periode
    $('input[name="periode"]').on('change', function() {
        let periode = $(this).val();
        if (!periode) return;

        // Tambahkan penanganan error
        $.ajax({
            url: `/claims/sisa-saldo/${periode}`,
            method: 'GET',
            success: function(response) {
                const sisaSaldo = parseFloat(response.sisa_saldo);
                $('#saldo-periode').text(sisaSaldo);

                // Update tampilan sisa saldo
                updateSisaSaldoDisplay(sisaSaldo);

                // Update batasan tanggal
                let [year, month] = periode.split('-');
                let lastDay = new Date(year, month, 0).getDate();

                $('.detail-row input[type="date"]').each(function() {
                    $(this).attr({
                        'min': `${periode}-01`,
                        'max': `${periode}-${lastDay}`
                    });
                });
            },
            error: function(xhr) {
                console.error('Error fetching saldo:', xhr);
                $('#saldo-periode').text('200.00');
                alert('Gagal mengambil data sisa saldo. Menggunakan nilai default 200 liter.');
            }
        });
    });

    // Fungsi untuk menghitung total dan memvalidasi terhadap sisa saldo
    function calculateTotals() {
        let totalLiter = 0;
        let totalHarga = 0;

        $('.liter').each(function() {
            let liter = parseFloat($(this).val()) || 0;
            let harga = parseFloat($(this).closest('tr').find('.total-harga').val()) || 0;

            totalLiter += liter;
            totalHarga += harga;
        });

        // Update tampilan total
        $('#total-liter').text(totalLiter.toFixed(2));
        $('#total-harga').text(totalHarga.toLocaleString('id'));

        // Validasi terhadap sisa saldo
        const sisaSaldo = parseFloat($('#saldo-periode').text()) || 0;

        if (totalLiter > sisaSaldo) {
            $('#total-liter').addClass('text-danger font-weight-bold');

            if (!$('#warning-message').length) {
                $('#claimForm').prepend(`
                <div id="warning-message" class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Perhatian!</strong> Total penggunaan BBM (${totalLiter.toFixed(2)} liter) melebihi sisa saldo (${sisaSaldo} liter).
                    Klaim ini akan ditandai dengan catatan khusus.
                </div>
            `);
            }
        } else {
            $('#total-liter').removeClass('text-danger font-weight-bold');
            $('#warning-message').remove();
        }

        // Cek juga apakah melebihi batas 200 liter
        if (totalLiter > 200) {
            if (!$('#limit-warning').length) {
                $('#claimForm').prepend(`
                <div id="limit-warning" class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <strong>Peringatan!</strong> Total penggunaan BBM melebihi batas 200 liter per periode.
                    Harap sesuaikan jumlah penggunaan atau hubungi admin.
                </div>
            `);
            }
        } else {
            $('#limit-warning').remove();
        }
    }
    // Handler untuk perubahan periode
    $('input[name="periode"]').on('change', function() {
        let periode = $(this).val();
        if (!periode) return;

        // Ambil data sisa saldo dari server
        $.get(`/claims/sisa-saldo/${periode}`, function(response) {
            const sisaSaldo = parseFloat(response.sisa_saldo);
            updateSisaSaldoDisplay(sisaSaldo);

            // Update batasan tanggal
            let [year, month] = periode.split('-');
            let lastDay = new Date(year, month, 0).getDate();

            $('.detail-row input[type="date"]').each(function() {
                $(this).attr({
                    'min': `${periode}-01`,
                    'max': `${periode}-${lastDay}`
                });
            });
        }).fail(function(xhr) {
            console.error('Error fetching saldo:', xhr);
            alert('Gagal mengambil data sisa saldo. Silakan coba lagi.');
        });
    });
</script>
@endpush