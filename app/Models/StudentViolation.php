<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentViolation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'violation_type_id',
        'violation_name',
        'category',
        'points',
        'violation_date',
        'location',
        'chronology',
        'action_taken',
        'follow_up_status',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
            'points' => 'integer',
        ];
    }

    public static function followUpStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Tindak Lanjut',
            self::STATUS_IN_PROGRESS => 'Sedang Ditindaklanjuti',
            self::STATUS_COMPLETED => 'Selesai',
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

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    public function getCategoryLabelAttribute(): string
    {
        return ViolationType::categories()[$this->category]
            ?? 'Tidak diketahui';
    }

    public function getFollowUpStatusLabelAttribute(): string
    {
        return self::followUpStatuses()[$this->follow_up_status]
            ?? 'Tidak diketahui';
    }
}
