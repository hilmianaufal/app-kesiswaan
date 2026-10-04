<?php

namespace App\Livewire\DevelopmentNotes;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentDevelopmentNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterClassId = '';
    public string $filterCategory = '';
    public string $filterType = '';
    public string $filterFollowUpStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public ?int $editingId = null;

    public string $studentId = '';
    public string $noteDate = '';
    public string $category = StudentDevelopmentNote::CATEGORY_ATTITUDE;
    public string $type = StudentDevelopmentNote::TYPE_POSITIVE;
    public string $title = '';
    public string $description = '';
    public string $followUp = '';
    public string $followUpStatus =
        StudentDevelopmentNote::FOLLOW_UP_PENDING;

    public bool $isPrivate = false;

    public function mount(): void
    {
        $this->noteDate = now()->format('Y-m-d');
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

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterFollowUpStatus(): void
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

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->dispatch(
            'open-modal',
            modal: 'development-note-form'
        );
    }

    public function edit(int $id): void
    {
        $note = StudentDevelopmentNote::query()
            ->findOrFail($id);

        $this->ensureCanModify($note);
        $this->resetValidation();

        $this->editingId = $note->id;
        $this->studentId = (string) $note->student_id;
        $this->noteDate = $note->note_date->format('Y-m-d');
        $this->category = $note->category;
        $this->type = $note->type;
        $this->title = $note->title;
        $this->description = $note->description;
        $this->followUp = $note->follow_up ?? '';
        $this->followUpStatus = $note->follow_up_status;
        $this->isPrivate = (bool) $note->is_private;

        $this->dispatch(
            'open-modal',
            modal: 'development-note-form'
        );
    }

    public function save(): void
    {
        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $student = Student::query()
            ->findOrFail($validated['studentId']);

        $existingNote = $this->editingId
            ? StudentDevelopmentNote::query()
                ->findOrFail($this->editingId)
            : null;

        if ($existingNote) {
            $this->ensureCanModify($existingNote);
        }

        DB::transaction(function () use (
            $validated,
            $student,
            $existingNote
        ): void {
            $data = [
                'student_id' => $student->id,
                'school_class_id' => $student->school_class_id,
                'note_date' => $validated['noteDate'],
                'category' => $validated['category'],
                'type' => $validated['type'],
                'title' => trim($validated['title']),
                'description' => trim($validated['description']),

                'follow_up' => filled($validated['followUp'])
                    ? trim($validated['followUp'])
                    : null,

                'follow_up_status' =>
                    $validated['followUpStatus'],

                'is_private' => (bool) $validated['isPrivate'],
            ];

            if ($existingNote) {
                $existingNote->update($data);

                return;
            }

            $data['recorded_by'] = auth()->id();

            StudentDevelopmentNote::query()->create($data);
        });

        $message = $this->editingId
            ? 'Catatan perkembangan berhasil diperbarui.'
            : 'Catatan perkembangan berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'development-note-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $note = StudentDevelopmentNote::query()
            ->findOrFail($id);

        $this->ensureCanModify($note);

        $note->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Catatan dihapus',
            text: 'Catatan perkembangan siswa berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterCategory',
            'filterType',
            'filterFollowUpStatus',
            'filterDateFrom',
            'filterDateTo',
        ]);

        $this->resetPage();
    }

    private function ensureCanModify(
        StudentDevelopmentNote $note
    ): void {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || $user->isGuruBk()
            || $note->recorded_by === $user->id;

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

            'noteDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'category' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentDevelopmentNote::categories()
                    )
                ),
            ],

            'type' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentDevelopmentNote::types()
                    )
                ),
            ],

            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],

            'followUp' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'followUpStatus' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentDevelopmentNote::followUpStatuses()
                    )
                ),
            ],

            'isPrivate' => [
                'boolean',
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

            'noteDate.required' =>
                'Tanggal catatan wajib diisi.',

            'noteDate.before_or_equal' =>
                'Tanggal catatan tidak boleh melebihi hari ini.',

            'category.required' =>
                'Kategori catatan wajib dipilih.',

            'category.in' =>
                'Kategori catatan tidak valid.',

            'type.required' =>
                'Jenis catatan wajib dipilih.',

            'type.in' =>
                'Jenis catatan tidak valid.',

            'title.required' =>
                'Judul catatan wajib diisi.',

            'title.max' =>
                'Judul maksimal 200 karakter.',

            'description.required' =>
                'Isi catatan wajib diisi.',

            'description.max' =>
                'Isi catatan maksimal 5.000 karakter.',

            'followUp.max' =>
                'Tindak lanjut maksimal 5.000 karakter.',

            'followUpStatus.required' =>
                'Status tindak lanjut wajib dipilih.',
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->studentId = '';
        $this->noteDate = now()->format('Y-m-d');
        $this->category =
            StudentDevelopmentNote::CATEGORY_ATTITUDE;

        $this->type =
            StudentDevelopmentNote::TYPE_POSITIVE;

        $this->title = '';
        $this->description = '';
        $this->followUp = '';

        $this->followUpStatus =
            StudentDevelopmentNote::FOLLOW_UP_PENDING;

        $this->isPrivate = false;

        $this->resetValidation();
    }

    private function visibleNotesQuery(): Builder
    {
        $query = StudentDevelopmentNote::query();

        $user = auth()->user();

        if (! $user->hasRole([
            User::ROLE_ADMIN,
            User::ROLE_GURU_BK,
        ])) {
            $query->where(function (Builder $query) use ($user): void {
                $query
                    ->where('is_private', false)
                    ->orWhere('recorded_by', $user->id);
            });
        }

        return $query;
    }

    public function render()
    {
        $notes = $this->visibleNotesQuery()
            ->with([
                'student.schoolClass',
                'schoolClass',
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
                                'title',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'description',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'follow_up',
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
                filled($this->filterType),
                fn (Builder $query) => $query->where(
                    'type',
                    $this->filterType
                )
            )

            ->when(
                filled($this->filterFollowUpStatus),
                fn (Builder $query) => $query->where(
                    'follow_up_status',
                    $this->filterFollowUpStatus
                )
            )

            ->when(
                filled($this->filterDateFrom),
                fn (Builder $query) => $query->whereDate(
                    'note_date',
                    '>=',
                    $this->filterDateFrom
                )
            )

            ->when(
                filled($this->filterDateTo),
                fn (Builder $query) => $query->whereDate(
                    'note_date',
                    '<=',
                    $this->filterDateTo
                )
            )

            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->paginate(10);

        $students = Student::query()
            ->with('schoolClass')
            ->where('status', Student::STATUS_ACTIVE)
            ->orderBy('name')
            ->get([
                'id',
                'school_class_id',
                'nis',
                'name',
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

        $visibleQuery = $this->visibleNotesQuery();

        $statistics = [
            'total' => (clone $visibleQuery)->count(),

            'positive' => (clone $visibleQuery)
                ->where(
                    'type',
                    StudentDevelopmentNote::TYPE_POSITIVE
                )
                ->count(),

            'attention' => (clone $visibleQuery)
                ->where(
                    'type',
                    StudentDevelopmentNote::TYPE_ATTENTION
                )
                ->count(),

            'pending' => (clone $visibleQuery)
                ->whereIn('follow_up_status', [
                    StudentDevelopmentNote::FOLLOW_UP_PENDING,
                    StudentDevelopmentNote::FOLLOW_UP_IN_PROGRESS,
                ])
                ->count(),
        ];

        return view(
            'livewire.development-notes.index',
            [
                'notes' => $notes,
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'statistics' => $statistics,

                'categories' =>
                    StudentDevelopmentNote::categories(),

                'types' =>
                    StudentDevelopmentNote::types(),

                'followUpStatuses' =>
                    StudentDevelopmentNote::followUpStatuses(),
            ]
        );
    }
}
