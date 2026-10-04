<?php

namespace App\Livewire\Attendances;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public string $selectedDate = '';

    public string $selectedClassId = '';

    public string $search = '';

    public array $statuses = [];

    public array $arrivalTimes = [];

    public array $departureTimes = [];

    public array $lateFlags = [];

    public array $earlyLeaveFlags = [];

    public array $notes = [];

    public array $persisted = [];

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');

        $firstClass = SchoolClass::query()
            ->where('is_active', true)
            ->orderByDesc('academic_year')
            ->orderBy('level')
            ->orderBy('name')
            ->first();

        $this->selectedClassId = $firstClass
            ? (string) $firstClass->id
            : '';

        $this->loadAttendanceData();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadAttendanceData();
    }

    public function updatedSelectedClassId(): void
    {
        $this->search = '';

        $this->loadAttendanceData();
    }

    public function setStatus(
        int $studentId,
        string $status
    ): void {
        if (! array_key_exists(
            $status,
            StudentAttendance::statuses()
        )) {
            return;
        }

        $key = (string) $studentId;

        $this->statuses[$key] = $status;

        if ($status !== StudentAttendance::STATUS_PRESENT) {
            $this->arrivalTimes[$key] = '';
            $this->departureTimes[$key] = '';
            $this->lateFlags[$key] = false;
            $this->earlyLeaveFlags[$key] = false;
        }
    }

    public function markAllPresent(): void
    {
        if (! filled($this->selectedClassId)) {
            $this->dispatch(
                'swal:error',
                title: 'Kelas belum dipilih',
                text: 'Pilih kelas terlebih dahulu.'
            );

            return;
        }

        $studentIds = Student::query()
            ->where(
                'school_class_id',
                $this->selectedClassId
            )
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )
            ->pluck('id');

        foreach ($studentIds as $studentId) {
            $key = (string) $studentId;

            $this->statuses[$key] =
                StudentAttendance::STATUS_PRESENT;

            $this->arrivalTimes[$key] ??= '';
            $this->departureTimes[$key] ??= '';
            $this->lateFlags[$key] ??= false;
            $this->earlyLeaveFlags[$key] ??= false;
            $this->notes[$key] ??= '';
        }
    }

    public function reloadAttendance(): void
    {
        $this->loadAttendanceData();

        $this->dispatch(
            'swal:success',
            title: 'Data dimuat ulang',
            text: 'Data absensi dikembalikan sesuai database.'
        );
    }

    public function saveAll(): void
    {
        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $students = Student::query()
            ->where(
                'school_class_id',
                $validated['selectedClassId']
            )
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )
            ->get([
                'id',
                'school_class_id',
            ]);

        $filledAttendance = 0;

        DB::transaction(function () use (
            $students,
            $validated,
            &$filledAttendance
        ): void {
            foreach ($students as $student) {
                $key = (string) $student->id;

                $status = $validated['statuses'][$key]
                    ?? null;

                if (! filled($status)) {
                    continue;
                }

                $isPresent = $status ===
                    StudentAttendance::STATUS_PRESENT;

                StudentAttendance::query()->updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'attendance_date' => $validated['selectedDate'],
                    ],
                    [
                        'school_class_id' => $student->school_class_id,
                        'status' => $status,

                        'arrival_time' => $isPresent
                            && filled(
                                $validated['arrivalTimes'][$key]
                                ?? null
                            )
                                ? $validated['arrivalTimes'][$key]
                                : null,

                        'departure_time' => $isPresent
                            && filled(
                                $validated['departureTimes'][$key]
                                ?? null
                            )
                                ? $validated['departureTimes'][$key]
                                : null,

                        'is_late' => $isPresent
                            ? (bool) (
                                $validated['lateFlags'][$key]
                                ?? false
                            )
                            : false,

                        'is_early_leave' => $isPresent
                            ? (bool) (
                                $validated['earlyLeaveFlags'][$key]
                                ?? false
                            )
                            : false,

                        'note' => filled(
                            $validated['notes'][$key]
                            ?? null
                        )
                            ? trim($validated['notes'][$key])
                            : null,

                        'recorded_by' => auth()->id(),
                    ]
                );

                $this->persisted[$key] = true;

                $filledAttendance++;
            }
        });

        if ($filledAttendance === 0) {
            $this->dispatch(
                'swal:error',
                title: 'Belum ada absensi',
                text: 'Pilih status minimal untuk satu siswa.'
            );

            return;
        }

        $this->dispatch(
            'swal:success',
            title: 'Absensi tersimpan',
            text: $filledAttendance.' data absensi berhasil disimpan.'
        );
    }

    public function deleteAttendance(
        int $studentId
    ): void {
        if (
            ! filled($this->selectedDate)
            || ! filled($this->selectedClassId)
        ) {
            return;
        }

        $this->validateOnly('selectedDate', $this->rules(), $this->messages());
        $this->validateOnly('selectedClassId', $this->rules(), $this->messages());

        abort_unless(
            Student::query()
                ->whereKey($studentId)
                ->where('school_class_id', $this->selectedClassId)
                ->exists(),
            403,
            'Siswa tidak berada di kelas yang dipilih.'
        );

        StudentAttendance::query()
            ->where('student_id', $studentId)
            ->where('school_class_id', $this->selectedClassId)
            ->whereDate(
                'attendance_date',
                $this->selectedDate
            )
            ->delete();

        $key = (string) $studentId;

        $this->statuses[$key] = '';
        $this->arrivalTimes[$key] = '';
        $this->departureTimes[$key] = '';
        $this->lateFlags[$key] = false;
        $this->earlyLeaveFlags[$key] = false;
        $this->notes[$key] = '';
        $this->persisted[$key] = false;

        $this->dispatch(
            'swal:success',
            title: 'Absensi dihapus',
            text: 'Catatan absensi siswa berhasil dihapus.'
        );
    }

    private function loadAttendanceData(): void
    {
        $this->statuses = [];
        $this->arrivalTimes = [];
        $this->departureTimes = [];
        $this->lateFlags = [];
        $this->earlyLeaveFlags = [];
        $this->notes = [];
        $this->persisted = [];

        if (
            ! filled($this->selectedClassId)
            || ! filled($this->selectedDate)
        ) {
            return;
        }

        $students = Student::query()
            ->where(
                'school_class_id',
                $this->selectedClassId
            )
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )
            ->orderBy('name')
            ->get([
                'id',
            ]);

        $attendances = StudentAttendance::query()
            ->whereDate(
                'attendance_date',
                $this->selectedDate
            )
            ->whereIn(
                'student_id',
                $students->pluck('id')
            )
            ->get()
            ->keyBy('student_id');

        foreach ($students as $student) {
            $key = (string) $student->id;

            $attendance = $attendances->get(
                $student->id
            );

            $this->statuses[$key] =
                $attendance?->status ?? '';

            $this->arrivalTimes[$key] =
                $attendance?->arrival_time
                    ? substr(
                        (string) $attendance->arrival_time,
                        0,
                        5
                    )
                    : '';

            $this->departureTimes[$key] =
                $attendance?->departure_time
                    ? substr(
                        (string) $attendance->departure_time,
                        0,
                        5
                    )
                    : '';

            $this->lateFlags[$key] =
                (bool) ($attendance?->is_late ?? false);

            $this->earlyLeaveFlags[$key] =
                (bool) (
                    $attendance?->is_early_leave
                    ?? false
                );

            $this->notes[$key] =
                $attendance?->note ?? '';

            $this->persisted[$key] =
                $attendance !== null;
        }
    }

    private function rules(): array
    {
        return [
            'selectedDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'selectedClassId' => [
                'required',
                Rule::exists(
                    'school_classes',
                    'id'
                ),
            ],

            'statuses' => [
                'array',
            ],

            'statuses.*' => [
                'nullable',
                Rule::in(
                    array_keys(
                        StudentAttendance::statuses()
                    )
                ),
            ],

            'arrivalTimes' => [
                'array',
            ],

            'arrivalTimes.*' => [
                'nullable',
                'date_format:H:i',
            ],

            'departureTimes' => [
                'array',
            ],

            'departureTimes.*' => [
                'nullable',
                'date_format:H:i',
            ],

            'lateFlags' => [
                'array',
            ],

            'lateFlags.*' => [
                'boolean',
            ],

            'earlyLeaveFlags' => [
                'array',
            ],

            'earlyLeaveFlags.*' => [
                'boolean',
            ],

            'notes' => [
                'array',
            ],

            'notes.*' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'selectedDate.required' =>
                'Tanggal absensi wajib dipilih.',

            'selectedDate.date' =>
                'Tanggal absensi tidak valid.',

            'selectedDate.before_or_equal' =>
                'Absensi tidak dapat dibuat untuk tanggal mendatang.',

            'selectedClassId.required' =>
                'Kelas wajib dipilih.',

            'selectedClassId.exists' =>
                'Kelas yang dipilih tidak valid.',

            'statuses.*.in' =>
                'Status absensi tidak valid.',

            'arrivalTimes.*.date_format' =>
                'Format jam masuk tidak valid.',

            'departureTimes.*.date_format' =>
                'Format jam pulang tidak valid.',

            'notes.*.max' =>
                'Catatan maksimal 1.000 karakter.',
        ];
    }

    public function render()
    {
        $schoolClasses = SchoolClass::query()
            ->where('is_active', true)
            ->orderByDesc('academic_year')
            ->orderBy('level')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'level',
                'academic_year',
                'semester',
            ]);

        $students = Student::query()
            ->when(
                filled($this->selectedClassId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->selectedClassId
                )
            )
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )
            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(
                        function (
                            Builder $query
                        ) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'nis',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'nisn',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy('name')
            ->get();

        $attendanceQuery = StudentAttendance::query()
            ->when(
                filled($this->selectedClassId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->selectedClassId
                )
            )
            ->when(
                filled($this->selectedDate),
                fn (Builder $query) => $query->whereDate(
                    'attendance_date',
                    $this->selectedDate
                )
            );

        $attendanceRecords =
            (clone $attendanceQuery)->get();

        $summary = [
            'total' => $attendanceRecords->count(),

            'present' => $attendanceRecords
                ->where(
                    'status',
                    StudentAttendance::STATUS_PRESENT
                )
                ->count(),

            'sick' => $attendanceRecords
                ->where(
                    'status',
                    StudentAttendance::STATUS_SICK
                )
                ->count(),

            'permit' => $attendanceRecords
                ->where(
                    'status',
                    StudentAttendance::STATUS_PERMIT
                )
                ->count(),

            'absent' => $attendanceRecords
                ->where(
                    'status',
                    StudentAttendance::STATUS_ABSENT
                )
                ->count(),

            'late' => $attendanceRecords
                ->where('is_late', true)
                ->count(),
        ];

        $recentSummaries = collect();

        if (filled($this->selectedClassId)) {
            $recentSummaries = StudentAttendance::query()
                ->where(
                    'school_class_id',
                    $this->selectedClassId
                )
                ->select('attendance_date')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw(
                    "SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present"
                )
                ->selectRaw(
                    "SUM(CASE WHEN status = 'sick' THEN 1 ELSE 0 END) as sick"
                )
                ->selectRaw(
                    "SUM(CASE WHEN status = 'permit' THEN 1 ELSE 0 END) as permit"
                )
                ->selectRaw(
                    "SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent"
                )
                ->selectRaw(
                    'SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END) as late'
                )
                ->groupBy('attendance_date')
                ->orderByDesc('attendance_date')
                ->limit(7)
                ->get();
        }

        $selectedClass = filled(
            $this->selectedClassId
        )
            ? SchoolClass::query()->find(
                $this->selectedClassId
            )
            : null;

        return view(
            'livewire.attendances.index',
            [
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'selectedClass' => $selectedClass,
                'summary' => $summary,
                'recentSummaries' => $recentSummaries,
                'statusOptions' =>
                    StudentAttendance::statuses(),
            ]
        );
    }
}
