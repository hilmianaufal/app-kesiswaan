<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use HasFactory;

    public const STATUS_PRESENT = 'present';
    public const STATUS_SICK = 'sick';
    public const STATUS_PERMIT = 'permit';
    public const STATUS_ABSENT = 'absent';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'attendance_date',
        'status',
        'arrival_time',
        'departure_time',
        'is_late',
        'is_early_leave',
        'note',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'is_late' => 'boolean',
            'is_early_leave' => 'boolean',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Hadir',
            self::STATUS_SICK => 'Sakit',
            self::STATUS_PERMIT => 'Izin',
            self::STATUS_ABSENT => 'Alfa',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]
            ?? 'Tidak diketahui';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'emerald',
            self::STATUS_SICK => 'blue',
            self::STATUS_PERMIT => 'amber',
            self::STATUS_ABSENT => 'red',
            default => 'slate',
        };
    }
}
