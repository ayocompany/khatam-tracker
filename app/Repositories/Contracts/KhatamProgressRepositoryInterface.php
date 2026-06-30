<?php


namespace App\Repositories\Contracts;

use App\Models\KhatamProgress;

interface KhatamProgressRepositoryInterface
{
    public function findByProgramId(int $programId): ?KhatamProgress;

    public function findPreviousByProgramId(int $programId): ?KhatamProgress;

    public function updateOrCreateByProgramId(int $programId, array $data): KhatamProgress;
}
