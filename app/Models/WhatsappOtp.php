<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $phone
 * @property string $otp_code_hash
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int $attempt_count
 * @property string|null $provider_message_id
 */
class WhatsappOtp extends Model
{
    protected $table = 'whatsapp_otps';

    protected $fillable = [
        'phone',
        'otp_code_hash',
        'expires_at',
        'verified_at',
        'attempt_count',
        'provider_message_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempt_count' => 'integer',
        ];
    }

    #[Scope]
    protected function activeByPhone(Builder $query, string $phone): void
    {
        $query
            ->where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id');
    }
}
