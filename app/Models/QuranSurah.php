<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name_ar
 * @property string $name_id
 * @property int $total_verses
 * @property string $type
 */
class QuranSurah extends Model
{
    protected $table = 'quran_surahs';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name_ar',
        'name_id',
        'total_verses',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'total_verses' => 'integer',
        ];
    }

    /**
     * @return HasMany<QuranVerse, $this>
     */
    public function verses(): HasMany
    {
        return $this->hasMany(QuranVerse::class, 'surah_id', 'id');
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('id');
    }
}
