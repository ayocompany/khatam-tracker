<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'phone_verified_at', 'quran_layout_code', 'reminder_times', 'wa_reminder_enabled', 'wa_phone', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'reminder_times' => 'array',
            'wa_reminder_enabled' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<QuranLayout, $this>
     */
    public function quranLayout(): BelongsTo
    {
        return $this->belongsTo(QuranLayout::class, 'quran_layout_code', 'code');
    }

    /**
     * @return HasMany<KhatamHistory, $this>
     */
    public function khatamHistories(): HasMany
    {
        return $this->hasMany(KhatamHistory::class, 'user_id');
    }
}
