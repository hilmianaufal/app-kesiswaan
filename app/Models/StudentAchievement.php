<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudentAchievement extends Model
{
    use HasFactory;

    public const CATEGORY_ACADEMIC = 'academic';
    public const CATEGORY_NON_ACADEMIC = 'non_academic';
    public const CATEGORY_RELIGIOUS = 'religious';
    public const CATEGORY_ORGANIZATION = 'organization';
    public const CATEGORY_OTHER = 'other';

    public const LEVEL_SCHOOL = 'school';
    public const LEVEL_SUBDISTRICT = 'subdistrict';
    public const LEVEL_DISTRICT = 'district';
    public const LEVEL_PROVINCE = 'province';
    public const LEVEL_NATIONAL = 'national';
    public const LEVEL_INTERNATIONAL = 'international';

    public const RANK_PARTICIPANT = 'participant';
    public const RANK_FINALIST = 'finalist';
    public const RANK_THIRD = 'third';
    public const RANK_SECOND = 'second';
    public const RANK_FIRST = 'first';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'student_id',
        'school_class_id',
        'title',
        'category',
        'level',
        'rank',
        'points',
        'event_name',
        'organizer',
        'mentor',
        'achievement_date',
        'description',
        'certificate_path',
        'status',
        'recorded_by',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'achievement_date' => 'date',
            'points' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ACADEMIC => 'Akademik',
            self::CATEGORY_NON_ACADEMIC => 'Nonakademik',
            self::CATEGORY_RELIGIOUS => 'Keagamaan',
            self::CATEGORY_ORGANIZATION => 'Organisasi',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public static function levels(): array
    {
        return [
            self::LEVEL_SCHOOL => 'Sekolah',
            self::LEVEL_SUBDISTRICT => 'Kecamatan',
            self::LEVEL_DISTRICT => 'Kabupaten/Kota',
            self::LEVEL_PROVINCE => 'Provinsi',
            self::LEVEL_NATIONAL => 'Nasional',
            self::LEVEL_INTERNATIONAL => 'Internasional',
        ];
    }

    public static function ranks(): array
    {
        return [
            self::RANK_PARTICIPANT => 'Peserta',
            self::RANK_FINALIST => 'Finalis',
            self::RANK_THIRD => 'Juara 3',
            self::RANK_SECOND => 'Juara 2',
            self::RANK_FIRST => 'Juara 1',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_VERIFIED => 'Terverifikasi',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    public static function calculatePoints(
        string $level,
        string $rank
    ): int {
        $pointMatrix = [
            self::LEVEL_SCHOOL => [
                self::RANK_PARTICIPANT => 2,
                self::RANK_FINALIST => 4,
                self::RANK_THIRD => 6,
                self::RANK_SECOND => 8,
                self::RANK_FIRST => 10,
            ],
            self::LEVEL_SUBDISTRICT => [
                self::RANK_PARTICIPANT => 3,
                self::RANK_FINALIST => 6,
                self::RANK_THIRD => 10,
                self::RANK_SECOND => 15,
                self::RANK_FIRST => 20,
            ],
            self::LEVEL_DISTRICT => [
                self::RANK_PARTICIPANT => 5,
                self::RANK_FINALIST => 10,
                self::RANK_THIRD => 15,
                self::RANK_SECOND => 20,
                self::RANK_FIRST => 30,
            ],
            self::LEVEL_PROVINCE => [
                self::RANK_PARTICIPANT => 10,
                self::RANK_FINALIST => 20,
                self::RANK_THIRD => 30,
                self::RANK_SECOND => 40,
                self::RANK_FIRST => 50,
            ],
            self::LEVEL_NATIONAL => [
                self::RANK_PARTICIPANT => 15,
                self::RANK_FINALIST => 30,
                self::RANK_THIRD => 45,
                self::RANK_SECOND => 60,
                self::RANK_FIRST => 75,
            ],
            self::LEVEL_INTERNATIONAL => [
                self::RANK_PARTICIPANT => 20,
                self::RANK_FINALIST => 40,
                self::RANK_THIRD => 60,
                self::RANK_SECOND => 80,
                self::RANK_FIRST => 100,
            ],
        ];

        return $pointMatrix[$level][$rank] ?? 0;
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

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? 'Lainnya';
    }

    public function getLevelLabelAttribute(): string
    {
        return self::levels()[$this->level] ?? 'Tidak diketahui';
    }

    public function getRankLabelAttribute(): string
    {
        return self::ranks()[$this->rank] ?? 'Tidak diketahui';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? 'Tidak diketahui';
    }

    public function getCertificateUrlAttribute(): ?string
    {
        return $this->certificate_path
            ? Storage::disk('public')->url($this->certificate_path)
            : null;
    }
}
