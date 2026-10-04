<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CounselingCase extends Model
{
    use HasFactory;

    public const CATEGORY_ACADEMIC = 'academic';
    public const CATEGORY_DISCIPLINE = 'discipline';
    public const CATEGORY_BEHAVIOR = 'behavior';
    public const CATEGORY_SOCIAL = 'social';
    public const CATEGORY_FAMILY = 'family';
    public const CATEGORY_CAREER = 'career';
    public const CATEGORY_PERSONAL = 'personal';
    public const CATEGORY_OTHER = 'other';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const CONFIDENTIALITY_NORMAL = 'normal';
    public const CONFIDENTIALITY_CONFIDENTIAL = 'confidential';
    public const CONFIDENTIALITY_HIGHLY_CONFIDENTIAL =
        'highly_confidential';

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_MONITORING = 'monitoring';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'case_number',
        'title',
        'problem_category',
        'priority',
        'confidentiality_level',
        'summary',
        'status',
        'opened_at',
        'closed_at',
        'assigned_to',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'date',
            'closed_at' => 'date',
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ACADEMIC => 'Akademik',
            self::CATEGORY_DISCIPLINE => 'Kedisiplinan',
            self::CATEGORY_BEHAVIOR => 'Perilaku',
            self::CATEGORY_SOCIAL => 'Hubungan Sosial',
            self::CATEGORY_FAMILY => 'Keluarga',
            self::CATEGORY_CAREER => 'Karier dan Masa Depan',
            self::CATEGORY_PERSONAL => 'Masalah Pribadi',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Rendah',
            self::PRIORITY_MEDIUM => 'Sedang',
            self::PRIORITY_HIGH => 'Tinggi',
            self::PRIORITY_URGENT => 'Mendesak',
        ];
    }

    public static function confidentialityLevels(): array
    {
        return [
            self::CONFIDENTIALITY_NORMAL => 'Biasa',
            self::CONFIDENTIALITY_CONFIDENTIAL => 'Rahasia',
            self::CONFIDENTIALITY_HIGHLY_CONFIDENTIAL =>
                'Sangat Rahasia',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Kasus Baru',
            self::STATUS_IN_PROGRESS => 'Dalam Penanganan',
            self::STATUS_MONITORING => 'Pemantauan',
            self::STATUS_RESOLVED => 'Terselesaikan',
            self::STATUS_CLOSED => 'Ditutup',
        ];
    }

    public static function generateCaseNumber(): string
    {
        do {
            $number = 'BK-'
                .now()->format('Ym')
                .'-'
                .Str::upper(Str::random(6));
        } while (
            self::query()
                ->where('case_number', $number)
                ->exists()
        );

        return $number;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CounselingSession::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->problem_category]
            ?? 'Lainnya';
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorities()[$this->priority]
            ?? 'Tidak diketahui';
    }

    public function getConfidentialityLabelAttribute(): string
    {
        return self::confidentialityLevels()[
            $this->confidentiality_level
        ] ?? 'Tidak diketahui';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]
            ?? 'Tidak diketahui';
    }

    public function getIsClosedAttribute(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_RESOLVED,
                self::STATUS_CLOSED,
            ],
            true
        );
    }
}
