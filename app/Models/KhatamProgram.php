<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $target_type
 * @property int $target_value
 * @property string $status
 */
class KhatamProgram extends Model
{
    protected $table = 'khatam_programs';

    protected $fillable = [
        'user_id',
        'title',
        'target_type',
        'target_value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'target_value' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<KhatamProgress, $this>
     */
    public function progress(): HasOne
    {
        return $this->hasOne(KhatamProgress::class, 'khatam_program_id', 'id');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', 'active');
    }
}
