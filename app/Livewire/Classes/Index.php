<?php

namespace App\Livewire\Classes;

use App\Models\SchoolClass;
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
    | Pencarian dan filter
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $filterAcademicYear = '';

    public string $filterSemester = '';

    public string $filterStatus = '';

    /*
    |--------------------------------------------------------------------------
    | Data form
    |--------------------------------------------------------------------------
    */

    public ?int $editingId = null;

    public string $name = '';

    public string $level = '';

    public string $major = '';

    public string $homeroomTeacherId = '';

    public string $academicYear = '';

    public string $semester = 'ganjil';

    public int|string $capacity = 36;

    public string $description = '';

    public bool $isActive = true;

    public function mount(): void
    {
        $this->academicYear = $this->defaultAcademicYear();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset pagination ketika filter berubah
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAcademicYear(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSemester(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Modal tambah
    |--------------------------------------------------------------------------
    */

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->dispatch(
            'open-modal',
            modal: 'class-form'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Modal edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        $schoolClass = SchoolClass::query()->findOrFail($id);

        $this->resetValidation();

        $this->editingId = $schoolClass->id;
        $this->name = $schoolClass->name;
        $this->level = $schoolClass->level;
        $this->major = $schoolClass->major ?? '';

        $this->homeroomTeacherId = $schoolClass->homeroom_teacher_id
            ? (string) $schoolClass->homeroom_teacher_id
            : '';

        $this->academicYear = $schoolClass->academic_year;
        $this->semester = $schoolClass->semester;
        $this->capacity = $schoolClass->capacity;
        $this->description = $schoolClass->description ?? '';
        $this->isActive = (bool) $schoolClass->is_active;

        $this->dispatch(
            'open-modal',
            modal: 'class-form'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan data
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        DB::transaction(function () use ($validated): void {
            $data = [
                'name' => trim($validated['name']),
                'level' => $validated['level'],

                'major' => filled($validated['major'])
                    ? trim($validated['major'])
                    : null,

                'homeroom_teacher_id' => filled(
                    $validated['homeroomTeacherId']
                )
                    ? (int) $validated['homeroomTeacherId']
                    : null,

                'academic_year' => $validated['academicYear'],
                'semester' => $validated['semester'],
                'capacity' => (int) $validated['capacity'],

                'description' => filled($validated['description'])
                    ? trim($validated['description'])
                    : null,

                'is_active' => (bool) $validated['isActive'],
            ];

            if ($this->editingId) {
                SchoolClass::query()
                    ->findOrFail($this->editingId)
                    ->update($data);

                return;
            }

            SchoolClass::query()->create($data);
        });

        $message = $this->editingId
            ? 'Data kelas berhasil diperbarui.'
            : 'Data kelas berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'class-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetForm();
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Hapus data
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): void
    {
        $schoolClass = SchoolClass::query()->findOrFail($id);

        $schoolClass->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Data dihapus',
            text: 'Data kelas berhasil dihapus.'
        );

        $this->resetPage();
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
            'filterAcademicYear',
            'filterSemester',
            'filterStatus',
        ]);

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Aturan validasi
    |--------------------------------------------------------------------------
    */

    private function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('school_classes', 'name')
                    ->where(function ($query) {
                        return $query
                            ->where(
                                'academic_year',
                                $this->academicYear
                            )
                            ->where(
                                'semester',
                                $this->semester
                            );
                    })
                    ->ignore($this->editingId),
            ],

            'level' => [
                'required',
                Rule::in([
                    '7',
                    '8',
                    '9',
                    '10',
                    '11',
                    '12',
                ]),
            ],

            'major' => [
                'nullable',
                'string',
                'max:100',
            ],

            'homeroomTeacherId' => [
                'nullable',

                Rule::exists('users', 'id')
                    ->where(function ($query) {
                        return $query
                            ->where('is_active', true)
                            ->whereIn('role', [
                                User::ROLE_WALI_KELAS,
                                User::ROLE_GURU,
                                User::ROLE_ADMIN,
                            ]);
                    }),
            ],

            'academicYear' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
            ],

            'semester' => [
                'required',
                Rule::in([
                    'ganjil',
                    'genap',
                ]),
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'isActive' => [
                'boolean',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.max' => 'Nama kelas maksimal 100 karakter.',

            'name.unique' => 'Nama kelas sudah digunakan pada tahun ajaran dan semester tersebut.',

            'level.required' => 'Tingkat kelas wajib dipilih.',
            'level.in' => 'Tingkat kelas tidak valid.',

            'major.max' => 'Nama jurusan maksimal 100 karakter.',

            'homeroomTeacherId.exists' => 'Wali kelas yang dipilih tidak valid.',

            'academicYear.required' => 'Tahun ajaran wajib diisi.',

            'academicYear.regex' => 'Format tahun ajaran harus seperti 2026/2027.',

            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester yang dipilih tidak valid.',

            'capacity.required' => 'Kapasitas kelas wajib diisi.',
            'capacity.integer' => 'Kapasitas kelas harus berupa angka.',
            'capacity.min' => 'Kapasitas kelas minimal 1 siswa.',
            'capacity.max' => 'Kapasitas kelas maksimal 100 siswa.',

            'description.max' => 'Catatan maksimal 1.000 karakter.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Reset form
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->level = '';
        $this->major = '';
        $this->homeroomTeacherId = '';
        $this->academicYear = $this->defaultAcademicYear();
        $this->semester = 'ganjil';
        $this->capacity = 36;
        $this->description = '';
        $this->isActive = true;

        $this->resetValidation();
    }

    private function defaultAcademicYear(): string
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('n');

        if ($month < 7) {
            $year--;
        }

        return $year.'/'.($year + 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $schoolClasses = SchoolClass::query()
            ->with('homeroomTeacher')

            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'level',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'major',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'academic_year',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'homeroomTeacher',
                                    function (
                                        Builder $query
                                    ) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                filled($this->filterAcademicYear),
                fn (Builder $query) => $query->where(
                    'academic_year',
                    $this->filterAcademicYear
                )
            )

            ->when(
                filled($this->filterSemester),
                fn (Builder $query) => $query->where(
                    'semester',
                    $this->filterSemester
                )
            )

            ->when(
                $this->filterStatus !== '',
                fn (Builder $query) => $query->where(
                    'is_active',
                    $this->filterStatus === '1'
                )
            )

            ->orderByDesc('academic_year')
            ->orderBy('semester')
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(10);

        $teachers = User::query()
            ->where('is_active', true)
            ->whereIn('role', [
                User::ROLE_WALI_KELAS,
                User::ROLE_GURU,
                User::ROLE_ADMIN,
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'role',
            ]);

        $academicYears = SchoolClass::query()
            ->whereNotNull('academic_year')
            ->where('academic_year', '!=', '')
            ->select('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        return view('livewire.classes.index', [
            'schoolClasses' => $schoolClasses,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
        ]);
    }
}
