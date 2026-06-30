<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $quran_layout_code
 * @property int $total_pages_read
 * @property string $completed_at
 */
class KhatamHistory extends Model
{
    protected $table = 'khatam_histories';

    protected $fillable = [
        'user_id',
        'quran_layout_code',
        'total_pages_read',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'total_pages_read' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
