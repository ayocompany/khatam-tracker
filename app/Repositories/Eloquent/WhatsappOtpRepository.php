<?php


namespace App\Repositories\Eloquent;

use App\Models\WhatsappOtp;
use App\Repositories\Contracts\WhatsappOtpRepositoryInterface;
use Carbon\CarbonImmutable;

final class WhatsappOtpRepository implements WhatsappOtpRepositoryInterface
{
    public function create(array $data): WhatsappOtp
    {
        return WhatsappOtp::query()->create($data);
    }

    public function findLatestActiveByPhone(string $phone): ?WhatsappOtp
    {
        return WhatsappOtp::query()
            ->activeByPhone($phone)
            ->latest('id')
            ->first();
    }

    public function incrementAttempt(WhatsappOtp $otp): bool
    {
        return $otp->update([
            'attempt_count' => $otp->attempt_count + 1,
        ]);
    }

    public function markVerified(WhatsappOtp $otp): bool
    {
        return $otp->update([
            'verified_at' => CarbonImmutable::now(),
        ]);
    }
}
