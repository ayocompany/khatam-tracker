<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $khatam_program_id
 * @property string $log_date
 * @property int $page_from
 * @property int $page_to
 * @property int $pages_read
 * @property int|null $surah_id_from
 * @property int|null $verse_from
 * @property int|null $surah_id_to
 * @property int|null $verse_to
 */
class KhatamDailyLog extends Model
{
    protected $table = 'khatam_daily_logs';

    protected $fillable = [
        'khatam_program_id',
        'log_date',
        'page_from',
        'page_to',
        'pages_read',
        'surah_id_from',
        'verse_from',
        'surah_id_to',
        'verse_to',
    ];

    protected function casts(): array
    {
        return [
            'id'                => 'integer',
            'khatam_program_id' => 'integer',
            'page_from'         => 'integer',
            'page_to'           => 'integer',
            'pages_read'        => 'integer',
            'surah_id_from'     => 'integer',
            'verse_from'        => 'integer',
            'surah_id_to'       => 'integer',
            'verse_to'          => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(KhatamProgram::class, 'khatam_program_id', 'id');
    }
}
