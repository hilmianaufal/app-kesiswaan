<?php

namespace App\Http\Controllers;

use App\Models\CounselingCase;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;
use App\Models\StudentDevelopmentNote;
use App\Models\StudentPermission;
use App\Models\StudentViolation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $filters = $this->validateFilters($request);

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->endOfMonth()->toDateString();

        $records = StudentAttendance::query()
            ->with([
                'student:id,name,nis,school_class_id',
                'schoolClass:id,name',
            ])
            ->whereBetween('attendance_date', [
                $dateFrom,
                $dateTo,
            ])
            ->when(
                filled($filters['class_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $filters['class_id']
                )
            )
            ->orderBy('attendance_date')
            ->orderBy('school_class_id')
            ->orderBy('student_id')
            ->get();

        $summary = [
            'Total Tercatat' => $records->count(),
            'Hadir' => $records
                ->where(
                    'status',
                    StudentAttendance::STATUS_PRESENT
                )
                ->count(),
            'Sakit' => $records
                ->where(
                    'status',
                    StudentAttendance::STATUS_SICK
                )
                ->count(),
            'Izin' => $records
                ->where(
                    'status',
                    StudentAttendance::STATUS_PERMIT
                )
                ->count(),
            'Alfa' => $records
                ->where(
                    'status',
                    StudentAttendance::STATUS_ABSENT
                )
                ->count(),
            'Terlambat' => $records
                ->where('is_late', true)
                ->count(),
        ];

        $rows = $records->map(
            fn (StudentAttendance $record): array => [
                $record->attendance_date
                    ->format('d-m-Y'),

                $record->student?->name ?? '-',
                $record->student?->nis ?? '-',
                $record->schoolClass?->name ?? '-',
                $record->status_label,

                $record->arrival_time
                    ? substr(
                        (string) $record->arrival_time,
                        0,
                        5
                    )
                    : '-',

                $record->departure_time
                    ? substr(
                        (string) $record->departure_time,
                        0,
                        5
                    )
                    : '-',

                $record->is_late ? 'Ya' : 'Tidak',
                $record->note ?: '-',
            ]
        )->all();

        return $this->tablePdf(
            title: 'Laporan Absensi Siswa',
            subtitle: $this->periodLabel(
                $dateFrom,
                $dateTo
            ),
            summary: $summary,
            headers: [
                'Tanggal',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Status',
                'Masuk',
                'Pulang',
                'Terlambat',
                'Catatan',
            ],
            rows: $rows,
            filename: 'laporan-absensi-'
                .$dateFrom.'-'.$dateTo.'.pdf'
        );
    }

    public function violations(Request $request)
    {
        $filters = $this->validateFilters($request);

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->endOfMonth()->toDateString();

        $records = StudentViolation::query()
            ->with([
                'student:id,name,nis,school_class_id',
                'schoolClass:id,name',
                'recorder:id,name',
            ])
            ->whereBetween('violation_date', [
                $dateFrom,
                $dateTo,
            ])
            ->when(
                filled($filters['class_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $filters['class_id']
                )
            )
            ->orderBy('violation_date')
            ->orderBy('student_id')
            ->get();

        $summary = [
            'Total Kasus' => $records->count(),
            'Total Poin' => $records->sum('points'),
            'Ringan' => $records
                ->where('category', 'light')
                ->count(),
            'Sedang' => $records
                ->where('category', 'medium')
                ->count(),
            'Berat' => $records
                ->where('category', 'heavy')
                ->count(),
            'Belum Selesai' => $records
                ->whereIn('follow_up_status', [
                    StudentViolation::STATUS_PENDING,
                    StudentViolation::STATUS_IN_PROGRESS,
                ])
                ->count(),
        ];

        $rows = $records->map(
            fn (StudentViolation $record): array => [
                $record->violation_date
                    ->format('d-m-Y'),

                $record->student?->name ?? '-',
                $record->student?->nis ?? '-',
                $record->schoolClass?->name ?? '-',
                $record->violation_name,
                $record->category_label,
                $record->points,
                $record->follow_up_status_label,
                $record->recorder?->name ?? '-',
            ]
        )->all();

        return $this->tablePdf(
            title: 'Laporan Pelanggaran Siswa',
            subtitle: $this->periodLabel(
                $dateFrom,
                $dateTo
            ),
            summary: $summary,
            headers: [
                'Tanggal',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Pelanggaran',
                'Kategori',
                'Poin',
                'Tindak Lanjut',
                'Pencatat',
            ],
            rows: $rows,
            filename: 'laporan-pelanggaran-'
                .$dateFrom.'-'.$dateTo.'.pdf'
        );
    }

    public function achievements(Request $request)
    {
        $filters = $this->validateFilters($request);

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->endOfMonth()->toDateString();

        $records = StudentAchievement::query()
            ->with([
                'student:id,name,nis,school_class_id',
                'schoolClass:id,name',
                'recorder:id,name',
                'verifier:id,name',
            ])
            ->whereBetween('achievement_date', [
                $dateFrom,
                $dateTo,
            ])
            ->when(
                filled($filters['class_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $filters['class_id']
                )
            )
            ->orderBy('achievement_date')
            ->orderBy('student_id')
            ->get();

        $summary = [
            'Total Prestasi' => $records->count(),
            'Terverifikasi' => $records
                ->where(
                    'status',
                    StudentAchievement::STATUS_VERIFIED
                )
                ->count(),
            'Menunggu' => $records
                ->where(
                    'status',
                    StudentAchievement::STATUS_PENDING
                )
                ->count(),
            'Ditolak' => $records
                ->where(
                    'status',
                    StudentAchievement::STATUS_REJECTED
                )
                ->count(),
            'Poin Terverifikasi' => $records
                ->where(
                    'status',
                    StudentAchievement::STATUS_VERIFIED
                )
                ->sum('points'),
        ];

        $rows = $records->map(
            fn (StudentAchievement $record): array => [
                $record->achievement_date
                    ->format('d-m-Y'),

                $record->student?->name ?? '-',
                $record->student?->nis ?? '-',
                $record->schoolClass?->name ?? '-',
                $record->title,
                $record->category_label,
                $record->level_label,
                $record->rank_label,
                $record->points,
                $record->status_label,
            ]
        )->all();

        return $this->tablePdf(
            title: 'Laporan Prestasi Siswa',
            subtitle: $this->periodLabel(
                $dateFrom,
                $dateTo
            ),
            summary: $summary,
            headers: [
                'Tanggal',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Prestasi',
                'Kategori',
                'Tingkat',
                'Peringkat',
                'Poin',
                'Status',
            ],
            rows: $rows,
            filename: 'laporan-prestasi-'
                .$dateFrom.'-'.$dateTo.'.pdf'
        );
    }

    public function permissions(Request $request)
    {
        $filters = $this->validateFilters($request);

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->endOfMonth()->toDateString();

        $records = StudentPermission::query()
            ->with([
                'student:id,name,nis,school_class_id',
                'schoolClass:id,name',
                'recorder:id,name',
                'approver:id,name',
            ])
            ->whereBetween('permission_date', [
                $dateFrom,
                $dateTo,
            ])
            ->when(
                filled($filters['class_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $filters['class_id']
                )
            )
            ->orderBy('permission_date')
            ->orderBy('student_id')
            ->get();

        $summary = [
            'Total Pengajuan' => $records->count(),
            'Menunggu' => $records
                ->where(
                    'status',
                    StudentPermission::STATUS_PENDING
                )
                ->count(),
            'Disetujui' => $records
                ->where(
                    'status',
                    StudentPermission::STATUS_APPROVED
                )
                ->count(),
            'Ditolak' => $records
                ->where(
                    'status',
                    StudentPermission::STATUS_REJECTED
                )
                ->count(),
            'Sudah Kembali' => $records
                ->where(
                    'status',
                    StudentPermission::STATUS_COMPLETED
                )
                ->count(),
        ];

        $rows = $records->map(
            fn (StudentPermission $record): array => [
                $record->permission_date
                    ->format('d-m-Y'),

                $record->student?->name ?? '-',
                $record->student?->nis ?? '-',
                $record->schoolClass?->name ?? '-',
                $record->type_label,

                $record->start_time
                    ? substr(
                        (string) $record->start_time,
                        0,
                        5
                    )
                    : '-',

                $record->expected_return_time
                    ? substr(
                        (string) $record->expected_return_time,
                        0,
                        5
                    )
                    : '-',

                $record->status_label,
                $record->reason,
                $record->approver?->name ?? '-',
            ]
        )->all();

        return $this->tablePdf(
            title: 'Laporan Perizinan Siswa',
            subtitle: $this->periodLabel(
                $dateFrom,
                $dateTo
            ),
            summary: $summary,
            headers: [
                'Tanggal',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Jenis Izin',
                'Jam Keluar',
                'Rencana Kembali',
                'Status',
                'Alasan',
                'Pemroses',
            ],
            rows: $rows,
            filename: 'laporan-perizinan-'
                .$dateFrom.'-'.$dateTo.'.pdf'
        );
    }

    public function counseling(Request $request)
    {
        $filters = $this->validateFilters($request);

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->endOfMonth()->toDateString();

        $query = CounselingCase::query()
            ->with([
                'student:id,name,nis,school_class_id',
                'schoolClass:id,name',
                'assignedTeacher:id,name',
            ])
            ->withCount('sessions')
            ->whereBetween('opened_at', [
                $dateFrom,
                $dateTo,
            ])
            ->when(
                filled($filters['class_id'] ?? null),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $filters['class_id']
                )
            );

        $this->restrictCounseling($query);

        $records = $query
            ->orderBy('opened_at')
            ->orderBy('student_id')
            ->get();

        $summary = [
            'Total Kasus' => $records->count(),
            'Kasus Aktif' => $records
                ->whereIn('status', [
                    CounselingCase::STATUS_OPEN,
                    CounselingCase::STATUS_IN_PROGRESS,
                    CounselingCase::STATUS_MONITORING,
                ])
                ->count(),
            'Terselesaikan' => $records
                ->whereIn('status', [
                    CounselingCase::STATUS_RESOLVED,
                    CounselingCase::STATUS_CLOSED,
                ])
                ->count(),
            'Prioritas Mendesak' => $records
                ->where(
                    'priority',
                    CounselingCase::PRIORITY_URGENT
                )
                ->count(),
            'Jumlah Pertemuan' => $records
                ->sum('sessions_count'),
        ];

        $rows = $records->map(
            fn (CounselingCase $record): array => [
                $record->opened_at->format('d-m-Y'),
                $record->case_number,
                $record->student?->name ?? '-',
                $record->student?->nis ?? '-',
                $record->schoolClass?->name ?? '-',
                $record->title,
                $record->category_label,
                $record->priority_label,
                $record->status_label,
                $record->sessions_count,
                $record->assignedTeacher?->name ?? '-',
            ]
        )->all();

        return $this->tablePdf(
            title: 'Laporan Bimbingan Konseling',
            subtitle: $this->periodLabel(
                $dateFrom,
                $dateTo
            ),
            summary: $summary,
            headers: [
                'Dibuka',
                'Nomor Kasus',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Judul Kasus',
                'Kategori',
                'Prioritas',
                'Status',
                'Pertemuan',
                'Guru BK',
            ],
            rows: $rows,
            filename: 'laporan-konseling-'
                .$dateFrom.'-'.$dateTo.'.pdf'
        );
    }

    public function studentProfile(Student $student)
    {
        $user = auth()->user();

        $student->load([
            'schoolClass.homeroomTeacher',

            'attendances' => fn ($query) => $query
                ->orderByDesc('attendance_date'),

            'developmentNotes' => function ($query) use (
                $user
            ): void {
                if (
                    ! $user->isAdmin()
                    && ! $user->isGuruBk()
                ) {
                    $query->where(function (
                        $query
                    ) use ($user): void {
                        $query
                            ->where('is_private', false)
                            ->orWhere(
                                'recorded_by',
                                $user->id
                            );
                    });
                }

                $query->orderByDesc('note_date');
            },

            'violations' => fn ($query) => $query
                ->orderByDesc('violation_date'),

            'achievements' => fn ($query) => $query
                ->orderByDesc('achievement_date'),

            'permissions' => fn ($query) => $query
                ->orderByDesc('permission_date'),
        ]);

        $counselingQuery = CounselingCase::query()
            ->with([
                'assignedTeacher',
                'sessions' => fn ($query) => $query
                    ->orderByDesc('session_date'),
            ])
            ->where('student_id', $student->id);

        $this->restrictCounseling(
            $counselingQuery
        );

        $counselingCases = $counselingQuery
            ->orderByDesc('opened_at')
            ->get();

        $summary = [
            'attendance_total' =>
                $student->attendances->count(),

            'present' => $student->attendances
                ->where(
                    'status',
                    StudentAttendance::STATUS_PRESENT
                )
                ->count(),

            'sick' => $student->attendances
                ->where(
                    'status',
                    StudentAttendance::STATUS_SICK
                )
                ->count(),

            'permit' => $student->attendances
                ->where(
                    'status',
                    StudentAttendance::STATUS_PERMIT
                )
                ->count(),

            'absent' => $student->attendances
                ->where(
                    'status',
                    StudentAttendance::STATUS_ABSENT
                )
                ->count(),

            'violation_cases' =>
                $student->violations->count(),

            'violation_points' =>
                $student->violations->sum('points'),

            'achievement_total' =>
                $student->achievements
                    ->where(
                        'status',
                        StudentAchievement::STATUS_VERIFIED
                    )
                    ->count(),

            'achievement_points' =>
                $student->achievements
                    ->where(
                        'status',
                        StudentAchievement::STATUS_VERIFIED
                    )
                    ->sum('points'),

            'permissions' =>
                $student->permissions->count(),

            'counseling' =>
                $counselingCases->count(),
        ];

        return Pdf::loadView(
            'reports.pdf.student-profile',
            [
                'student' => $student,
                'summary' => $summary,
                'counselingCases' =>
                    $counselingCases,
            ]
        )
            ->setPaper('a4', 'portrait')
            ->download(
                'profil-siswa-'
                .$student->nis
                .'-'.now()->format('Ymd').'.pdf'
            );
    }

    private function restrictCounseling(
        Builder $query
    ): void {
        $user = auth()->user();

        if (
            $user->isAdmin()
            || $user->isGuruBk()
        ) {
            return;
        }

        if ($user->isKepalaSekolah()) {
            $query->where(function (
                Builder $query
            ) use ($user): void {
                $query
                    ->where(
                        'confidentiality_level',
                        '!=',
                        CounselingCase::CONFIDENTIALITY_HIGHLY_CONFIDENTIAL
                    )
                    ->orWhere(
                        'assigned_to',
                        $user->id
                    )
                    ->orWhere(
                        'created_by',
                        $user->id
                    );
            });

            return;
        }

        $query->where(function (
            Builder $query
        ) use ($user): void {
            $query
                ->where(
                    'confidentiality_level',
                    CounselingCase::CONFIDENTIALITY_NORMAL
                )
                ->orWhere(
                    'assigned_to',
                    $user->id
                )
                ->orWhere(
                    'created_by',
                    $user->id
                );
        });
    }

    private function validateFilters(
        Request $request
    ): array {
        return $request->validate([
            'date_from' => [
                'nullable',
                'date',
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
            'class_id' => [
                'nullable',
                'integer',
                'exists:school_classes,id',
            ],
        ]);
    }

    private function periodLabel(
        string $dateFrom,
        string $dateTo
    ): string {
        return 'Periode '
            .date('d-m-Y', strtotime($dateFrom))
            .' sampai '
            .date('d-m-Y', strtotime($dateTo));
    }

    private function tablePdf(
        string $title,
        string $subtitle,
        array $summary,
        array $headers,
        array $rows,
        string $filename
    ) {
        return Pdf::loadView(
            'reports.pdf.table',
            [
                'title' => $title,
                'subtitle' => $subtitle,
                'summary' => $summary,
                'headers' => $headers,
                'rows' => $rows,
            ]
        )
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }
}
