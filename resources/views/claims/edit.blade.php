@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Edit Klaim BBM</h3>
        </div>

        <form action="{{ route('claims.update', $claim->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Periode</label>
                            <input type="month" name="periode" class="form-control" value="{{ $claim->periode->format('Y-m') }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Kendaraan</label>
                            <select name="kendaraan_id" class="form-control select2" required>
                                @foreach($kendaraans as $kendaraan)
                                <option value="{{ $kendaraan->id }}" {{ $kendaraan->id == $claim->kendaraan_id ? 'selected' : '' }}>
                                    {{ $kendaraan->nama_kendaraan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">BBM</label>
                            <select name="bbm_id" class="form-control select2" required>
                                @foreach($bbms as $bbm)
                                <option value="{{ $bbm->id }}" data-harga="{{ $bbm->harga_bbm }}" {{ $bbm->id == $claim->bbm_id ? 'selected' : '' }}>
                                    {{ $bbm->nama_bbm }} - Rp {{ number_format($bbm->harga_bbm, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Detail Section -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Detail Pengisian BBM</h5>
                    </div>
                    <div class="card-body">
                        <div id="detail-container">
                            @foreach($claim->details as $index => $detail)
                            <div class="detail-item mb-4 p-3 border rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small font-weight-bold mb-2">Tanggal</label>
                                            <input type="date" name="details[{{ $index }}][tanggal]" class="form-control" value="{{ $detail->tanggal->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="small font-weight-bold mb-2">KM</label>
                                            <input type="number" name="details[{{ $index }}][km]" class="form-control" value="{{ $detail->km }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="small font-weight-bold mb-2">Liter</label>
                                            <input type="number" step="0.01" name="details[{{ $index }}][liter]" class="form-control liter" value="{{ $detail->liter }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="small font-weight-bold mb-2">Total Harga</label>
                                            <input type="text" class="form-control total-harga" value="Rp {{ number_format($detail->total_harga, 0, ',', '.') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <button type="button" class="btn btn-danger btn-delete-detail">
                                            <i class="fas fa-trash me-1"></i>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-secondary" id="add-detail">
                            <i class="fas fa-plus"></i> Tambah Detail
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('claims.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let detailIndex = {{ count($claim->details) }};

    $('#add-detail').click(function() {
        detailIndex++;
        let template = $('.detail-item').first().clone();
        template.find('input').val('');
        template.find('input, select').each(function() {
            let name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\d+/, detailIndex));
            }
        });
        $('#detail-container').append(template);
    });

    $(document).on('input', '.liter', function() {
        let row = $(this).closest('.detail-item');
        let harga = $('select[name="bbm_id"] option:selected').data('harga');
        let liter = $(this).val();
        let total = harga * liter;
        row.find('.total-harga').val('Rp ' + total.toLocaleString('id-ID'));
    });

    $(document).on('click', '.btn-delete-detail', function() {
        $(this).closest('.detail-item').remove();
    });
</script>
@endpush
@endsection
