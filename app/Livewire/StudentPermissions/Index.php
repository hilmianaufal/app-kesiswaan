<?php

namespace App\Livewire\StudentPermissions;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';
    public string $filterClassId = '';
    public string $filterType = '';
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public ?int $editingId = null;

    public string $studentId = '';
    public string $permissionType =
        StudentPermission::TYPE_ABSENCE;

    public string $permissionDate = '';
    public string $startTime = '';
    public string $expectedReturnTime = '';
    public string $reason = '';

    public $evidence = null;

    public ?string $existingEvidence = null;
    public bool $removeExistingEvidence = false;

    public ?int $rejectingId = null;
    public string $rejectionNote = '';

    public function mount(): void
    {
        $this->permissionDate = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClassId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedPermissionType(): void
    {
        if (
            $this->permissionType
            === StudentPermission::TYPE_ABSENCE
        ) {
            $this->startTime = '';
            $this->expectedReturnTime = '';
        }

        if (
            $this->permissionType
            === StudentPermission::TYPE_EARLY_LEAVE
        ) {
            $this->expectedReturnTime = '';
        }

        $this->resetValidation([
            'startTime',
            'expectedReturnTime',
        ]);
    }

    public function updatedEvidence(): void
    {
        $this->validateOnly(
            'evidence',
            [
                'evidence' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'evidence.mimes' =>
                    'Bukti harus berupa PDF, JPG, PNG, atau WEBP.',

                'evidence.max' =>
                    'Ukuran bukti maksimal 5 MB.',
            ]
        );
    }

    public function openCreateModal(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan dan persetujuan.'
        );

        $this->resetForm();

        $this->dispatch(
            'open-modal',
            modal: 'student-permission-form'
        );
    }

    public function edit(int $id): void
    {
        $permission = StudentPermission::query()
            ->findOrFail($id);

        $this->ensureCanModify($permission);
        $this->resetValidation();

        $this->editingId = $permission->id;
        $this->studentId = (string) $permission->student_id;
        $this->permissionType = $permission->permission_type;

        $this->permissionDate = $permission
            ->permission_date
            ->format('Y-m-d');

        $this->startTime = $permission->start_time
            ? substr((string) $permission->start_time, 0, 5)
            : '';

        $this->expectedReturnTime =
            $permission->expected_return_time
                ? substr(
                    (string) $permission->expected_return_time,
                    0,
                    5
                )
                : '';

        $this->reason = $permission->reason;
        $this->evidence = null;
        $this->existingEvidence = $permission->evidence_path;
        $this->removeExistingEvidence = false;

        $this->dispatch(
            'open-modal',
            modal: 'student-permission-form'
        );
    }

    public function cancelNewEvidence(): void
    {
        $this->evidence = null;
    }

    public function removeCurrentEvidence(): void
    {
        $this->evidence = null;
        $this->existingEvidence = null;
        $this->removeExistingEvidence = true;
    }

    public function save(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan dan persetujuan.'
        );

        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $student = Student::query()
            ->findOrFail($validated['studentId']);

        $permission = $this->editingId
            ? StudentPermission::query()
                ->findOrFail($this->editingId)
            : null;

        if ($permission) {
            $this->ensureCanModify($permission);
        }

        $oldEvidence = $permission?->evidence_path;
        $newEvidence = null;

        if ($this->evidence) {
            $newEvidence = $this->evidence->store(
                'student-permissions',
                'public'
            );
        }

        $evidencePath = match (true) {
            filled($newEvidence) => $newEvidence,
            $this->removeExistingEvidence => null,
            default => $oldEvidence,
        };

        try {
            DB::transaction(function () use (
                $validated,
                $student,
                $permission,
                $evidencePath
            ): void {
                $data = [
                    'student_id' => $student->id,
                    'school_class_id' => $student->school_class_id,
                    'permission_type' =>
                        $validated['permissionType'],

                    'permission_date' =>
                        $validated['permissionDate'],

                    'start_time' => filled($validated['startTime'])
                        ? $validated['startTime']
                        : null,

                    'expected_return_time' => filled(
                        $validated['expectedReturnTime']
                    )
                        ? $validated['expectedReturnTime']
                        : null,

                    'actual_return_time' => null,
                    'reason' => trim($validated['reason']),
                    'evidence_path' => $evidencePath,
                    'status' => StudentPermission::STATUS_PENDING,
                    'rejection_note' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ];

                if ($permission) {
                    $permission->update($data);

                    return;
                }

                $data['recorded_by'] = auth()->id();

                StudentPermission::query()->create($data);
            });
        } catch (Throwable $exception) {
            if ($newEvidence) {
                Storage::disk('public')->delete($newEvidence);
            }

            throw $exception;
        }

        if (
            $oldEvidence
            && $oldEvidence !== $evidencePath
        ) {
            Storage::disk('public')->delete($oldEvidence);
        }

        $message = $this->editingId
            ? 'Perizinan berhasil diperbarui dan menunggu persetujuan ulang.'
            : 'Pengajuan izin berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'student-permission-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetForm();
        $this->resetPage();
    }

    public function approvePermission(int $id): void
    {
        $this->ensureCanApprove();

        $permission = StudentPermission::query()
            ->findOrFail($id);

        if (
            $permission->status
            !== StudentPermission::STATUS_PENDING
        ) {
            $this->dispatch(
                'swal:error',
                title: 'Tidak dapat disetujui',
                text: 'Perizinan ini sudah diproses sebelumnya.'
            );

            return;
        }

        $permission->update([
            'status' => StudentPermission::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        $this->dispatch(
            'swal:success',
            title: 'Izin disetujui',
            text: 'Pengajuan izin siswa berhasil disetujui.'
        );
    }

    public function openRejectModal(int $id): void
    {
        $this->ensureCanApprove();

        $permission = StudentPermission::query()
            ->findOrFail($id);

        if (
            $permission->status
            !== StudentPermission::STATUS_PENDING
        ) {
            $this->dispatch(
                'swal:error',
                title: 'Tidak dapat ditolak',
                text: 'Perizinan ini sudah diproses sebelumnya.'
            );

            return;
        }

        $this->rejectingId = $permission->id;
        $this->rejectionNote = '';
        $this->resetValidation('rejectionNote');

        $this->dispatch(
            'open-modal',
            modal: 'reject-permission-form'
        );
    }

    public function submitRejection(): void
    {
        $this->ensureCanApprove();

        $validated = $this->validate(
            [
                'rejectingId' => [
                    'required',
                    Rule::exists('student_permissions', 'id'),
                ],
                'rejectionNote' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'rejectionNote.required' =>
                    'Alasan penolakan wajib diisi.',

                'rejectionNote.max' =>
                    'Alasan penolakan maksimal 2.000 karakter.',
            ]
        );

        $permission = StudentPermission::query()
            ->findOrFail($validated['rejectingId']);

        if (
            $permission->status
            !== StudentPermission::STATUS_PENDING
        ) {
            $this->dispatch(
                'close-modal',
                modal: 'reject-permission-form'
            );

            $this->dispatch(
                'swal:error',
                title: 'Tidak dapat ditolak',
                text: 'Perizinan ini sudah diproses sebelumnya.'
            );

            return;
        }

        $permission->update([
            'status' => StudentPermission::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_note' => trim(
                $validated['rejectionNote']
            ),
        ]);

        $this->dispatch(
            'close-modal',
            modal: 'reject-permission-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Izin ditolak',
            text: 'Pengajuan izin siswa berhasil ditolak.'
        );

        $this->rejectingId = null;
        $this->rejectionNote = '';
    }

    public function markReturned(int $id): void
    {
        $permission = StudentPermission::query()
            ->findOrFail($id);

        if (
            $permission->permission_type
            !== StudentPermission::TYPE_OUT
            || $permission->status
            !== StudentPermission::STATUS_APPROVED
        ) {
            $this->dispatch(
                'swal:error',
                title: 'Tidak dapat diproses',
                text: 'Hanya izin keluar yang sudah disetujui yang dapat ditandai kembali.'
            );

            return;
        }

        $permission->update([
            'actual_return_time' => now()->format('H:i:s'),
            'status' => StudentPermission::STATUS_COMPLETED,
        ]);

        $this->dispatch(
            'swal:success',
            title: 'Siswa sudah kembali',
            text: 'Waktu kembali siswa berhasil dicatat.'
        );
    }

    public function deletePermission(int $id): void
    {
        $permission = StudentPermission::query()
            ->findOrFail($id);

        $this->ensureCanModify($permission);

        $evidencePath = $permission->evidence_path;

        $permission->delete();

        if ($evidencePath) {
            Storage::disk('public')->delete($evidencePath);
        }

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Perizinan dihapus',
            text: 'Data perizinan siswa berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterType',
            'filterStatus',
            'filterDateFrom',
            'filterDateTo',
        ]);

        $this->resetPage();
    }

    private function ensureCanApprove(): void
    {
        abort_unless(
            auth()->user()->isAdmin()
                || auth()->user()->isKepalaSekolah()
                || auth()->user()->isGuruBk(),
            403,
            'Anda tidak memiliki izin menyetujui perizinan.'
        );
    }

    private function ensureCanModify(
        StudentPermission $permission
    ): void {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || $user->isGuruBk()
            || (
                $permission->recorded_by === $user->id
                && $permission->status
                    === StudentPermission::STATUS_PENDING
            );

        abort_unless(
            $allowed,
            403,
            'Anda tidak memiliki izin mengubah perizinan ini.'
        );
    }

    private function rules(): array
    {
        return [
            'studentId' => [
                'required',
                Rule::exists('students', 'id'),
            ],

            'permissionType' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentPermission::types()
                    )
                ),
            ],

            'permissionDate' => [
                'required',
                'date',
            ],

            'startTime' => [
                Rule::requiredIf(
                    fn (): bool => in_array(
                        $this->permissionType,
                        [
                            StudentPermission::TYPE_OUT,
                            StudentPermission::TYPE_EARLY_LEAVE,
                        ],
                        true
                    )
                ),
                'nullable',
                'date_format:H:i',
            ],

            'expectedReturnTime' => [
                Rule::requiredIf(
                    fn (): bool => $this->permissionType
                        === StudentPermission::TYPE_OUT
                ),
                'nullable',
                'date_format:H:i',
                'after:startTime',
            ],

            'reason' => [
                'required',
                'string',
                'max:5000',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'studentId.required' =>
                'Siswa wajib dipilih.',

            'studentId.exists' =>
                'Siswa yang dipilih tidak valid.',

            'permissionType.required' =>
                'Jenis izin wajib dipilih.',

            'permissionType.in' =>
                'Jenis izin tidak valid.',

            'permissionDate.required' =>
                'Tanggal izin wajib diisi.',

            'permissionDate.date' =>
                'Tanggal izin tidak valid.',

            'startTime.required' =>
                'Jam keluar atau jam pulang wajib diisi.',

            'startTime.date_format' =>
                'Format jam keluar tidak valid.',

            'expectedReturnTime.required' =>
                'Perkiraan jam kembali wajib diisi.',

            'expectedReturnTime.date_format' =>
                'Format perkiraan jam kembali tidak valid.',

            'expectedReturnTime.after' =>
                'Jam kembali harus setelah jam keluar.',

            'reason.required' =>
                'Alasan izin wajib diisi.',

            'reason.max' =>
                'Alasan izin maksimal 5.000 karakter.',

            'evidence.mimes' =>
                'Bukti harus berupa PDF, JPG, PNG, atau WEBP.',

            'evidence.max' =>
                'Ukuran bukti maksimal 5 MB.',
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->studentId = '';

        $this->permissionType =
            StudentPermission::TYPE_ABSENCE;

        $this->permissionDate = now()->format('Y-m-d');
        $this->startTime = '';
        $this->expectedReturnTime = '';
        $this->reason = '';
        $this->evidence = null;
        $this->existingEvidence = null;
        $this->removeExistingEvidence = false;

        $this->resetValidation();
    }

    public function render()
    {
        $permissions = StudentPermission::query()
            ->with([
                'student.schoolClass',
                'recorder',
                'approver',
            ])

            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (
                        Builder $query
                    ) use ($search): void {
                        $query
                            ->where(
                                'reason',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'rejection_note',
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
                filled($this->filterType),
                fn (Builder $query) => $query->where(
                    'permission_type',
                    $this->filterType
                )
            )

            ->when(
                filled($this->filterStatus),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->filterStatus
                )
            )

            ->when(
                filled($this->filterDateFrom),
                fn (Builder $query) => $query->whereDate(
                    'permission_date',
                    '>=',
                    $this->filterDateFrom
                )
            )

            ->when(
                filled($this->filterDateTo),
                fn (Builder $query) => $query->whereDate(
                    'permission_date',
                    '<=',
                    $this->filterDateTo
                )
            )

            ->orderByDesc('permission_date')
            ->orderByDesc('id')
            ->paginate(10);

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

        $statistics = [
            'total' => StudentPermission::query()->count(),

            'pending' => StudentPermission::query()
                ->where(
                    'status',
                    StudentPermission::STATUS_PENDING
                )
                ->count(),

            'approved' => StudentPermission::query()
                ->where(
                    'status',
                    StudentPermission::STATUS_APPROVED
                )
                ->count(),

            'rejected' => StudentPermission::query()
                ->where(
                    'status',
                    StudentPermission::STATUS_REJECTED
                )
                ->count(),

            'outside' => StudentPermission::query()
                ->where(
                    'permission_type',
                    StudentPermission::TYPE_OUT
                )
                ->where(
                    'status',
                    StudentPermission::STATUS_APPROVED
                )
                ->whereNull('actual_return_time')
                ->count(),
        ];

        return view(
            'livewire.student-permissions.index',
            [
                'permissions' => $permissions,
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'statistics' => $statistics,

                'permissionTypes' =>
                    StudentPermission::types(),

                'permissionStatuses' =>
                    StudentPermission::statuses(),
            ]
        );
    }
}
