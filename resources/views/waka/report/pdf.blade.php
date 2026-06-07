<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a1a;
    }

    .page { padding: 36px 40px; }

    .header {
        border-bottom: 2.5px solid #1d4ed8;
        padding-bottom: 14px;
        margin-bottom: 20px;
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .school-name {
        font-size: 16px;
        font-weight: bold;
        color: #1d4ed8;
    }
    .school-sub {
        font-size: 10px;
        color: #6b7280;
        margin-top: 2px;
    }
    .doc-label {
        font-size: 10px;
        color: #6b7280;
        text-align: right;
    }
    .doc-label strong {
        display: block;
        font-size: 12px;
        color: #111827;
    }

    .info-card {
        background: #f0f7ff;
        border-left: 4px solid #1d4ed8;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 24px;
    }
    .info-row { display: flex; gap: 6px; }
    .info-label { color: #6b7280; min-width: 80px; }
    .info-value { font-weight: bold; color: #111827; }

    .rekap-title {
        font-size: 10px;
        font-weight: bold;
        color: #374151;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .summary-table th, .summary-table td { border: 1px solid #e5e7eb; padding: 8px; text-align: center; }
    .summary-table thead { background: #f3f4f6; }

    .table-title {
        font-size: 10px;
        font-weight: bold;
        color: #374151;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1d4ed8; color: white; }
    thead th {
        padding: 8px 10px;
        text-align: left;
        font-size: 10px;
        font-weight: bold;
    }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td {
        padding: 7px 10px;
        font-size: 10px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .sessions-badge {
        display: inline-block;
        background: #e0e7ff;
        color: #3730a3;
        font-size: 8px;
        font-weight: bold;
        padding: 1px 5px;
        border-radius: 10px;
        margin-left: 3px;
    }

    .checkin-badge {
        display: inline-block;
        background: #d1fae5;
        color: #065f46;
        font-size: 9px;
        font-weight: bold;
        padding: 2px 7px;
        border-radius: 10px;
    }

    .date-separator { background: #f8fafc !important; }
    .date-separator td {
        font-weight: bold;
        font-size: 10px;
        color: #6b7280;
        padding: 6px 10px;
        border-bottom: 1px solid #e5e7eb;
        border-top: 1px solid #e5e7eb;
    }

    .footer {
        margin-top: 30px;
        border-top: 1px solid #e5e7eb;
        padding-top: 14px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .footer-note {
        font-size: 9px;
        color: #9ca3af;
        line-height: 1.6;
    }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-top">
            <div>
                <div class="school-name">SMK NEGERI 3</div>
                <div class="school-sub">Laporan Kehadiran Guru</div>
            </div>
            <div class="doc-label">
                Periode
                <strong>{{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}</strong>
            </div>
        </div>
    </div>
    <div class="info-card">
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value">: {{ $teacher->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIP</span>
                <span class="info-value">: {{ $teacher->nip ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="rekap-title" style="margin-bottom: 10px; font-weight: bold; font-size: 11pt;">Rekapitulasi Kehadiran</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Hadir</th>
                <th>Tidak Hadir</th>
                <th>Total Jadwal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $rekap['hadir'] }}</td>
                <td>{{ $rekap['tidak'] }}</td>
                <td>{{ $rekap['total'] }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tabel --}}
    <div class="table-title">Detail Kehadiran Per Hari</div>
    <table>
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th style="width:85px">Tanggal</th>
                <th>Mata Pelajaran</th>
                <th style="width:70px">Kelas</th>
                <th style="width:55px">Ruang</th>
                <th style="width:80px">Jam</th>
                <th style="width:120px">Jurnal Pembelajaran</th>
                <th style="width:65px; text-align:center">Check In</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($grouped as $date => $rows)
                <tr class="date-separator">
                    <td colspan="8">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </td>
                </tr>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td style="color:#9ca3af">&mdash;</td>
                        <td>
                            {{ $row['subject'] }}
                            @if($row['sessions'] > 1)
                                <span class="sessions-badge">{{ $row['sessions'] }} sesi</span>
                            @endif
                        </td>
                        <td>{{ $row['rombel'] }}</td>
                        <td>{{ $row['classroom'] }}</td>
                        <td>{{ substr($row['start'], 0, 5) }} &ndash; {{ substr($row['end'], 0, 5) }}</td>
                        
                        {{-- Kolom Jurnal --}}
                        <td style="font-size: 9px; color: #4b5563;">
                            {{ $row['topic'] ?? '-' }}
                        </td>
                        
                        <td style="text-align:center">
                            @if($row['check_in'])
                                <span class="checkin-badge">{{ substr($row['check_in'], 0, 5) }}</span>
                            @else
                                <span style="color:#9ca3af">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#9ca3af; padding:20px;">
                        Tidak ada data kehadiran pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-note">
            Dicetak pada {{ now()->translatedFormat('d F Y') }}, pukul {{ now()->format('H:i') }} WIB<br>
            Dokumen ini digenerate secara otomatis oleh sistem.
        </div>
    </div>

</div>
</body>
</html>