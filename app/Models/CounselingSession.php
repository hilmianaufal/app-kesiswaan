<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingSession extends Model
{
    use HasFactory;

    public const METHOD_INDIVIDUAL = 'individual';
    public const METHOD_GROUP = 'group';
    public const METHOD_HOME_VISIT = 'home_visit';
    public const METHOD_PHONE = 'phone';
    public const METHOD_ONLINE = 'online';

    public const ATTENDANCE_SCHEDULED = 'scheduled';
    public const ATTENDANCE_COMPLETED = 'completed';
    public const ATTENDANCE_CANCELLED = 'cancelled';
    public const ATTENDANCE_NO_SHOW = 'no_show';

    protected $fillable = [
        'counseling_case_id',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'method',
        'attendance_status',
        'agenda',
        'result',
        'follow_up',
        'next_session_date',
        'is_parent_contacted',
        'parent_response',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'next_session_date' => 'date',
            'is_parent_contacted' => 'boolean',
        ];
    }

    public static function methods(): array
    {
        return [
            self::METHOD_INDIVIDUAL => 'Konseling Individu',
            self::METHOD_GROUP => 'Konseling Kelompok',
            self::METHOD_HOME_VISIT => 'Kunjungan Rumah',
            self::METHOD_PHONE => 'Telepon',
            self::METHOD_ONLINE => 'Konseling Online',
        ];
    }

    public static function attendanceStatuses(): array
    {
        return [
            self::ATTENDANCE_SCHEDULED => 'Dijadwalkan',
            self::ATTENDANCE_COMPLETED => 'Selesai Dilaksanakan',
            self::ATTENDANCE_CANCELLED => 'Dibatalkan',
            self::ATTENDANCE_NO_SHOW => 'Siswa Tidak Hadir',
        ];
    }

    public function counselingCase(): BelongsTo
    {
        return $this->belongsTo(CounselingCase::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    public function getMethodLabelAttribute(): string
    {
        return self::methods()[$this->method]
            ?? 'Tidak diketahui';
    }

    public function getAttendanceStatusLabelAttribute(): string
    {
        return self::attendanceStatuses()[
            $this->attendance_status
        ] ?? 'Tidak diketahui';
    }
}
