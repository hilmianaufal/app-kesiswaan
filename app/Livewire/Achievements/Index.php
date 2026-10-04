<?php

namespace App\Livewire\Achievements;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAchievement;
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
    public string $filterCategory = '';
    public string $filterLevel = '';
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public ?int $editingId = null;

    public string $studentId = '';
    public string $title = '';
    public string $category =
        StudentAchievement::CATEGORY_ACADEMIC;

    public string $level =
        StudentAchievement::LEVEL_SCHOOL;

    public string $rank =
        StudentAchievement::RANK_FIRST;

    public int $points = 10;

    public string $eventName = '';
    public string $organizer = '';
    public string $mentor = '';
    public string $achievementDate = '';
    public string $description = '';

    public $certificate = null;

    public ?string $existingCertificate = null;
    public bool $removeExistingCertificate = false;

    public function mount(): void
    {
        $this->achievementDate = now()->format('Y-m-d');
        $this->recalculatePoints();
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

    public function updatedFilterLevel(): void
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

    public function updatedLevel(): void
    {
        $this->recalculatePoints();
    }

    public function updatedRank(): void
    {
        $this->recalculatePoints();
    }

    public function updatedCertificate(): void
    {
        $this->validateOnly(
            'certificate',
            [
                'certificate' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'certificate.mimes' =>
                    'Sertifikat harus berupa PDF, JPG, PNG, atau WEBP.',

                'certificate.max' =>
                    'Ukuran sertifikat maksimal 5 MB.',
            ]
        );
    }

    public function recalculatePoints(): void
    {
        $this->points = StudentAchievement::calculatePoints(
            $this->level,
            $this->rank
        );
    }

    public function openCreateModal(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan dan verifikasi.'
        );

        $this->resetForm();

        $this->dispatch(
            'open-modal',
            modal: 'achievement-form'
        );
    }

    public function edit(int $id): void
    {
        $achievement = StudentAchievement::query()
            ->findOrFail($id);

        $this->ensureCanModify($achievement);
        $this->resetValidation();

        $this->editingId = $achievement->id;
        $this->studentId = (string) $achievement->student_id;
        $this->title = $achievement->title;
        $this->category = $achievement->category;
        $this->level = $achievement->level;
        $this->rank = $achievement->rank;
        $this->points = $achievement->points;
        $this->eventName = $achievement->event_name ?? '';
        $this->organizer = $achievement->organizer ?? '';
        $this->mentor = $achievement->mentor ?? '';

        $this->achievementDate = $achievement
            ->achievement_date
            ->format('Y-m-d');

        $this->description = $achievement->description ?? '';
        $this->certificate = null;
        $this->existingCertificate =
            $achievement->certificate_path;

        $this->removeExistingCertificate = false;

        $this->dispatch(
            'open-modal',
            modal: 'achievement-form'
        );
    }

    public function cancelNewCertificate(): void
    {
        $this->certificate = null;
    }

    public function removeCurrentCertificate(): void
    {
        $this->certificate = null;
        $this->existingCertificate = null;
        $this->removeExistingCertificate = true;
    }

    public function save(): void
    {
        abort_if(
            auth()->user()->isKepalaSekolah(),
            403,
            'Kepala sekolah memiliki akses pemantauan dan verifikasi.'
        );

        $validated = $this->validate(
            $this->rules(),
            $this->messages()
        );

        $student = Student::query()
            ->findOrFail($validated['studentId']);

        $achievement = $this->editingId
            ? StudentAchievement::query()
                ->findOrFail($this->editingId)
            : null;

        if ($achievement) {
            $this->ensureCanModify($achievement);
        }

        $oldCertificate = $achievement?->certificate_path;
        $newCertificate = null;

        if ($this->certificate) {
            $newCertificate = $this->certificate->store(
                'achievements/certificates',
                'public'
            );
        }

        $certificatePath = match (true) {
            filled($newCertificate) => $newCertificate,
            $this->removeExistingCertificate => null,
            default => $oldCertificate,
        };

        $calculatedPoints =
            StudentAchievement::calculatePoints(
                $validated['level'],
                $validated['rank']
            );

        try {
            DB::transaction(function () use (
                $validated,
                $student,
                $achievement,
                $certificatePath,
                $calculatedPoints
            ): void {
                $data = [
                    'student_id' => $student->id,
                    'school_class_id' => $student->school_class_id,
                    'title' => trim($validated['title']),
                    'category' => $validated['category'],
                    'level' => $validated['level'],
                    'rank' => $validated['rank'],
                    'points' => $calculatedPoints,

                    'event_name' => filled($validated['eventName'])
                        ? trim($validated['eventName'])
                        : null,

                    'organizer' => filled($validated['organizer'])
                        ? trim($validated['organizer'])
                        : null,

                    'mentor' => filled($validated['mentor'])
                        ? trim($validated['mentor'])
                        : null,

                    'achievement_date' =>
                        $validated['achievementDate'],

                    'description' => filled(
                        $validated['description']
                    )
                        ? trim($validated['description'])
                        : null,

                    'certificate_path' => $certificatePath,
                    'status' => StudentAchievement::STATUS_PENDING,
                    'verified_by' => null,
                    'verified_at' => null,
                ];

                if ($achievement) {
                    $achievement->update($data);

                    return;
                }

                $data['recorded_by'] = auth()->id();

                StudentAchievement::query()->create($data);
            });
        } catch (Throwable $exception) {
            if ($newCertificate) {
                Storage::disk('public')->delete($newCertificate);
            }

            throw $exception;
        }

        if (
            $oldCertificate
            && $oldCertificate !== $certificatePath
        ) {
            Storage::disk('public')->delete($oldCertificate);
        }

        $message = $this->editingId
            ? 'Prestasi berhasil diperbarui dan menunggu verifikasi ulang.'
            : 'Prestasi berhasil ditambahkan dan menunggu verifikasi.';

        $this->dispatch(
            'close-modal',
            modal: 'achievement-form'
        );

        $this->dispatch(
            'swal:success',
            title: 'Berhasil',
            text: $message
        );

        $this->resetForm();
        $this->resetPage();
    }

    public function verifyAchievement(int $id): void
    {
        $this->ensureCanVerify();

        $achievement = StudentAchievement::query()
            ->findOrFail($id);

        $achievement->update([
            'status' => StudentAchievement::STATUS_VERIFIED,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $this->dispatch(
            'swal:success',
            title: 'Prestasi terverifikasi',
            text: 'Poin penghargaan siswa sudah diaktifkan.'
        );
    }

    public function rejectAchievement(int $id): void
    {
        $this->ensureCanVerify();

        $achievement = StudentAchievement::query()
            ->findOrFail($id);

        $achievement->update([
            'status' => StudentAchievement::STATUS_REJECTED,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $this->dispatch(
            'swal:success',
            title: 'Prestasi ditolak',
            text: 'Data prestasi telah ditandai sebagai ditolak.'
        );
    }

    public function deleteAchievement(int $id): void
    {
        $achievement = StudentAchievement::query()
            ->findOrFail($id);

        $this->ensureCanModify($achievement);

        $certificate = $achievement->certificate_path;

        $achievement->delete();

        if ($certificate) {
            Storage::disk('public')->delete($certificate);
        }

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch(
            'swal:success',
            title: 'Prestasi dihapus',
            text: 'Data prestasi siswa berhasil dihapus.'
        );

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterCategory',
            'filterLevel',
            'filterStatus',
            'filterDateFrom',
            'filterDateTo',
        ]);

        $this->resetPage();
    }

    private function ensureCanModify(
        StudentAchievement $achievement
    ): void {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || $user->isGuruBk()
            || $achievement->recorded_by === $user->id;

        abort_unless(
            $allowed,
            403,
            'Anda tidak memiliki izin mengubah prestasi ini.'
        );
    }

    private function ensureCanVerify(): void
    {
        abort_unless(
            auth()->user()->isAdmin()
                || auth()->user()->isKepalaSekolah(),
            403,
            'Hanya admin atau kepala sekolah yang dapat memverifikasi prestasi.'
        );
    }

    private function rules(): array
    {
        return [
            'studentId' => [
                'required',
                Rule::exists('students', 'id'),
            ],

            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'category' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentAchievement::categories()
                    )
                ),
            ],

            'level' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentAchievement::levels()
                    )
                ),
            ],

            'rank' => [
                'required',
                Rule::in(
                    array_keys(
                        StudentAchievement::ranks()
                    )
                ),
            ],

            'eventName' => [
                'nullable',
                'string',
                'max:200',
            ],

            'organizer' => [
                'nullable',
                'string',
                'max:200',
            ],

            'mentor' => [
                'nullable',
                'string',
                'max:150',
            ],

            'achievementDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'certificate' => [
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
            'studentId.required' => 'Siswa wajib dipilih.',
            'studentId.exists' => 'Siswa yang dipilih tidak valid.',

            'title.required' => 'Nama prestasi wajib diisi.',
            'title.max' => 'Nama prestasi maksimal 200 karakter.',

            'category.required' => 'Kategori prestasi wajib dipilih.',
            'category.in' => 'Kategori prestasi tidak valid.',

            'level.required' => 'Tingkat prestasi wajib dipilih.',
            'level.in' => 'Tingkat prestasi tidak valid.',

            'rank.required' => 'Peringkat prestasi wajib dipilih.',
            'rank.in' => 'Peringkat prestasi tidak valid.',

            'eventName.max' => 'Nama kegiatan maksimal 200 karakter.',
            'organizer.max' => 'Penyelenggara maksimal 200 karakter.',
            'mentor.max' => 'Nama pembimbing maksimal 150 karakter.',

            'achievementDate.required' =>
                'Tanggal prestasi wajib diisi.',

            'achievementDate.before_or_equal' =>
                'Tanggal prestasi tidak boleh melebihi hari ini.',

            'description.max' =>
                'Deskripsi maksimal 5.000 karakter.',

            'certificate.mimes' =>
                'Sertifikat harus berupa PDF, JPG, PNG, atau WEBP.',

            'certificate.max' =>
                'Ukuran sertifikat maksimal 5 MB.',
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->studentId = '';
        $this->title = '';

        $this->category =
            StudentAchievement::CATEGORY_ACADEMIC;

        $this->level =
            StudentAchievement::LEVEL_SCHOOL;

        $this->rank =
            StudentAchievement::RANK_FIRST;

        $this->eventName = '';
        $this->organizer = '';
        $this->mentor = '';
        $this->achievementDate = now()->format('Y-m-d');
        $this->description = '';
        $this->certificate = null;
        $this->existingCertificate = null;
        $this->removeExistingCertificate = false;

        $this->recalculatePoints();
        $this->resetValidation();
    }

    public function render()
    {
        $achievements = StudentAchievement::query()
            ->with([
                'student.schoolClass',
                'recorder',
                'verifier',
            ])

            ->when(
                filled($this->search),
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (
                        Builder $query
                    ) use ($search): void {
                        $query
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere(
                                'event_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'organizer',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'mentor',
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
                filled($this->filterLevel),
                fn (Builder $query) => $query->where(
                    'level',
                    $this->filterLevel
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
                    'achievement_date',
                    '>=',
                    $this->filterDateFrom
                )
            )

            ->when(
                filled($this->filterDateTo),
                fn (Builder $query) => $query->whereDate(
                    'achievement_date',
                    '<=',
                    $this->filterDateTo
                )
            )

            ->orderByDesc('achievement_date')
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
            'total' => StudentAchievement::query()->count(),

            'verified' => StudentAchievement::query()
                ->where(
                    'status',
                    StudentAchievement::STATUS_VERIFIED
                )
                ->count(),

            'pending' => StudentAchievement::query()
                ->where(
                    'status',
                    StudentAchievement::STATUS_PENDING
                )
                ->count(),

            'points' => (int) StudentAchievement::query()
                ->where(
                    'status',
                    StudentAchievement::STATUS_VERIFIED
                )
                ->sum('points'),
        ];

        $topStudents = StudentAchievement::query()
            ->where(
                'status',
                StudentAchievement::STATUS_VERIFIED
            )
            ->select('student_id')
            ->selectRaw('COUNT(*) as total_achievements')
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
            'livewire.achievements.index',
            [
                'achievements' => $achievements,
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'statistics' => $statistics,
                'topStudents' => $topStudents,

                'categories' =>
                    StudentAchievement::categories(),

                'levels' =>
                    StudentAchievement::levels(),

                'ranks' =>
                    StudentAchievement::ranks(),

                'statuses' =>
                    StudentAchievement::statuses(),
            ]
        );
    }
}
