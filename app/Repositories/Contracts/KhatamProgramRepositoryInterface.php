<?php


namespace App\Repositories\Contracts;

use App\Models\KhatamProgram;

interface KhatamProgramRepositoryInterface
{
    public function findActiveByUserId(int $userId): ?KhatamProgram;

    public function create(array $data): KhatamProgram;

    public function deleteByUserId(int $userId): void;
}
