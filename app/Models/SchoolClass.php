<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'major',
        'homeroom_teacher_id',
        'academic_year',
        'semester',
        'capacity',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'homeroom_teacher_id'
        );
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function activeStudents(): HasMany
    {
        return $this->students()
            ->where(
                'status',
                Student::STATUS_ACTIVE
            );
    }

    public function studentAttendances(): HasMany
    {
        return $this->hasMany(
            StudentAttendance::class
        );
    }

    public function getFullNameAttribute(): string
    {
        return $this->major
            ? $this->name.' - '.$this->major
            : $this->name;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active
            ? 'Aktif'
            : 'Tidak Aktif';
    }

    public function getSemesterLabelAttribute(): string
    {
        return ucfirst($this->semester);
    }
    public function studentViolations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function studentPermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentPermission::class);
    }

    public function counselingCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CounselingCase::class);
    }

}
