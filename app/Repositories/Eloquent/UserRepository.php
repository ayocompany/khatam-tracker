<?php


namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final class UserRepository implements UserRepositoryInterface
{
    public function findByPhone(string $phone): ?User
    {
        return User::query()
            ->where('phone', $phone)
            ->first();
    }

    public function createWithPhone(string $phone, ?string $name = null): User
    {
        return User::query()->create([
            'name' => $name ?? 'User '.Str::random(6),
            'phone' => $phone,
            'password' => Str::password(32, true, true, true, false),
        ]);
    }

    public function markPhoneVerified(User $user): bool
    {
        return $user->update([
            'phone_verified_at' => CarbonImmutable::now(),
        ]);
    }
}
