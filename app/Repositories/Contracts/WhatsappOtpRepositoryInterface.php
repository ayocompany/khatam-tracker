<?php


namespace App\Repositories\Contracts;

use App\Models\WhatsappOtp;

interface WhatsappOtpRepositoryInterface
{
    public function create(array $data): WhatsappOtp;

    public function findLatestActiveByPhone(string $phone): ?WhatsappOtp;

    public function incrementAttempt(WhatsappOtp $otp): bool;

    public function markVerified(WhatsappOtp $otp): bool;
}
