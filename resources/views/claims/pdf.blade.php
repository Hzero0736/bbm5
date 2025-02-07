<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Claim BBM</title>
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

        .header div {
            text-align: right;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table td {
            padding: 5px;
            border-bottom: 1px solid #000;
            /* Garis bawah konsisten */
        }

        .info-table tr:last-child td {
            border-bottom: none;
            /* Hilangkan garis bawah pada baris terakhir */
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
            background-color: #f2f2f2;
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
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="{{ public_path('/dist/img/image001.png') }}" alt="Sinarmas Logo">
            <div>
                <p>Periode: {{ Carbon\Carbon::parse($claim->periode)->format('M-Y') }}</p>
            </div>
        </div>

        <!-- Title -->
        <div class="title">Form Claim BBM</div>

        <!-- Informasi Pemohon -->
        <table class="info-table">
            <tr>
                <td>Nama Pemohon: <b>{{ $claim->user->nama }}</b></td>
                <td>NIK: {{ $claim->user->nik }}</td>
            </tr>
            <tr>
                <td>Posisi / Dept: {{ $claim->user->posisi }} / {{ $departments->first()->kode_department }}</td>
                <td>Cost Center: {{ $departments->first()->cost_center }}</td>
            </tr>
            <tr>
                <td>Jumlah Dana: <b>Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</b></td>
                <td></td> <!-- Kolom kosong untuk menjaga struktur tabel -->
            </tr>
            <tr>
                <td>Keperluan: BBM {{ $claim->kendaraan->keperluan }} {{ $claim->kendaraan->no_plat }}</td>
                <td>Jenis BBM: {{ $claim->bbm->nama_bbm }}</td>
            </tr>
        </table>

        <!-- Tabel Data BBM -->
        <table class="data-table">
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
                <tr>
                    <td colspan="5" style="text-align: right;"><b>JUMLAH YANG DITAGIHKAN</b></td>
                    <td><b>{{ number_format($claim->total_penggunaan_liter, 2) }}</b> L</td>
                    <td><b>Rp {{ number_format($claim->jumlah_dana, 0, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>

        <!-- Ringkasan Saldo -->
        <div class="summary">
            <div>
                <table class="summary-table">
                    <tr>
                        <td>Saldo Awal</td>
                        <td>{{ number_format($claim->saldo_liter) }}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Penggantian BBM</td>
                        <td>{{ number_format($claim->total_penggunaan_liter, 2) }} L</td>
                    </tr>
                    <tr>
                        <td>Jumlah yang sudah diambil</td>
                        <td>{{ number_format($claim->total_penggunaan_liter, 2) }} L</td>
                    </tr>
                    <tr>
                        <td>Sisa di Warehouse</td>
                        <td>{{ number_format($claim->sisa_saldo_liter, 2) }} L</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div>
                <p>Diminta oleh:</p>
                <div class="signature-box">User</div>
                <p><b>{{ $claim->user->nama }}</b></p>
            </div>
            <div>
                <p>*Minimal setingkat section head</p>
                <p>*Wajib melampirkan seluruh nota print pembelian</p>
            </div>
        </div>
    </div>
</body>

</html>