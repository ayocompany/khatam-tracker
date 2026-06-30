<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $verse_id
 * @property string $layout_type
 * @property int $page_number
 */
class PageMapping extends Model
{
    protected $table = 'page_mappings';

    protected $fillable = [
        'verse_id',
        'layout_type',
        'page_number',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'verse_id' => 'integer',
            'page_number' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<QuranVerse, $this>
     */
    public function verse(): BelongsTo
    {
        return $this->belongsTo(QuranVerse::class, 'verse_id', 'id');
    }

    #[Scope]
    protected function byLayout(Builder $query, string $layoutType): void
    {
        $query->where('layout_type', $layoutType);
    }
}
