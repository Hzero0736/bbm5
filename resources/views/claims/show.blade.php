@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Detail Klaim BBM</h3>
            <div>
                <a href="{{ route('claims.pdf', $claim->id) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('claims.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <!-- Data Pemohon -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Data Pemohon</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="150px">Nama Pemohon</th>
                                    <td>{{ $claim->user->nama }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $claim->user->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Posisi/Dept</th>
                                    <td>{{ $claim->user->posisi }} / {{ $departments->first()->kode_department }}</td>
                                </tr>
                                <tr>
                                    <th>Cost Center</th>
                                    <td>{{ $departments->first()->cost_center }}</td>
                                </tr>
                                <tr>
                                    <th>Keperluan</th>
                                    <td>{{ $claim->kendaraan->keperluan }} ({{ $claim->kendaraan->no_plat }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Data Klaim -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Data Klaim</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="150px">No ACC</th>
                                    <td>{{ $claim->no_acc }}</td>
                                </tr>
                                <tr>
                                    <th>Periode</th>
                                    <td>{{ Carbon\Carbon::parse($claim->periode)->format('F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Kendaraan</th>
                                    <td>{{ $claim->kendaraan->nama_kendaraan }} ({{ $claim->kendaraan->no_plat }})</td>
                                </tr>
                                <tr>
                                    <th>BBM</th>
                                    <td>{{ $claim->bbm->nama_bbm }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Saldo -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Ringkasan Saldo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Saldo Liter</span>
                                    <span class="info-box-number">{{ number_format($claim->saldo_liter, 2) }} L</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Penggunaan</span>
                                    <span class="info-box-number">{{ number_format($claim->total_penggunaan_liter, 2) }} L</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Dana</span>
                                    <span class="info-box-number">Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text">Sisa Saldo</span>
                                    <span class="info-box-number">{{ number_format($claim->sisa_saldo_liter, 2) }} L</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endsection