<?php

namespace App\Livewire\StudentProfiles;

use App\Models\CounselingCase;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentDevelopmentNote;
use App\Models\StudentPermission;
use App\Models\StudentViolation;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterClassId = '';

    public string $filterCondition = '';

    public ?int $selectedStudentId = null;

    public string $activeTab = 'overview';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClassId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCondition(): void
    {
        $this->resetPage();
    }

    public function openProfile(int $studentId): void
    {
        Student::query()->findOrFail($studentId);

        $this->selectedStudentId = $studentId;
        $this->activeTab = 'overview';

        $this->dispatch(
            'open-modal',
            modal: 'student-profile-detail'
        );
    }

    public function setActiveTab(string $tab): void
    {
        $allowedTabs = [
            'overview',
            'attendance',
            'development',
            'violations',
            'achievements',
            'permissions',
            'counseling',
        ];

        if (! in_array($tab, $allowedTabs, true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterClassId',
            'filterCondition',
        ]);

        $this->resetPage();
    }

    private function restrictDevelopmentNotes(
        $query
    ): void {
        $user = auth()->user();

        if (
            $user->isAdmin()
            || $user->isGuruBk()
        ) {
            return;
        }

        $query->where(function ($query) use ($user): void {
            $query
                ->where('is_private', false)
                ->orWhere('recorded_by', $user->id);
        });
    }

    private function restrictCounselingCases(
        $query
    ): void {
        $user = auth()->user();

        if (
            $user->isAdmin()
            || $user->isGuruBk()
        ) {
            return;
        }

        if ($user->isKepalaSekolah()) {
            $query->where(
                function ($query) use ($user): void {
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

            return;
        }

        $query->where(
            function ($query) use ($user): void {
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

    private function applyNeedsAttention(
        Builder $query
    ): void {
        $query->where(function (Builder $query): void {
            $query
                ->whereHas('violations')

                ->orWhereHas(
                    'permissions',
                    function ($query): void {
                        $query->where(
                            'status',
                            StudentPermission::STATUS_PENDING
                        );
                    }
                )

                ->orWhereHas(
                    'developmentNotes',
                    function ($query): void {
                        $this->restrictDevelopmentNotes($query);

                        $query->whereIn('type', [
                            StudentDevelopmentNote::TYPE_ATTENTION,
                            StudentDevelopmentNote::TYPE_FOLLOW_UP,
                        ]);
                    }
                );
        });
    }

    private function applyClearCondition(
        Builder $query
    ): void {
        $query
            ->whereDoesntHave('violations')

            ->whereDoesntHave(
                'permissions',
                function ($query): void {
                    $query->where(
                        'status',
                        StudentPermission::STATUS_PENDING
                    );
                }
            )

            ->whereDoesntHave(
                'developmentNotes',
                function ($query): void {
                    $this->restrictDevelopmentNotes($query);

                    $query->whereIn('type', [
                        StudentDevelopmentNote::TYPE_ATTENTION,
                        StudentDevelopmentNote::TYPE_FOLLOW_UP,
                    ]);
                }
            );
    }

    private function selectedStudent()
    {
        if (! $this->selectedStudentId) {
            return null;
        }

        return Student::query()
            ->with([
                'schoolClass.homeroomTeacher',

                'attendances' => function ($query): void {
                    $query
                        ->orderByDesc('attendance_date')
                        ->limit(30);
                },

                'developmentNotes' => function ($query): void {
                    $this->restrictDevelopmentNotes($query);

                    $query
                        ->with('recorder')
                        ->orderByDesc('note_date')
                        ->orderByDesc('id')
                        ->limit(30);
                },

                'violations' => function ($query): void {
                    $query
                        ->with('recorder')
                        ->orderByDesc('violation_date')
                        ->orderByDesc('id')
                        ->limit(30);
                },

                'achievements' => function ($query): void {
                    $query
                        ->with([
                            'recorder',
                            'verifier',
                        ])
                        ->orderByDesc('achievement_date')
                        ->orderByDesc('id')
                        ->limit(30);
                },

                'permissions' => function ($query): void {
                    $query
                        ->with([
                            'recorder',
                            'approver',
                        ])
                        ->orderByDesc('permission_date')
                        ->orderByDesc('id')
                        ->limit(30);
                },
            ])

            ->withCount([
                'attendances',

                'attendances as present_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            'present'
                        );
                    },

                'attendances as sick_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            'sick'
                        );
                    },

                'attendances as permit_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            'permit'
                        );
                    },

                'attendances as absent_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            'absent'
                        );
                    },

                'attendances as late_count' =>
                    function ($query): void {
                        $query->where(
                            'is_late',
                            true
                        );
                    },

                'developmentNotes as development_notes_count' =>
                    function ($query): void {
                        $this->restrictDevelopmentNotes($query);
                    },

                'violations',

                'achievements as verified_achievements_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            StudentAchievement::STATUS_VERIFIED
                        );
                    },

                'permissions',

                'permissions as pending_permissions_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            StudentPermission::STATUS_PENDING
                        );
                    },

                'counselingCases as counseling_cases_count' =>
                    function ($query): void {
                        $this->restrictCounselingCases($query);
                    },
            ])

            ->addSelect([
                'violation_points' =>
                    StudentViolation::query()
                        ->selectRaw(
                            'COALESCE(SUM(points), 0)'
                        )
                        ->whereColumn(
                            'student_id',
                            'students.id'
                        ),

                'achievement_points' =>
                    StudentAchievement::query()
                        ->selectRaw(
                            'COALESCE(SUM(points), 0)'
                        )
                        ->whereColumn(
                            'student_id',
                            'students.id'
                        )
                        ->where(
                            'status',
                            StudentAchievement::STATUS_VERIFIED
                        ),
            ])

            ->find($this->selectedStudentId);
    }

    private function visibleCounselingCases()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        $query = CounselingCase::query()
            ->with([
                'assignedTeacher',
                'creator',

                'sessions' => function ($query): void {
                    $query
                        ->with('recorder')
                        ->orderByDesc('session_date')
                        ->orderByDesc('start_time');
                },
            ])
            ->where(
                'student_id',
                $this->selectedStudentId
            );

        $this->restrictCounselingCases($query);

        return $query
            ->orderByDesc('opened_at')
            ->get();
    }

    public function render()
    {
        $studentsQuery = Student::query()
            ->with('schoolClass')
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )

            ->withCount([
                'attendances',
                'violations',

                'achievements as verified_achievements_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            StudentAchievement::STATUS_VERIFIED
                        );
                    },

                'permissions as pending_permissions_count' =>
                    function ($query): void {
                        $query->where(
                            'status',
                            StudentPermission::STATUS_PENDING
                        );
                    },

                'developmentNotes as development_notes_count' =>
                    function ($query): void {
                        $this->restrictDevelopmentNotes($query);
                    },

                'counselingCases as counseling_cases_count' =>
                    function ($query): void {
                        $this->restrictCounselingCases($query);
                    },
            ])

            ->addSelect([
                'violation_points' =>
                    StudentViolation::query()
                        ->selectRaw(
                            'COALESCE(SUM(points), 0)'
                        )
                        ->whereColumn(
                            'student_id',
                            'students.id'
                        ),

                'achievement_points' =>
                    StudentAchievement::query()
                        ->selectRaw(
                            'COALESCE(SUM(points), 0)'
                        )
                        ->whereColumn(
                            'student_id',
                            'students.id'
                        )
                        ->where(
                            'status',
                            StudentAchievement::STATUS_VERIFIED
                        ),
            ])

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
                $this->filterCondition === 'needs_attention',
                function (Builder $query): void {
                    $this->applyNeedsAttention($query);
                }
            )

            ->when(
                $this->filterCondition === 'has_achievement',
                function (Builder $query): void {
                    $query->whereHas(
                        'achievements',
                        function ($query): void {
                            $query->where(
                                'status',
                                StudentAchievement::STATUS_VERIFIED
                            );
                        }
                    );
                }
            )

            ->when(
                $this->filterCondition === 'pending_permission',
                function (Builder $query): void {
                    $query->whereHas(
                        'permissions',
                        function ($query): void {
                            $query->where(
                                'status',
                                StudentPermission::STATUS_PENDING
                            );
                        }
                    );
                }
            )

            ->when(
                $this->filterCondition === 'clear',
                function (Builder $query): void {
                    $this->applyClearCondition($query);
                }
            );

        $students = $studentsQuery
            ->orderBy('name')
            ->paginate(12);

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

        $needsAttentionQuery = Student::query()
            ->where(
                'status',
                Student::STATUS_ACTIVE
            );

        $this->applyNeedsAttention(
            $needsAttentionQuery
        );

        $statistics = [
            'students' => Student::query()
                ->where(
                    'status',
                    Student::STATUS_ACTIVE
                )
                ->count(),

            'needs_attention' =>
                $needsAttentionQuery->count(),

            'achievements' =>
                StudentAchievement::query()
                    ->where(
                        'status',
                        StudentAchievement::STATUS_VERIFIED
                    )
                    ->count(),

            'pending_permissions' =>
                StudentPermission::query()
                    ->where(
                        'status',
                        StudentPermission::STATUS_PENDING
                    )
                    ->count(),
        ];

        return view(
            'livewire.student-profiles.index',
            [
                'students' => $students,
                'schoolClasses' => $schoolClasses,
                'statistics' => $statistics,

                'selectedStudent' =>
                    $this->selectedStudent(),

                'visibleCounselingCases' =>
                    $this->visibleCounselingCases(),
            ]
        );
    }
}
