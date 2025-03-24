<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Claim BBM - {{ $claim->klaim_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            border: 1px solid #000;
            padding: 15px;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .header img {
            width: 120px;
            height: auto;
        }

        .header-info {
            text-align: right;
            font-size: 14px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table td {
            padding: 5px;
            border-bottom: 1px solid #fff;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .data-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td,
        .summary-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .data-table th {
            background-color: rgb(255, 255, 255);
        }

        .summary {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .summary div {
            width: 48%;
        }

        .signature {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            height: 50px;
            border: 1px solid #000;
            text-align: center;
            padding-top: 30px;
            margin-top: 10px;
        }

        .signature p {
            margin: 5px 0;
        }

        .footer-notes {
            font-style: italic;
            margin-top: 20px;
            font-size: 11px;
        }

        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .id-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            background-color: #dc3545;
            border-radius: 4px;
        }

        .user-box {
            display: inline-block;
            /* Agar kotak hanya sebesar teks */
            border: 1px solid #000;
            /* Garis tepi kotak */
            padding: 2px 5px;
            /* Ruang di dalam kotak */
            border-radius: 3px;
            /* Sudut kotak yang membulat */
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="{{ public_path('/dist/img/image001.png') }}" alt="Sinarmas Logo">
            <div class="header-info">
                <p>Periode: {{ Carbon\Carbon::parse($claim->periode)->format('M-Y') }}</p>
            </div>
        </div>

        <div class="title">Form Claim BBM</div>

        <!-- Informasi Pemohon -->
        <table class="info-table">
            <tr>
                <td>Nama Pemohon: <b>{{ $claim->user->nama }}</b></td>
                <td>NIK: {{ $claim->user->nik }}</td>
            </tr>
            <tr>
                <td>Posisi / Dept: {{ $claim->user->roles->pluck('nama')->implode(', ') }} {{ $claim->user->department->nama_department }} / {{ $claim->user->department->cost_center }}</td>
                <td>Cost Center :{{ $claim->user->department->cost_center }}</td>

            </tr>
            <tr>
                <td>Jumlah Dana: <b>Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</b></td>
                <td>Jenis BBM: {{ $claim->bbm->nama_bbm }}</td>
            </tr>
            <tr>
                <td>Keperluan: BBM {{ $claim->kendaraan->keperluan }} {{ $claim->kendaraan->no_plat }}</td>
            </tr>
        </table>

        <!-- Tabel Data BBM -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
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
                @foreach($claim->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $claim->no_acc }}</td>
                    <td>BBM mobil ({{ $claim->kendaraan->no_plat }})</td>
                    <td>{{ Carbon\Carbon::parse($detail->tanggal)->format('d.m.Y') }}</td>
                    <td>{{ number_format($detail->km, 3, ',', '.') }}</td>
                    <td>Rp {{ number_format($detail->bbm->harga_bbm) }}</td>
                    <td>{{ number_format($detail->liter, 2) }} L</td>
                    <td>Rp {{ number_format($detail->total_harga) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="6" class="text-right text-bold">JUMLAH YANG DITAGIHKAN</td>
                    <td class="text-bold">{{ number_format($claim->total_penggunaan_liter, 2) }} L</td>
                    <td class="text-bold">Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Ringkasan Saldo -->
        <div class="summary">
            <div>
                <table class="summary-table">
                    <tr>
                        <td>Saldo Awal</td>
                        <td>
                            @php
                            $saldoAwal = isset($usedBalanceByPeriodAndUser[$claim->id]['saldo_awal'])
                            ? $usedBalanceByPeriodAndUser[$claim->id]['saldo_awal']
                            : ($claim->saldoBBM ? $claim->saldoBBM->saldo_awal : 200);
                            @endphp
                            {{ number_format($saldoAwal, 1) }} L
                        </td>
                    </tr>
                    <tr>
                        <td>Penggunaan BBM</td>
                        <td>{{ number_format($claim->total_penggunaan_liter, 1) }} L</td>
                    </tr>
                    <tr>
                        <td>Sisa Saldo</td>
                        <td>
                            @php
                            $sisaSaldo = isset($usedBalanceByPeriodAndUser[$claim->id]['sisa_saldo'])
                            ? $usedBalanceByPeriodAndUser[$claim->id]['sisa_saldo']
                            : ($claim->saldoBBM ? $claim->saldoBBM->sisa_saldo : (200 - $claim->total_penggunaan_liter));
                            @endphp
                            {{ number_format($sisaSaldo, 1) }} L
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div>
                <p>Diminta oleh:</p>
                <div class="signature-box"></div>
                <p>(<span class="user-box">User </span>)</p> <!-- Menambahkan kotak di sekitar "User " -->
                <p><b>{{ $claim->user->nama }}</b></p>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="footer-notes">
            @if($claim->catatan)
            <p>*Catatan: {{ $claim->catatan }}</p>
            @endif
            <p>*Minimal setingkat section head</p>
            <p>*Wajib melampirkan seluruh nota print pembelian</p>
        </div>
    </div>
</body>

</html>