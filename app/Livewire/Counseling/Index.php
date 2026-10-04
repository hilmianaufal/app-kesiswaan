<?php

namespace App\Livewire\Counseling;

use App\Models\CounselingCase;
use App\Models\CounselingSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    public string $search = '';
    public string $filterClassId = '';
    public string $filterCategory = '';
    public string $filterPriority = '';
    public string $filterStatus = '';

    /*
    |--------------------------------------------------------------------------
    | Form kasus konseling
    |--------------------------------------------------------------------------
    */

    public ?int $caseEditingId = null;

    public string $studentId = '';
    public string $caseTitle = '';
    public string $problemCategory =
        CounselingCase::CATEGORY_PERSONAL;

    public string $priority =
        CounselingCase::PRIORITY_MEDIUM;

    public string $confidentialityLevel =
        CounselingCase::CONFIDENTIALITY_CONFIDENTIAL;

    public string $caseSummary = '';

    public string $caseStatus =
        CounselingCase::STATUS_OPEN;

    public string $openedAt = '';
    public string $assignedTo = '';

    /*
    |--------------------------------------------------------------------------
    | Form pertemuan konseling
    |--------------------------------------------------------------------------
    */

    public ?int $sessionEditingId = null;
    public string $sessionCaseId = '';
    public string $sessionDate = '';
    public string $startTime = '';
    public string $endTime = '';
    public string $sessionLocation = 'Ruang BK';

    public string $sessionMethod =
        CounselingSession::METHOD_INDIVIDUAL;

    public string $attendanceStatus =
        CounselingSession::ATTENDANCE_SCHEDULED;

    public string $agenda = '';
    public string $sessionResult = '';
    public string $followUp = '';
    public string $nextSessionDate = '';
    public bool $isParentContacted = false;
    public string $parentResponse = '';

    /*
    |--------------------------------------------------------------------------
    | Riwayat kasus
    |--------------------------------------------------------------------------
    */

    public ?int $selectedCaseId = null;

    public function mount(): void
    {
        $this->openedAt = now()->format('Y-m-d');
        $this->sessionDate = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClassId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPriority(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD kasus
    |--------------------------------------------------------------------------
    */

    public function openCreateCaseModal(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan.'
        );

        $this->resetCaseForm();

        $this->dispatch(
            'open-modal',
            modal: 'counseling-case-form'
        );
    }

    public function editCase(int $id): void
    {
        $case = $this->visibleCasesQuery()
            ->findOrFail($id);

        $this->ensureCanModifyCase($case);
        $this->resetValidation();

        $this->caseEditingId = $case->id;
        $this->studentId = (string) $case->student_id;
        $this->caseTitle = $case->title;
        $this->problemCategory = $case->problem_category;
        $this->priority = $case->priority;
        $this->confidentialityLevel =
            $case->confidentiality_level;

        $this->caseSummary = $case->summary ?? '';
        $this->caseStatus = $case->status;
        $this->openedAt = $case->opened_at->format('Y-m-d');

        $this->assignedTo = $case->assigned_to
            ? (string) $case->assigned_to
            : '';

        $this->dispatch(
            'open-modal',
            modal: 'counseling-case-form'
        );
    }

    public function saveCase(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan.'
        );

        $validated = $this->validate(
            $this->caseRules(),
            $this->caseMessages()
        );

        $student = Student::query()
            ->findOrFail($validated['studentId']);

        $case = $this->caseEditingId
            ? CounselingCase::query()
                ->findOrFail($this->caseEditingId)
            : null;

        if ($case) {
            $this->ensureCanModifyCase($case);
        }

        DB::transaction(function () use (
            $validated,
            $student,
            $case
        ): void {
            $isClosed = in_array(
                $validated['caseStatus'],
                [
                    CounselingCase::STATUS_RESOLVED,
                    CounselingCase::STATUS_CLOSED,
                ],
                true
            );

            $data = [
                'student_id' => $student->id,
                'school_class_id' => $student->school_class_id,
                'title' => trim($validated['caseTitle']),
                'problem_category' =>
                    $validated['problemCategory'],

                'priority' => $validated['priority'],

                'confidentiality_level' =>
                    $validated['confidentialityLevel'],

                'summary' => filled($validated['caseSummary'])
                    ? trim($validated['caseSummary'])
                    : null,

                'status' => $validated['caseStatus'],
                'opened_at' => $validated['openedAt'],

                'closed_at' => $isClosed
                    ? now()->format('Y-m-d')
                    : null,

                'assigned_to' => filled(
                    $validated['assignedTo']
                )
                    ? (int) $validated['assignedTo']
                    : null,
            ];

            if ($case) {
                $case->update($data);

                return;
            }

            $data['case_number'] =
                CounselingCase::generateCaseNumber();

            $data['created_by'] = auth()->id();

            CounselingCase::query()->create($data);
        });

        $message = $this->caseEditingId
            ? 'Kasus konseling berhasil diperbarui.'
            : 'Kasus konseling berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'counseling-case-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetCaseForm();
        $this->resetPage();
    }

    public function deleteCase(int $id): void
    {
        $case = $this->visibleCasesQuery()
            ->findOrFail($id);

        $this->ensureCanModifyCase($case);

        $case->delete();

        if ($this->caseEditingId === $id) {
            $this->resetCaseForm();
        }

        if ($this->selectedCaseId === $id) {
            $this->selectedCaseId = null;
        }

        $this->dispatch(
            'swal:success',
            title: 'Kasus dihapus',
            text: 'Kasus beserta riwayat pertemuannya berhasil dihapus.'
        );

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Riwayat kasus
    |--------------------------------------------------------------------------
    */

    public function showHistory(int $id): void
    {
        $case = $this->visibleCasesQuery()
            ->findOrFail($id);

        $this->selectedCaseId = $case->id;

        $this->dispatch(
            'open-modal',
            modal: 'counseling-history'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD pertemuan
    |--------------------------------------------------------------------------
    */

    public function openSessionModal(int $caseId): void
    {
        $case = $this->visibleCasesQuery()
            ->findOrFail($caseId);

        $this->ensureCanModifyCase($case);
        $this->resetSessionForm();

        $this->sessionCaseId = (string) $case->id;

        $this->dispatch(
            'open-modal',
            modal: 'counseling-session-form'
        );
    }

    public function editSession(int $id): void
    {
        $session = CounselingSession::query()
            ->with('counselingCase')
            ->findOrFail($id);

        $case = $this->visibleCasesQuery()
            ->findOrFail($session->counseling_case_id);

        $this->ensureCanModifyCase($case);
        $this->resetValidation();

        $this->sessionEditingId = $session->id;

        $this->sessionCaseId =
            (string) $session->counseling_case_id;

        $this->sessionDate =
            $session->session_date->format('Y-m-d');

        $this->startTime = $session->start_time
            ? substr((string) $session->start_time, 0, 5)
            : '';

        $this->endTime = $session->end_time
            ? substr((string) $session->end_time, 0, 5)
            : '';

        $this->sessionLocation = $session->location ?? '';
        $this->sessionMethod = $session->method;
        $this->attendanceStatus =
            $session->attendance_status;

        $this->agenda = $session->agenda;
        $this->sessionResult = $session->result ?? '';
        $this->followUp = $session->follow_up ?? '';

        $this->nextSessionDate = $session->next_session_date
            ? $session->next_session_date->format('Y-m-d')
            : '';

        $this->isParentContacted =
            (bool) $session->is_parent_contacted;

        $this->parentResponse =
            $session->parent_response ?? '';

        $this->dispatch(
            'open-modal',
            modal: 'counseling-session-form'
        );
    }

    public function saveSession(): void
    {
        $validated = $this->validate(
            $this->sessionRules(),
            $this->sessionMessages()
        );

        $case = $this->visibleCasesQuery()
            ->findOrFail($validated['sessionCaseId']);

        $this->ensureCanModifyCase($case);

        $session = $this->sessionEditingId
            ? CounselingSession::query()
                ->findOrFail($this->sessionEditingId)
            : null;

        DB::transaction(function () use (
            $validated,
            $case,
            $session
        ): void {
            $data = [
                'counseling_case_id' => $case->id,
                'session_date' => $validated['sessionDate'],

                'start_time' => filled($validated['startTime'])
                    ? $validated['startTime']
                    : null,

                'end_time' => filled($validated['endTime'])
                    ? $validated['endTime']
                    : null,

                'location' => filled(
                    $validated['sessionLocation']
                )
                    ? trim($validated['sessionLocation'])
                    : null,

                'method' => $validated['sessionMethod'],

                'attendance_status' =>
                    $validated['attendanceStatus'],

                'agenda' => trim($validated['agenda']),

                'result' => filled($validated['sessionResult'])
                    ? trim($validated['sessionResult'])
                    : null,

                'follow_up' => filled($validated['followUp'])
                    ? trim($validated['followUp'])
                    : null,

                'next_session_date' => filled(
                    $validated['nextSessionDate']
                )
                    ? $validated['nextSessionDate']
                    : null,

                'is_parent_contacted' =>
                    (bool) $validated['isParentContacted'],

                'parent_response' => filled(
                    $validated['parentResponse']
                )
                    ? trim($validated['parentResponse'])
                    : null,
            ];

            if ($session) {
                $session->update($data);

                return;
            }

            $data['recorded_by'] = auth()->id();

            CounselingSession::query()->create($data);

            if (
                $case->status === CounselingCase::STATUS_OPEN
            ) {
                $case->update([
                    'status' =>
                        CounselingCase::STATUS_IN_PROGRESS,
                ]);
            }
        });

        $message = $this->sessionEditingId
            ? 'Pertemuan konseling berhasil diperbarui.'
            : 'Pertemuan konseling berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'counseling-session-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetSessionForm();
    }

    public function deleteSession(int $id): void
    {
        $session = CounselingSession::query()
            ->findOrFail($id);

        $case = $this->visibleCasesQuery()
            ->findOrFail($session->counseling_case_id);

        $this->ensureCanModifyCase($case);

        $session->delete();

        if ($this->sessionEditingId === $id) {
            $this->resetSessionForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Pertemuan dihapus',
            text: 'Riwayat pertemuan konseling berhasil dihapus.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset filter
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterCategory',
            'filterPriority',
            'filterStatus',
        ]);

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Hak akses
    |--------------------------------------------------------------------------
    */

    private function visibleCasesQuery(): Builder
    {
        $query = CounselingCase::query();
        $user = auth()->user();

        if ($user->isAdmin() || $user->isGuruBk()) {
            return $query;
        }

        if ($user->isKepalaSekolah()) {
            return $query->where(
                function (Builder $query) use ($user): void {
                    $query
                        ->where(
                            'confidentiality_level',
                            '!=',
                            CounselingCase::CONFIDENTIALITY_HIGHLY_CONFIDENTIAL
                        )
                        ->orWhere('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                }
            );
        }

        return $query->where(
            function (Builder $query) use ($user): void {
                $query
                    ->where(
                        'confidentiality_level',
                        CounselingCase::CONFIDENTIALITY_NORMAL
                    )
                    ->orWhere('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            }
        );
    }

    private function ensureCanModifyCase(
        CounselingCase $case
    ): void {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || $user->isGuruBk()
            || $case->assigned_to === $user->id
            || $case->created_by === $user->id;

        abort_unless(
            $allowed,
            403,
            'Anda tidak memiliki izin mengubah kasus ini.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi kasus
    |--------------------------------------------------------------------------
    */

    private function caseRules(): array
    {
        return [
            'studentId' => [
                'required',
                Rule::exists('students', 'id'),
            ],

            'caseTitle' => [
                'required',
                'string',
                'max:200',
            ],

            'problemCategory' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingCase::categories()
                    )
                ),
            ],

            'priority' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingCase::priorities()
                    )
                ),
            ],

            'confidentialityLevel' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingCase::confidentialityLevels()
                    )
                ),
            ],

            'caseSummary' => [
                'required',
                'string',
                'max:5000',
            ],

            'caseStatus' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingCase::statuses()
                    )
                ),
            ],

            'openedAt' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'assignedTo' => [
                'nullable',

                Rule::exists('users', 'id')
                    ->where(function ($query) {
                        return $query
                            ->where('is_active', true)
                            ->whereIn('role', [
                                User::ROLE_ADMIN,
                                User::ROLE_GURU_BK,
                            ]);
                    }),
            ],
        ];
    }

    private function caseMessages(): array
    {
        return [
            'studentId.required' =>
                'Siswa wajib dipilih.',

            'studentId.exists' =>
                'Siswa yang dipilih tidak valid.',

            'caseTitle.required' =>
                'Judul kasus wajib diisi.',

            'caseTitle.max' =>
                'Judul kasus maksimal 200 karakter.',

            'problemCategory.required' =>
                'Kategori masalah wajib dipilih.',

            'priority.required' =>
                'Prioritas kasus wajib dipilih.',

            'confidentialityLevel.required' =>
                'Tingkat kerahasiaan wajib dipilih.',

            'caseSummary.required' =>
                'Ringkasan masalah wajib diisi.',

            'caseSummary.max' =>
                'Ringkasan maksimal 5.000 karakter.',

            'caseStatus.required' =>
                'Status kasus wajib dipilih.',

            'openedAt.required' =>
                'Tanggal pembukaan kasus wajib diisi.',

            'openedAt.before_or_equal' =>
                'Tanggal pembukaan tidak boleh melebihi hari ini.',

            'assignedTo.exists' =>
                'Guru BK yang dipilih tidak valid.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi pertemuan
    |--------------------------------------------------------------------------
    */

    private function sessionRules(): array
    {
        return [
            'sessionCaseId' => [
                'required',
                Rule::exists('counseling_cases', 'id'),
            ],

            'sessionDate' => [
                'required',
                'date',
            ],

            'startTime' => [
                'nullable',
                'date_format:H:i',
            ],

            'endTime' => [
                'nullable',
                'date_format:H:i',
                'after:startTime',
            ],

            'sessionLocation' => [
                'nullable',
                'string',
                'max:150',
            ],

            'sessionMethod' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingSession::methods()
                    )
                ),
            ],

            'attendanceStatus' => [
                'required',
                Rule::in(
                    array_keys(
                        CounselingSession::attendanceStatuses()
                    )
                ),
            ],

            'agenda' => [
                'required',
                'string',
                'max:5000',
            ],

            'sessionResult' => [
                Rule::requiredIf(
                    fn (): bool =>
                        $this->attendanceStatus
                        === CounselingSession::ATTENDANCE_COMPLETED
                ),
                'nullable',
                'string',
                'max:5000',
            ],

            'followUp' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'nextSessionDate' => [
                'nullable',
                'date',
                'after_or_equal:sessionDate',
            ],

            'isParentContacted' => [
                'boolean',
            ],

            'parentResponse' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }

    private function sessionMessages(): array
    {
        return [
            'sessionCaseId.required' =>
                'Kasus konseling wajib dipilih.',

            'sessionDate.required' =>
                'Tanggal pertemuan wajib diisi.',

            'startTime.date_format' =>
                'Format jam mulai tidak valid.',

            'endTime.date_format' =>
                'Format jam selesai tidak valid.',

            'endTime.after' =>
                'Jam selesai harus setelah jam mulai.',

            'sessionLocation.max' =>
                'Lokasi maksimal 150 karakter.',

            'sessionMethod.required' =>
                'Metode konseling wajib dipilih.',

            'attendanceStatus.required' =>
                'Status pertemuan wajib dipilih.',

            'agenda.required' =>
                'Agenda pertemuan wajib diisi.',

            'agenda.max' =>
                'Agenda maksimal 5.000 karakter.',

            'sessionResult.required' =>
                'Hasil konseling wajib diisi untuk pertemuan yang sudah selesai.',

            'sessionResult.max' =>
                'Hasil konseling maksimal 5.000 karakter.',

            'followUp.max' =>
                'Tindak lanjut maksimal 5.000 karakter.',

            'nextSessionDate.after_or_equal' =>
                'Pertemuan berikutnya tidak boleh sebelum pertemuan saat ini.',

            'parentResponse.max' =>
                'Tanggapan orang tua maksimal 3.000 karakter.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Reset form
    |--------------------------------------------------------------------------
    */

    private function resetCaseForm(): void
    {
        $this->caseEditingId = null;
        $this->studentId = '';
        $this->caseTitle = '';

        $this->problemCategory =
            CounselingCase::CATEGORY_PERSONAL;

        $this->priority =
            CounselingCase::PRIORITY_MEDIUM;

        $this->confidentialityLevel =
            CounselingCase::CONFIDENTIALITY_CONFIDENTIAL;

        $this->caseSummary = '';

        $this->caseStatus =
            CounselingCase::STATUS_OPEN;

        $this->openedAt = now()->format('Y-m-d');
        $this->assignedTo = '';

        $this->resetValidation();
    }

    private function resetSessionForm(): void
    {
        $this->sessionEditingId = null;
        $this->sessionCaseId = '';
        $this->sessionDate = now()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->sessionLocation = 'Ruang BK';

        $this->sessionMethod =
            CounselingSession::METHOD_INDIVIDUAL;

        $this->attendanceStatus =
            CounselingSession::ATTENDANCE_SCHEDULED;

        $this->agenda = '';
        $this->sessionResult = '';
        $this->followUp = '';
        $this->nextSessionDate = '';
        $this->isParentContacted = false;
        $this->parentResponse = '';

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $cases = $this->visibleCasesQuery()
            ->with([
                'student.schoolClass',
                'assignedTeacher',
                'creator',
            ])
            ->withCount('sessions')

            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (
                        Builder $query
                    ) use ($search): void {
                        $query
                            ->where(
                                'case_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'title',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'summary',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'student',
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
                    });
                }
            )

            ->when(
                filled($this->filterClassId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->filterClassId
                )
            )

            ->when(
                filled($this->filterCategory),
                fn (Builder $query) => $query->where(
                    'problem_category',
                    $this->filterCategory
                )
            )

            ->when(
                filled($this->filterPriority),
                fn (Builder $query) => $query->where(
                    'priority',
                    $this->filterPriority
                )
            )

            ->when(
                filled($this->filterStatus),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->filterStatus
                )
            )

            ->orderByRaw(
                "CASE priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END"
            )
            ->orderByDesc('opened_at')
            ->paginate(8);

        $students = Student::query()
            ->with('schoolClass')
            ->where('status', Student::STATUS_ACTIVE)
            ->orderBy('name')
            ->get([
                'id',
                'school_class_id',
                'name',
                'nis',
            ]);

        $schoolClasses = SchoolClass::query()
            ->where('is_active', true)
            ->orderByDesc('academic_year')
            ->orderBy('level')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'academic_year',
            ]);

        $counselors = User::query()
            ->where('is_active', true)
            ->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_GURU_BK,
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'role',
            ]);

        $visibleCaseIds = $this->visibleCasesQuery()
            ->select('id');

        $statistics = [
            'total' => $this->visibleCasesQuery()->count(),

            'active' => $this->visibleCasesQuery()
                ->whereIn('status', [
                    CounselingCase::STATUS_OPEN,
                    CounselingCase::STATUS_IN_PROGRESS,
                    CounselingCase::STATUS_MONITORING,
                ])
                ->count(),

            'urgent' => $this->visibleCasesQuery()
                ->where(
                    'priority',
                    CounselingCase::PRIORITY_URGENT
                )
                ->whereNotIn('status', [
                    CounselingCase::STATUS_RESOLVED,
                    CounselingCase::STATUS_CLOSED,
                ])
                ->count(),

            'upcoming' => CounselingSession::query()
                ->whereIn(
                    'counseling_case_id',
                    $visibleCaseIds
                )
                ->whereDate(
                    'session_date',
                    '>=',
                    now()->format('Y-m-d')
                )
                ->where(
                    'attendance_status',
                    CounselingSession::ATTENDANCE_SCHEDULED
                )
                ->count(),
        ];

        $selectedCase = null;

        if ($this->selectedCaseId) {
            $selectedCase = $this->visibleCasesQuery()
                ->with([
                    'student.schoolClass',
                    'assignedTeacher',
                    'creator',

                    'sessions' => function ($query): void {
                        $query
                            ->with('recorder')
                            ->orderByDesc('session_date')
                            ->orderByDesc('start_time');
                    },
                ])
                ->find($this->selectedCaseId);
        }

        return view(
            'livewire.counseling.index',
            [
                'cases' => $cases,
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'counselors' => $counselors,
                'statistics' => $statistics,
                'selectedCase' => $selectedCase,

                'categories' =>
                    CounselingCase::categories(),

                'priorities' =>
                    CounselingCase::priorities(),

                'confidentialityLevels' =>
                    CounselingCase::confidentialityLevels(),

                'caseStatuses' =>
                    CounselingCase::statuses(),

                'sessionMethods' =>
                    CounselingSession::methods(),

                'attendanceStatuses' =>
                    CounselingSession::attendanceStatuses(),
            ]
        );
    }
}
