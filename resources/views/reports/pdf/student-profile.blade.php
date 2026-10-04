<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Profil {{ $student->name }}</title>

    <style>
        @page {
            margin: 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        h1, h2, h3, p {
            margin-top: 0;
        }

        .header {
            border-bottom: 3px solid #4338ca;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1e1b4b;
        }

        .header p {
            margin: 5px 0 0;
            color: #64748b;
        }

        .section {
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #312e81;
            color: white;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .identity {
            width: 100%;
            border-collapse: collapse;
        }

        .identity td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px;
        }

        .identity td:first-child {
            width: 145px;
            color: #64748b;
        }

        .summary {
            width: 100%;
            border-spacing: 5px;
        }

        .summary td {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 8px;
            text-align: center;
        }

        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #312e81;
        }

        .summary-label {
            margin-top: 3px;
            font-size: 8px;
            color: #64748b;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #e0e7ff;
            border: 1px solid #a5b4fc;
            padding: 5px;
            font-size: 8px;
            text-align: left;
        }

        .table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
            font-size: 8px;
        }

        .empty {
            padding: 14px;
            text-align: center;
            color: #64748b;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            color: #64748b;
            font-size: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Profil Lengkap Siswa</h1>

        <p>
            Sistem Informasi Kesiswaan
        </p>

        <p>
            Dicetak pada {{ now()->format('d-m-Y H:i') }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">
            Identitas Siswa
        </div>

        <table class="identity">
            <tr>
                <td>Nama lengkap</td>
                <td><strong>{{ $student->name }}</strong></td>
            </tr>

            <tr>
                <td>NIS</td>
                <td>{{ $student->nis }}</td>
            </tr>

            <tr>
                <td>NISN</td>
                <td>{{ $student->nisn ?: '-' }}</td>
            </tr>

            <tr>
                <td>Kelas</td>
                <td>
                    {{ $student->schoolClass?->full_name ?? '-' }}
                </td>
            </tr>

            <tr>
                <td>Wali kelas</td>
                <td>
                    {{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}
                </td>
            </tr>

            <tr>
                <td>Jenis kelamin</td>
                <td>{{ $student->gender_label }}</td>
            </tr>

            <tr>
                <td>Tempat, tanggal lahir</td>
                <td>
                    {{ $student->birth_place ?: '-' }},
                    {{ $student->birth_date?->format('d-m-Y') ?? '-' }}
                </td>
            </tr>

            <tr>
                <td>Alamat</td>
                <td>{{ $student->address ?: '-' }}</td>
            </tr>

            <tr>
                <td>Nama ayah</td>
                <td>{{ $student->father_name ?: '-' }}</td>
            </tr>

            <tr>
                <td>Nama ibu</td>
                <td>{{ $student->mother_name ?: '-' }}</td>
            </tr>

            <tr>
                <td>Nama wali</td>
                <td>{{ $student->guardian_name ?: '-' }}</td>
            </tr>

            <tr>
                <td>Nomor orang tua</td>
                <td>{{ $student->parent_phone ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Ringkasan Kesiswaan
        </div>

        <table class="summary">
            <tr>
                <td>
                    <div class="summary-value">
                        {{ $summary['present'] }}
                    </div>
                    <div class="summary-label">Hadir</div>
                </td>

                <td>
                    <div class="summary-value">
                        {{ $summary['sick'] }}
                    </div>
                    <div class="summary-label">Sakit</div>
                </td>

                <td>
                    <div class="summary-value">
                        {{ $summary['permit'] }}
                    </div>
                    <div class="summary-label">Izin</div>
                </td>

                <td>
                    <div class="summary-value">
                        {{ $summary['absent'] }}
                    </div>
                    <div class="summary-label">Alfa</div>
                </td>

                <td>
                    <div class="summary-value">
                        {{ $summary['violation_points'] }}
                    </div>
                    <div class="summary-label">Poin Pelanggaran</div>
                </td>

                <td>
                    <div class="summary-value">
                        {{ $summary['achievement_points'] }}
                    </div>
                    <div class="summary-label">Poin Prestasi</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Riwayat Absensi
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Terlambat</th>
                    <th>Catatan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($student->attendances as $attendance)
                    <tr>
                        <td>
                            {{ $attendance->attendance_date->format('d-m-Y') }}
                        </td>
                        <td>{{ $attendance->status_label }}</td>
                        <td>
                            {{ $attendance->arrival_time ? substr((string) $attendance->arrival_time, 0, 5) : '-' }}
                        </td>
                        <td>
                            {{ $attendance->departure_time ? substr((string) $attendance->departure_time, 0, 5) : '-' }}
                        </td>
                        <td>
                            {{ $attendance->is_late ? 'Ya' : 'Tidak' }}
                        </td>
                        <td>{{ $attendance->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            Belum ada data absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Catatan Perkembangan
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Judul</th>
                    <th>Catatan</th>
                    <th>Tindak Lanjut</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($student->developmentNotes as $note)
                    <tr>
                        <td>
                            {{ $note->note_date->format('d-m-Y') }}
                        </td>
                        <td>{{ $note->category_label }}</td>
                        <td>{{ $note->type_label }}</td>
                        <td>{{ $note->title }}</td>
                        <td>{{ $note->description }}</td>
                        <td>{{ $note->follow_up ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            Belum ada catatan perkembangan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Pelanggaran Siswa
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelanggaran</th>
                    <th>Kategori</th>
                    <th>Poin</th>
                    <th>Kronologi</th>
                    <th>Tindakan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($student->violations as $violation)
                    <tr>
                        <td>
                            {{ $violation->violation_date->format('d-m-Y') }}
                        </td>
                        <td>{{ $violation->violation_name }}</td>
                        <td>{{ $violation->category_label }}</td>
                        <td>{{ $violation->points }}</td>
                        <td>{{ $violation->chronology }}</td>
                        <td>{{ $violation->action_taken ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            Tidak memiliki catatan pelanggaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Prestasi Siswa
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Prestasi</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Peringkat</th>
                    <th>Poin</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($student->achievements as $achievement)
                    <tr>
                        <td>
                            {{ $achievement->achievement_date->format('d-m-Y') }}
                        </td>
                        <td>{{ $achievement->title }}</td>
                        <td>{{ $achievement->category_label }}</td>
                        <td>{{ $achievement->level_label }}</td>
                        <td>{{ $achievement->rank_label }}</td>
                        <td>{{ $achievement->points }}</td>
                        <td>{{ $achievement->status_label }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            Belum ada data prestasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Perizinan Siswa
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Jam Keluar</th>
                    <th>Jam Kembali</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($student->permissions as $permission)
                    <tr>
                        <td>
                            {{ $permission->permission_date->format('d-m-Y') }}
                        </td>
                        <td>{{ $permission->type_label }}</td>
                        <td>{{ $permission->status_label }}</td>
                        <td>{{ $permission->reason }}</td>
                        <td>
                            {{ $permission->start_time ? substr((string) $permission->start_time, 0, 5) : '-' }}
                        </td>
                        <td>
                            {{ $permission->actual_return_time ? substr((string) $permission->actual_return_time, 0, 5) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            Belum ada riwayat perizinan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">
            Bimbingan Konseling
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Nomor Kasus</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Guru BK</th>
                    <th>Pertemuan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($counselingCases as $case)
                    <tr>
                        <td>{{ $case->case_number }}</td>
                        <td>{{ $case->opened_at->format('d-m-Y') }}</td>
                        <td>{{ $case->title }}</td>
                        <td>{{ $case->category_label }}</td>
                        <td>{{ $case->priority_label }}</td>
                        <td>{{ $case->status_label }}</td>
                        <td>
                            {{ $case->assignedTeacher?->name ?? '-' }}
                        </td>
                        <td>{{ $case->sessions->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            Tidak ada kasus konseling yang dapat ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dokumen profil siswa dibuat melalui Sistem Informasi Kesiswaan.
    </div>
</body>
</html>
