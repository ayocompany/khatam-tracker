<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code
 * @property string $name
 * @property int $total_pages
 * @property int $total_surahs
 * @property int $total_verses
 * @property int $lines_per_page
 * @property bool $is_active
 */
class QuranLayout extends Model
{
    /** @use HasFactory<\Database\Factories\QuranLayoutFactory> */
    use HasFactory;

    protected $table = 'quran_layouts';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'total_pages',
        'total_surahs',
        'total_verses',
        'lines_per_page',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_pages' => 'integer',
            'total_surahs' => 'integer',
            'total_verses' => 'integer',
            'lines_per_page' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'quran_layout_code', 'code');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
