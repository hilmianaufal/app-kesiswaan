<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationType extends Model
{
    use HasFactory;

    public const CATEGORY_LIGHT = 'light';
    public const CATEGORY_MEDIUM = 'medium';
    public const CATEGORY_HEAVY = 'heavy';

    protected $fillable = [
        'name',
        'category',
        'points',
        'default_action',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_LIGHT => 'Ringan',
            self::CATEGORY_MEDIUM => 'Sedang',
            self::CATEGORY_HEAVY => 'Berat',
        ];
    }

    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? 'Tidak diketahui';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}
