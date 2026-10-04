<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDevelopmentNote extends Model
{
    use HasFactory;

    public const TYPE_POSITIVE = 'positive';
    public const TYPE_ATTENTION = 'attention';
    public const TYPE_FOLLOW_UP = 'follow_up';

    public const CATEGORY_ACADEMIC = 'academic';
    public const CATEGORY_ATTITUDE = 'attitude';
    public const CATEGORY_DISCIPLINE = 'discipline';
    public const CATEGORY_SOCIAL = 'social';
    public const CATEGORY_RESPONSIBILITY = 'responsibility';
    public const CATEGORY_CLEANLINESS = 'cleanliness';
    public const CATEGORY_OTHER = 'other';

    public const FOLLOW_UP_PENDING = 'pending';
    public const FOLLOW_UP_IN_PROGRESS = 'in_progress';
    public const FOLLOW_UP_COMPLETED = 'completed';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'note_date',
        'category',
        'type',
        'title',
        'description',
        'follow_up',
        'follow_up_status',
        'is_private',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'note_date' => 'date',
            'is_private' => 'boolean',
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ACADEMIC => 'Akademik',
            self::CATEGORY_ATTITUDE => 'Sikap dan Perilaku',
            self::CATEGORY_DISCIPLINE => 'Kedisiplinan',
            self::CATEGORY_SOCIAL => 'Hubungan Sosial',
            self::CATEGORY_RESPONSIBILITY => 'Tanggung Jawab',
            self::CATEGORY_CLEANLINESS => 'Kebersihan',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_POSITIVE => 'Catatan Positif',
            self::TYPE_ATTENTION => 'Perlu Perhatian',
            self::TYPE_FOLLOW_UP => 'Perlu Tindak Lanjut',
        ];
    }

    public static function followUpStatuses(): array
    {
        return [
            self::FOLLOW_UP_PENDING => 'Menunggu Tindak Lanjut',
            self::FOLLOW_UP_IN_PROGRESS => 'Sedang Ditindaklanjuti',
            self::FOLLOW_UP_COMPLETED => 'Selesai',
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

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category]
            ?? 'Lainnya';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type]
            ?? 'Catatan';
    }

    public function getFollowUpStatusLabelAttribute(): string
    {
        return self::followUpStatuses()[$this->follow_up_status]
            ?? 'Belum diketahui';
    }
}
