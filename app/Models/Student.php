<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MOVED = 'moved';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_DROPPED = 'dropped';

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    protected $fillable = [
        'school_class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'father_name',
        'mother_name',
        'guardian_name',
        'parent_phone',
        'photo',
        'enrollment_year',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'enrollment_year' => 'integer',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            self::STATUS_MOVED => 'Pindah',
            self::STATUS_GRADUATED => 'Lulus',
            self::STATUS_DROPPED => 'Keluar',
        ];
    }

    public static function genders(): array
    {
        return [
            self::GENDER_MALE => 'Laki-laki',
            self::GENDER_FEMALE => 'Perempuan',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            StudentAttendance::class
        );
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]
            ?? 'Tidak diketahui';
    }

    public function getGenderLabelAttribute(): string
    {
        return self::genders()[$this->gender]
            ?? 'Tidak diketahui';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo
            ? Storage::disk('public')->url($this->photo)
            : null;
    }

    public function getInitialAttribute(): string
    {
        return strtoupper(
            mb_substr($this->name, 0, 1)
        );
    }
    public function developmentNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentDevelopmentNote::class);
    }

    public function violations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function getTotalViolationPointsAttribute(): int
    {
        return (int) $this->violations()->sum('points');
    }

    public function achievements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function getTotalAchievementPointsAttribute(): int
    {
        return (int) $this->achievements()
            ->where(
                'status',
                StudentAchievement::STATUS_VERIFIED
            )
            ->sum('points');
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentPermission::class);
    }

    public function counselingCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CounselingCase::class);
    }

}
