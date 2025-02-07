@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-file-invoice-dollar me-2"></i> Buat Klaim BBM
            </h5>
        </div>

        <form action="{{ route('claims.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="alert alert-info">
                    <span id="sisa-saldo">Sisa Saldo: 200.00 liter</span>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode</label>
                            <input type="month" name="periode" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kendaraan</label>
                            <select name="kendaraan_id" class="form-control select2" required>
                                <option value="">Pilih Kendaraan</option>
                                @foreach($kendaraans as $kendaraan)
                                @if($kendaraan->user_id == auth()->user()->id)
                                <option value="{{ $kendaraan->id }}">{{ $kendaraan->nama_kendaraan }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>BBM</label>
                            <select name="bbm_id" class="form-control select2" required>
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

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th>Tanggal</th>
                                <th>KM</th>
                                <th>Liter</th>
                                <th>Total Harga</th>
                                <th width="50px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail-container">
                            <tr class="detail-row">
                                <td>
                                    <input type="date" name="details[0][tanggal]" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" name="details[0][km]" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="details[0][liter]" class="form-control liter" required>
                                </td>
                                <td>
                                    <input type="hidden" name="details[0][total_harga]" class="total-harga">
                                    <input type="text" class="form-control total-harga-display" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-secondary" id="add-row">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="card-footer bg-white">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('claims.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let sisaSaldoGlobal = 200.00;
        let rowIndex = 0;

        // Handler untuk perubahan periode
        $('input[name="periode"]').on('change', function() {
            let periode = $(this).val();
            $.get(`/claims/sisa-saldo/${periode}`, function(response) {
                sisaSaldoGlobal = response.sisa_saldo;
                $('#sisa-saldo').text(`Sisa Saldo: ${sisaSaldoGlobal} liter`);
            });
        });

        // Fungsi tambah baris baru
        $('#add-row').click(function() {
            rowIndex++;
            let template = $('.detail-row').first().clone();
            template.find('input').val('');
            template.find('[name]').each(function() {
                let name = $(this).attr('name');
                $(this).attr('name', name.replace('0', rowIndex));
            });
            $('#detail-container').append(template);
        });

        // Handler untuk input liter dan validasi saldo
        $(document).on('input', '.liter', function() {
            let totalLiter = 0;
            $('.liter').each(function() {
                totalLiter += parseFloat($(this).val()) || 0;
            });

            if (totalLiter > sisaSaldoGlobal) {
                alert(`Total penggunaan (${totalLiter} liter) melebihi sisa saldo (${sisaSaldoGlobal} liter)`);
                $(this).val('');
                return;
            }

            calculateTotal($(this));
        });

        // Handler untuk perubahan BBM
        $(document).on('change', 'select[name="bbm_id"]', function() {
            $('.liter').each(function() {
                calculateTotal($(this));
            });
        });

        // Fungsi hitung total harga
        function calculateTotal(literInput) {
            let harga = $('select[name="bbm_id"] option:selected').data('harga') || 0;
            let liter = literInput.val() || 0;
            let total = harga * liter;

            literInput.closest('tr').find('.total-harga').val(total);
            literInput.closest('tr').find('.total-harga-display').val('Rp ' + total.toLocaleString('id'));
        }

        // Handler untuk hapus baris
        $(document).on('click', '.delete-row', function() {
            if ($('.detail-row').length > 1) {
                $(this).closest('tr').remove();
            }
        });
    });
</script>
@endpush