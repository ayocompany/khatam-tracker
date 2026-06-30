<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $surah_id
 * @property int $verse_number
 * @property string $text_ar
 * @property string|null $text_id
 */
class QuranVerse extends Model
{
    protected $table = 'quran_verses';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'surah_id',
        'verse_number',
        'text_ar',
        'text_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'surah_id' => 'integer',
            'verse_number' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<QuranSurah, $this>
     */
    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'surah_id', 'id');
    }

    /**
     * @return HasMany<PageMapping, $this>
     */
    public function pageMappings(): HasMany
    {
        return $this->hasMany(PageMapping::class, 'verse_id', 'id');
    }

    #[Scope]
    protected function byPosition(Builder $query, int $surahId, int $verseNumber): void
    {
        $query
            ->where('surah_id', $surahId)
            ->where('verse_number', $verseNumber);
    }
}
