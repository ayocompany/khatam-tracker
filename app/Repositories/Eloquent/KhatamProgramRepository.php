<?php


namespace App\Repositories\Eloquent;

use App\Models\KhatamProgram;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;

final class KhatamProgramRepository implements KhatamProgramRepositoryInterface
{
    public function findActiveByUserId(int $userId): ?KhatamProgram
    {
        return KhatamProgram::query()
            ->where('user_id', $userId)
            ->active()
            ->latest('id')
            ->first();
    }

    public function create(array $data): KhatamProgram
    {
        return KhatamProgram::query()->create($data);
    }

    public function deleteByUserId(int $userId): void
    {
        KhatamProgram::query()
            ->where('user_id', $userId)
            ->delete();
    }
}
