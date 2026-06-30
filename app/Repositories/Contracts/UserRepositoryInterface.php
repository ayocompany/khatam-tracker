<?php


namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByPhone(string $phone): ?User;

    public function createWithPhone(string $phone, ?string $name = null): User;

    public function markPhoneVerified(User $user): bool;
}
