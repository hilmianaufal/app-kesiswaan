<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudentPermission extends Model
{
    use HasFactory;

    public const TYPE_ABSENCE = 'absence';
    public const TYPE_OUT = 'out';
    public const TYPE_EARLY_LEAVE = 'early_leave';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'permission_type',
        'permission_date',
        'start_time',
        'expected_return_time',
        'actual_return_time',
        'reason',
        'evidence_path',
        'status',
        'rejection_note',
        'recorded_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'permission_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_ABSENCE => 'Izin Tidak Masuk',
            self::TYPE_OUT => 'Izin Keluar Sekolah',
            self::TYPE_EARLY_LEAVE => 'Pulang Lebih Awal',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_COMPLETED => 'Sudah Kembali',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->permission_type]
            ?? 'Tidak diketahui';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]
            ?? 'Tidak diketahui';
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->evidence_path
            ? Storage::disk('public')->url($this->evidence_path)
            : null;
    }

    public function getIsCurrentlyOutsideAttribute(): bool
    {
        return $this->permission_type === self::TYPE_OUT
            && $this->status === self::STATUS_APPROVED
            && blank($this->actual_return_time);
    }
}
