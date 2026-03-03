<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Cuti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            color: #A52A2A; /* Merah sesuai tema */
        }
        .header p {
            margin: 5px 0;
        }
        hr {
            border: 0;
            border-top: 2px solid #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PEMERINTAH KABUPATEN SEMARANG</h2>
        <h3>REKAPITULASI PENGAJUAN CUTI PEGAWAI</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nama Pegawai</th>
                <th style="width: 15%">Unit Kerja</th>
                <th style="width: 15%">Jenis Cuti</th>
                <th style="width: 15%">Tanggal Mulai</th>
                <th style="width: 15%">Tanggal Selesai</th>
                <th style="width: 15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->user->name ?? '-' }}</strong><br>
                    <span style="font-size: 10px; color: #555;">NIP: {{ $item->user->nip ?? '-' }}</span>
                </td>
                <td>{{ $item->user->bidang_unit ?? '-' }}</td>
                <td>{{ $item->jenis_cuti }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}</td>
                <td style="text-align: center;">
                    @if($item->status == 'approved')
                        <span class="status-approved">Disetujui</span>
                    @elseif($item->status == 'rejected')
                        <span class="status-rejected">Ditolak</span>
                    @else
                        <span class="status-pending">Menunggu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data pengajuan cuti pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,<br>Kepala Dinas</p>
        <br><br><br>
        <p>__________________________</p>
    </div>

</body>
</html>