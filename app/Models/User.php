<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_KEPALA_SEKOLAH = 'kepala_sekolah';
    public const ROLE_WALI_KELAS = 'wali_kelas';
    public const ROLE_GURU_BK = 'guru_bk';
    public const ROLE_GURU = 'guru';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::ROLE_WALI_KELAS => 'Wali Kelas',
            self::ROLE_GURU_BK => 'Guru BK',
            self::ROLE_GURU => 'Guru',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roles()[$this->role] ?? 'Pengguna';
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === self::ROLE_KEPALA_SEKOLAH;
    }

    public function isWaliKelas(): bool
    {
        return $this->role === self::ROLE_WALI_KELAS;
    }

    public function isGuruBk(): bool
    {
        return $this->role === self::ROLE_GURU_BK;
    }

    public function isGuru(): bool
    {
        return $this->role === self::ROLE_GURU;
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(
            SchoolClass::class,
            'homeroom_teacher_id'
        );
    }
    public function recordedDevelopmentNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentDevelopmentNote::class,
            'recorded_by'
        );
    }

    public function recordedViolations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentViolation::class,
            'recorded_by'
        );
    }

    public function recordedAchievements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentAchievement::class,
            'recorded_by'
        );
    }

    public function verifiedAchievements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentAchievement::class,
            'verified_by'
        );
    }

    public function recordedPermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentPermission::class,
            'recorded_by'
        );
    }

    public function approvedPermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            StudentPermission::class,
            'approved_by'
        );
    }

    public function assignedCounselingCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            CounselingCase::class,
            'assigned_to'
        );
    }

    public function createdCounselingCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            CounselingCase::class,
            'created_by'
        );
    }

    public function recordedCounselingSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            CounselingSession::class,
            'recorded_by'
        );
    }

}
