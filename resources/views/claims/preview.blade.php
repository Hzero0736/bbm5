@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('dist/img/image001.png') }}" alt="Sinarmas Logo" height="50">
        <h3 class="mt-2">Form Claim BBM</h3>
    </div>

    <table class="table table-borderless">
        <tr>
            <td><strong>Nama Pemohon</strong></td>
            <td>: {{ $claim->user->nama }}</td>
            <td><strong>NIK</strong></td>
            <td>: {{ $claim->user->nik }}</td>
        </tr>
        <tr>
            <td><strong>Posisi / Dept</strong></td>
            <td>: {{ $claim->user->posisi }} / {{ $departments->first()->kode_department }}</td>
            <td><strong>Cost Center</strong></td>
            <td>: {{ $departments->first()->cost_center }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Dana</strong></td>
            <td>: Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</td>
            <td><strong>Periode</strong></td>
            <td>: {{ Carbon\Carbon::parse($claim->periode)->format('M-Y') }}</td>
        </tr>
        <tr>
            <td><strong>Keperluan</strong></td>
            <td>: {{ $claim->kendaraan->keperluan }}</td>
            <td><strong>Jenis BBM</strong></td>
            <td>: {{ $claim->bbm->nama_bbm }}</td>
        </tr>
    </table>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>No. Acc</th>
                <th>Uraian</th>
                <th>Tanggal</th>
                <th>Km</th>
                <th>Rp/Liter</th>
                <th>Liter</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($claim->details as $detail)
            <tr>
                <td>{{ $claim->no_acc }}</td>
                <td>BBM mobil ({{ $claim->kendaraan->no_plat }})</td>
                <td>{{ Carbon\Carbon::parse($detail->tanggal)->format('d.m.Y') }}</td>
                <td>{{ number_format($detail->km, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($detail->bbm->harga_bbm) }}</td>
                <td>{{ number_format($detail->liter, 2) }}</td>
                <td>Rp {{ number_format($detail->total_harga) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right">
        <strong>JUMLAH YANG DITAGIHKAN</strong>
        <p class="h5">{{ number_format($claim->total_penggunaan_liter, 2) }}- Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</p>
    </div>

    <table class="table table-bordered mt-4">
        <tr>
            <td><strong>Saldo Awal</strong></td>
            <td>{{ number_format($claim->saldo_liter) }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Penggantian BBM</strong></td>
            <td>{{ number_format($claim->total_penggunaan_liter, 2) }} Liter</td>
        </tr>
        <tr>
            <td><strong>Jumlah yang sudah diambil</strong></td>
            <td>{{ number_format($claim->total_penggunaan_liter, 2) }} Liter</td>
        </tr>
        <tr>
            <td><strong>Sisa di Warehouse</strong></td>
            <td>{{ number_format($claim->sisa_saldo_liter, 2) }} Liter</td>
        </tr>
    </table>

    <div class="mt-4">
        <p>* Minimal setingkat section head</p>
        <p>* Wajib melampirkan seluruh nota print pembelian</p>
    </div>

    <div class="text-center mt-4">
        <p><strong>User</strong></p>
        <p>{{ $claim->user->nama }}</p>
    </div>

    <div class="text-right mt-4">
        <a href="{{ route('claims.pdf', $claim->id) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </a>
    </div>
</div>
@endsection