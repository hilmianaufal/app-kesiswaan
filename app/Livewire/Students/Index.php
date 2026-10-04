<?php

namespace App\Livewire\Students;

use App\Models\SchoolClass;
use App\Models\Student;
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
    public string $filterGender = '';
    public string $filterStatus = '';

    public ?int $editingId = null;

    public string $schoolClassId = '';
    public string $nis = '';
    public string $nisn = '';
    public string $name = '';
    public string $gender = '';
    public string $birthPlace = '';
    public string $birthDate = '';
    public string $address = '';
    public string $phone = '';
    public string $fatherName = '';
    public string $motherName = '';
    public string $guardianName = '';
    public string $parentPhone = '';
    public string $enrollmentYear = '';
    public string $status = Student::STATUS_ACTIVE;
    public string $notes = '';

    public $photo = null;

    public ?string $existingPhoto = null;
    public bool $removeExistingPhoto = false;

    public function mount(): void
    {
        $this->enrollmentYear = now()->format('Y');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClassId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGender(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPhoto(): void
    {
        $this->validateOnly('photo', [
            'photo' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ], [
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->dispatch(
            'open-modal',
            modal: 'student-form'
        );
    }

    public function edit(int $id): void
    {
        $student = Student::query()->findOrFail($id);

        $this->resetValidation();

        $this->editingId = $student->id;

        $this->schoolClassId = $student->school_class_id
            ? (string) $student->school_class_id
            : '';

        $this->nis = $student->nis;
        $this->nisn = $student->nisn ?? '';
        $this->name = $student->name;
        $this->gender = $student->gender;
        $this->birthPlace = $student->birth_place ?? '';

        $this->birthDate = $student->birth_date
            ? $student->birth_date->format('Y-m-d')
            : '';

        $this->address = $student->address ?? '';
        $this->phone = $student->phone ?? '';
        $this->fatherName = $student->father_name ?? '';
        $this->motherName = $student->mother_name ?? '';
        $this->guardianName = $student->guardian_name ?? '';
        $this->parentPhone = $student->parent_phone ?? '';

        $this->enrollmentYear = $student->enrollment_year
            ? (string) $student->enrollment_year
            : '';

        $this->status = $student->status;
        $this->notes = $student->notes ?? '';
        $this->photo = null;
        $this->existingPhoto = $student->photo;
        $this->removeExistingPhoto = false;

        $this->dispatch(
            'open-modal',
            modal: 'student-form'
        );
    }

    public function removeCurrentPhoto(): void
    {
        $this->photo = null;
        $this->existingPhoto = null;
        $this->removeExistingPhoto = true;
    }

    public function cancelNewPhoto(): void
    {
        $this->photo = null;
    }

    public function save(): void
    {
        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $student = $this->editingId
            ? Student::query()->findOrFail($this->editingId)
            : null;

        $oldPhoto = $student?->photo;
        $newPhoto = null;

        if ($this->photo) {
            $newPhoto = $this->photo->store(
                'students',
                'public'
            );
        }

        $photoPath = match (true) {
            filled($newPhoto) => $newPhoto,
            $this->removeExistingPhoto => null,
            default => $oldPhoto,
        };

        try {
            DB::transaction(function () use (
                $validated,
                $student,
                $photoPath
            ): void {
                $data = [
                    'school_class_id' => filled(
                        $validated['schoolClassId']
                    )
                        ? (int) $validated['schoolClassId']
                        : null,

                    'nis' => trim($validated['nis']),

                    'nisn' => filled($validated['nisn'])
                        ? trim($validated['nisn'])
                        : null,

                    'name' => trim($validated['name']),
                    'gender' => $validated['gender'],

                    'birth_place' => filled($validated['birthPlace'])
                        ? trim($validated['birthPlace'])
                        : null,

                    'birth_date' => filled($validated['birthDate'])
                        ? $validated['birthDate']
                        : null,

                    'address' => filled($validated['address'])
                        ? trim($validated['address'])
                        : null,

                    'phone' => filled($validated['phone'])
                        ? trim($validated['phone'])
                        : null,

                    'father_name' => filled($validated['fatherName'])
                        ? trim($validated['fatherName'])
                        : null,

                    'mother_name' => filled($validated['motherName'])
                        ? trim($validated['motherName'])
                        : null,

                    'guardian_name' => filled($validated['guardianName'])
                        ? trim($validated['guardianName'])
                        : null,

                    'parent_phone' => filled($validated['parentPhone'])
                        ? trim($validated['parentPhone'])
                        : null,

                    'enrollment_year' => filled(
                        $validated['enrollmentYear']
                    )
                        ? (int) $validated['enrollmentYear']
                        : null,

                    'status' => $validated['status'],

                    'notes' => filled($validated['notes'])
                        ? trim($validated['notes'])
                        : null,

                    'photo' => $photoPath,
                ];

                if ($student) {
                    $student->update($data);

                    return;
                }

                $data['created_by'] = auth()->id();

                Student::query()->create($data);
            });
        } catch (Throwable $exception) {
            if ($newPhoto) {
                Storage::disk('public')->delete($newPhoto);
            }

            throw $exception;
        }

        if (
            $oldPhoto
            && $oldPhoto !== $photoPath
        ) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $message = $this->editingId
            ? 'Data siswa berhasil diperbarui.'
            : 'Data siswa berhasil ditambahkan.';

        $this->dispatch(
            'close-modal',
            modal: 'student-form'
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
        $student = Student::query()->findOrFail($id);
        $photo = $student->photo;

        $student->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Data dihapus',
            text: 'Data siswa berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterGender',
            'filterStatus',
        ]);

        $this->resetPage();
    }

    private function rules(): array
    {
        $maximumYear = now()->year + 1;

        return [
            'schoolClassId' => [
                'nullable',
                Rule::exists('school_classes', 'id'),
            ],

            'nis' => [
                'required',
                'string',
                'max:30',
                Rule::unique('students', 'nis')
                    ->ignore($this->editingId),
            ],

            'nisn' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('students', 'nisn')
                    ->ignore($this->editingId),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'gender' => [
                'required',
                Rule::in(array_keys(Student::genders())),
            ],

            'birthPlace' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birthDate' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:25',
            ],

            'fatherName' => [
                'nullable',
                'string',
                'max:150',
            ],

            'motherName' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardianName' => [
                'nullable',
                'string',
                'max:150',
            ],

            'parentPhone' => [
                'nullable',
                'string',
                'max:25',
            ],

            'enrollmentYear' => [
                'nullable',
                'integer',
                'min:2000',
                'max:'.$maximumYear,
            ],

            'status' => [
                'required',
                Rule::in(array_keys(Student::statuses())),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'photo' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'schoolClassId.exists' => 'Kelas yang dipilih tidak valid.',

            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan siswa lain.',
            'nis.max' => 'NIS maksimal 30 karakter.',

            'nisn.unique' => 'NISN sudah digunakan siswa lain.',
            'nisn.max' => 'NISN maksimal 30 karakter.',

            'name.required' => 'Nama siswa wajib diisi.',
            'name.max' => 'Nama siswa maksimal 150 karakter.',

            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',

            'birthPlace.max' => 'Tempat lahir maksimal 100 karakter.',
            'birthDate.date' => 'Tanggal lahir tidak valid.',
            'birthDate.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',

            'address.max' => 'Alamat maksimal 2.000 karakter.',
            'phone.max' => 'Nomor HP maksimal 25 karakter.',

            'fatherName.max' => 'Nama ayah maksimal 150 karakter.',
            'motherName.max' => 'Nama ibu maksimal 150 karakter.',
            'guardianName.max' => 'Nama wali maksimal 150 karakter.',
            'parentPhone.max' => 'Nomor orang tua maksimal 25 karakter.',

            'enrollmentYear.integer' => 'Tahun masuk harus berupa angka.',
            'enrollmentYear.min' => 'Tahun masuk minimal tahun 2000.',

            'status.required' => 'Status siswa wajib dipilih.',
            'status.in' => 'Status siswa tidak valid.',

            'notes.max' => 'Catatan maksimal 2.000 karakter.',

            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->schoolClassId = '';
        $this->nis = '';
        $this->nisn = '';
        $this->name = '';
        $this->gender = '';
        $this->birthPlace = '';
        $this->birthDate = '';
        $this->address = '';
        $this->phone = '';
        $this->fatherName = '';
        $this->motherName = '';
        $this->guardianName = '';
        $this->parentPhone = '';
        $this->enrollmentYear = now()->format('Y');
        $this->status = Student::STATUS_ACTIVE;
        $this->notes = '';
        $this->photo = null;
        $this->existingPhoto = null;
        $this->removeExistingPhoto = false;

        $this->resetValidation();
    }

    public function render()
    {
        $students = Student::query()
            ->with('schoolClass')

            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%")
                                ->orWhere('nisn', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere(
                                    'parent_phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'schoolClass',
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
                filled($this->filterClassId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->filterClassId
                )
            )

            ->when(
                filled($this->filterGender),
                fn (Builder $query) => $query->where(
                    'gender',
                    $this->filterGender
                )
            )

            ->when(
                filled($this->filterStatus),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->filterStatus
                )
            )

            ->orderBy('name')
            ->paginate(10);

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
            ]);

        $statistics = [
            'total' => Student::query()->count(),

            'active' => Student::query()
                ->where('status', Student::STATUS_ACTIVE)
                ->count(),

            'male' => Student::query()
                ->where('gender', Student::GENDER_MALE)
                ->count(),

            'female' => Student::query()
                ->where('gender', Student::GENDER_FEMALE)
                ->count(),
        ];

        return view('livewire.students.index', [
            'students' => $students,
            'schoolClasses' => $schoolClasses,
            'statistics' => $statistics,
            'statuses' => Student::statuses(),
            'genders' => Student::genders(),
        ]);
    }
}
