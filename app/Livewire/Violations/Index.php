<?php

namespace App\Livewire\Violations;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\ViolationType;
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
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    /*
    |--------------------------------------------------------------------------
    | Form pelanggaran siswa
    |--------------------------------------------------------------------------
    */

    public ?int $editingId = null;

    public string $studentId = '';
    public string $violationTypeId = '';
    public string $violationDate = '';
    public string $location = '';
    public string $chronology = '';
    public string $actionTaken = '';
    public string $followUpStatus = StudentViolation::STATUS_PENDING;
    public string $notes = '';

    /*
    |--------------------------------------------------------------------------
    | Form master jenis pelanggaran
    |--------------------------------------------------------------------------
    */

    public ?int $typeEditingId = null;

    public string $typeName = '';
    public string $typeCategory = ViolationType::CATEGORY_LIGHT;
    public int|string $typePoints = 5;
    public string $typeDefaultAction = '';
    public bool $typeIsActive = true;

    public function mount(): void
    {
        $this->violationDate = now()->format('Y-m-d');
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

    public function updatedViolationTypeId(): void
    {
        if (! filled($this->violationTypeId)) {
            return;
        }

        $type = ViolationType::query()->find(
            $this->violationTypeId
        );

        if ($type && blank($this->actionTaken)) {
            $this->actionTaken = $type->default_action ?? '';
        }
    }

    public function openCreateModal(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan.'
        );

        $this->resetViolationForm();

        $this->dispatch(
            'open-modal',
            modal: 'violation-form'
        );
    }

    public function edit(int $id): void
    {
        $violation = StudentViolation::query()
            ->findOrFail($id);

        $this->ensureCanModify($violation);
        $this->resetValidation();

        $this->editingId = $violation->id;
        $this->studentId = (string) $violation->student_id;

        $this->violationTypeId = $violation->violation_type_id
            ? (string) $violation->violation_type_id
            : '';

        $this->violationDate = $violation
            ->violation_date
            ->format('Y-m-d');

        $this->location = $violation->location ?? '';
        $this->chronology = $violation->chronology;
        $this->actionTaken = $violation->action_taken ?? '';
        $this->followUpStatus = $violation->follow_up_status;
        $this->notes = $violation->notes ?? '';

        $this->dispatch(
            'open-modal',
            modal: 'violation-form'
        );
    }

    public function save(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan.'
        );

        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $student = Student::query()
            ->findOrFail($validated['studentId']);

        $type = ViolationType::query()
            ->findOrFail($validated['violationTypeId']);

        $existing = $this->editingId
            ? StudentViolation::query()
                ->findOrFail($this->editingId)
            : null;

        if ($existing) {
            $this->ensureCanModify($existing);
        }

        DB::transaction(function () use (
            $validated,
            $student,
            $type,
            $existing
        ): void {
            $data = [
                'student_id' => $student->id,
                'school_class_id' => $student->school_class_id,
                'violation_type_id' => $type->id,
                'violation_name' => $type->name,
                'category' => $type->category,
                'points' => $type->points,
                'violation_date' => $validated['violationDate'],

                'location' => filled($validated['location'])
                    ? trim($validated['location'])
                    : null,

                'chronology' => trim($validated['chronology']),

                'action_taken' => filled($validated['actionTaken'])
                    ? trim($validated['actionTaken'])
                    : $type->default_action,

                'follow_up_status' =>
                    $validated['followUpStatus'],

                'notes' => filled($validated['notes'])
                    ? trim($validated['notes'])
                    : null,
            ];

            if ($existing) {
                $existing->update($data);

                return;
            }

            $data['recorded_by'] = auth()->id();

            StudentViolation::query()->create($data);
        });

        $message = $this->editingId
            ? 'Catatan pelanggaran berhasil diperbarui.'
            : 'Pelanggaran siswa berhasil dicatat.';

        $this->dispatch(
            'close-modal',
            modal: 'violation-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetViolationForm();
        $this->resetPage();
    }

    public function deleteViolation(int $id): void
    {
        $violation = StudentViolation::query()
            ->findOrFail($id);

        $this->ensureCanModify($violation);

        $violation->delete();

        if ($this->editingId === $id) {
            $this->resetViolationForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Catatan dihapus',
            text: 'Catatan pelanggaran berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function openTypeModal(): void
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403,
            'Hanya admin yang dapat mengelola jenis pelanggaran.'
        );

        $this->resetTypeForm();

        $this->dispatch(
            'open-modal',
            modal: 'violation-type-form'
        );
    }

    public function editType(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $type = ViolationType::query()->findOrFail($id);

        $this->resetValidation();

        $this->typeEditingId = $type->id;
        $this->typeName = $type->name;
        $this->typeCategory = $type->category;
        $this->typePoints = $type->points;
        $this->typeDefaultAction = $type->default_action ?? '';
        $this->typeIsActive = (bool) $type->is_active;

        $this->dispatch(
            'open-modal',
            modal: 'violation-type-form'
        );
    }

    public function saveType(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validate(
            $this->typeRules(),
            $this->typeMessages()
        );

        ViolationType::query()->updateOrCreate(
            [
                'id' => $this->typeEditingId,
            ],
            [
                'name' => trim($validated['typeName']),
                'category' => $validated['typeCategory'],
                'points' => (int) $validated['typePoints'],

                'default_action' => filled(
                    $validated['typeDefaultAction']
                )
                    ? trim($validated['typeDefaultAction'])
                    : null,

                'is_active' => (bool) $validated['typeIsActive'],
            ]
        );

        $message = $this->typeEditingId
            ? 'Jenis pelanggaran berhasil diperbarui.'
            : 'Jenis pelanggaran berhasil ditambahkan.';

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetTypeForm();
    }

    public function toggleTypeStatus(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $type = ViolationType::query()->findOrFail($id);

        $type->update([
            'is_active' => ! $type->is_active,
        ]);

        $this->dispatch(
            'swal:success',
            title: 'Status diperbarui',
            text: 'Status jenis pelanggaran berhasil diubah.'
        );
    }

    public function deleteType(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $type = ViolationType::query()->findOrFail($id);

        if ($type->violations()->exists()) {
            $this->dispatch(
                'swal:error',
                title: 'Tidak dapat dihapus',
                text: 'Jenis pelanggaran sudah digunakan. Nonaktifkan jenis tersebut sebagai gantinya.'
            );

            return;
        }

        $type->delete();

        $this->dispatch(
            'swal:success',
            title: 'Jenis dihapus',
            text: 'Jenis pelanggaran berhasil dihapus.'
        );

        $this->resetTypeForm();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterCategory',
            'filterStatus',
            'filterDateFrom',
            'filterDateTo',
        ]);

        $this->resetPage();
    }

    private function ensureCanModify(
        StudentViolation $violation
    ): void {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || $user->isGuruBk()
            || $violation->recorded_by === $user->id;

        abort_unless(
            $allowed,
            403,
            'Anda tidak memiliki izin mengubah catatan ini.'
        );
    }

    private function rules(): array
    {
        return [
            'studentId' => [
                'required',
                Rule::exists('students', 'id'),
            ],

            'violationTypeId' => [
                'required',
                Rule::exists('violation_types', 'id'),
            ],

            'violationDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'location' => [
                'nullable',
                'string',
                'max:150',
            ],

            'chronology' => [
                'required',
                'string',
                'max:5000',
            ],

            'actionTaken' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'followUpStatus' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentViolation::followUpStatuses()
                    )
                ),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
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

            'violationTypeId.required' =>
                'Jenis pelanggaran wajib dipilih.',

            'violationTypeId.exists' =>
                'Jenis pelanggaran tidak valid.',

            'violationDate.required' =>
                'Tanggal pelanggaran wajib diisi.',

            'violationDate.before_or_equal' =>
                'Tanggal tidak boleh melebihi hari ini.',

            'location.max' =>
                'Lokasi maksimal 150 karakter.',

            'chronology.required' =>
                'Kronologi kejadian wajib diisi.',

            'chronology.max' =>
                'Kronologi maksimal 5.000 karakter.',

            'actionTaken.max' =>
                'Tindakan maksimal 5.000 karakter.',

            'followUpStatus.required' =>
                'Status tindak lanjut wajib dipilih.',

            'notes.max' =>
                'Catatan maksimal 3.000 karakter.',
        ];
    }

    private function typeRules(): array
    {
        return [
            'typeName' => [
                'required',
                'string',
                'max:150',

                Rule::unique('violation_types', 'name')
                    ->ignore($this->typeEditingId),
            ],

            'typeCategory' => [
                'required',
                Rule::in(
                    array_keys(
                        ViolationType::categories()
                    )
                ),
            ],

            'typePoints' => [
                'required',
                'integer',
                'min:0',
                'max:1000',
            ],

            'typeDefaultAction' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'typeIsActive' => [
                'boolean',
            ],
        ];
    }

    private function typeMessages(): array
    {
        return [
            'typeName.required' =>
                'Nama jenis pelanggaran wajib diisi.',

            'typeName.unique' =>
                'Nama jenis pelanggaran sudah digunakan.',

            'typeCategory.required' =>
                'Kategori wajib dipilih.',

            'typePoints.required' =>
                'Jumlah poin wajib diisi.',

            'typePoints.integer' =>
                'Poin harus berupa angka.',

            'typePoints.min' =>
                'Poin tidak boleh kurang dari nol.',

            'typePoints.max' =>
                'Poin maksimal 1.000.',

            'typeDefaultAction.max' =>
                'Tindakan maksimal 5.000 karakter.',
        ];
    }

    private function resetViolationForm(): void
    {
        $this->editingId = null;
        $this->studentId = '';
        $this->violationTypeId = '';
        $this->violationDate = now()->format('Y-m-d');
        $this->location = '';
        $this->chronology = '';
        $this->actionTaken = '';
        $this->followUpStatus =
            StudentViolation::STATUS_PENDING;

        $this->notes = '';

        $this->resetValidation();
    }

    private function resetTypeForm(): void
    {
        $this->typeEditingId = null;
        $this->typeName = '';
        $this->typeCategory =
            ViolationType::CATEGORY_LIGHT;

        $this->typePoints = 5;
        $this->typeDefaultAction = '';
        $this->typeIsActive = true;

        $this->resetValidation();
    }

    public function render()
    {
        $violations = StudentViolation::query()
            ->with([
                'student.schoolClass',
                'violationType',
                'recorder',
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
                                'violation_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'chronology',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'location',
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
                    'category',
                    $this->filterCategory
                )
            )

            ->when(
                filled($this->filterStatus),
                fn (Builder $query) => $query->where(
                    'follow_up_status',
                    $this->filterStatus
                )
            )

            ->when(
                filled($this->filterDateFrom),
                fn (Builder $query) => $query->whereDate(
                    'violation_date',
                    '>=',
                    $this->filterDateFrom
                )
            )

            ->when(
                filled($this->filterDateTo),
                fn (Builder $query) => $query->whereDate(
                    'violation_date',
                    '<=',
                    $this->filterDateTo
                )
            )

            ->orderByDesc('violation_date')
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

        $violationTypes = ViolationType::query()
            ->orderBy('category')
            ->orderBy('points')
            ->orderBy('name')
            ->get();

        $statistics = [
            'cases' => StudentViolation::query()->count(),

            'points' => (int) StudentViolation::query()
                ->sum('points'),

            'pending' => StudentViolation::query()
                ->whereIn('follow_up_status', [
                    StudentViolation::STATUS_PENDING,
                    StudentViolation::STATUS_IN_PROGRESS,
                ])
                ->count(),

            'heavy' => StudentViolation::query()
                ->where(
                    'category',
                    ViolationType::CATEGORY_HEAVY
                )
                ->count(),
        ];

        $topStudents = StudentViolation::query()
            ->select('student_id')
            ->selectRaw('COUNT(*) as total_cases')
            ->selectRaw('SUM(points) as total_points')
            ->with([
                'student:id,name,nis,school_class_id',
                'student.schoolClass:id,name',
            ])
            ->groupBy('student_id')
            ->orderByDesc('total_points')
            ->limit(5)
            ->get();

        return view(
            'livewire.violations.index',
            [
                'violations' => $violations,
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'violationTypes' => $violationTypes,
                'statistics' => $statistics,
                'topStudents' => $topStudents,

                'categories' =>
                    ViolationType::categories(),

                'followUpStatuses' =>
                    StudentViolation::followUpStatuses(),
            ]
        );
    }
}
