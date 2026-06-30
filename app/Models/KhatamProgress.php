<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $khatam_program_id
 * @property int $last_surah_id
 * @property int $last_verse_number
 * @property int $current_page_in_home_mushaf
 */
class KhatamProgress extends Model
{
    protected $table = 'khatam_progress';

    protected $fillable = [
        'khatam_program_id',
        'last_surah_id',
        'last_verse_number',
        'current_page_in_home_mushaf',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'khatam_program_id' => 'integer',
            'last_surah_id' => 'integer',
            'last_verse_number' => 'integer',
            'current_page_in_home_mushaf' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<KhatamProgram, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(KhatamProgram::class, 'khatam_program_id', 'id');
    }

    /**
     * @return BelongsTo<QuranSurah, $this>
     */
    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'last_surah_id', 'id');
    }
}
