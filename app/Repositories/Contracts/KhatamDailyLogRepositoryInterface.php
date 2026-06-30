<?php

namespace App\Repositories\Contracts;

use App\Models\KhatamDailyLog;
use Illuminate\Support\Collection;

interface KhatamDailyLogRepositoryInterface
{
    public function findByProgramAndDate(int $programId, string $date): ?KhatamDailyLog;

    public function upsertByProgramAndDate(int $programId, string $date, array $data): KhatamDailyLog;

    public function create(array $data): KhatamDailyLog;

    /**
     * @return Collection<int, KhatamDailyLog>
     */
    public function getRecentByProgramId(int $programId, int $days = 7): Collection;
}
