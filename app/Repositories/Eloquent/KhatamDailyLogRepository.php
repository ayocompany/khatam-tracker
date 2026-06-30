<?php

namespace App\Repositories\Eloquent;

use App\Models\KhatamDailyLog;
use App\Repositories\Contracts\KhatamDailyLogRepositoryInterface;
use Illuminate\Support\Collection;

class KhatamDailyLogRepository implements KhatamDailyLogRepositoryInterface
{
    public function findByProgramAndDate(int $programId, string $date): ?KhatamDailyLog
    {
        return KhatamDailyLog::query()
            ->where('khatam_program_id', $programId)
            ->where('log_date', $date)
            ->first();
    }

    public function upsertByProgramAndDate(int $programId, string $date, array $data): KhatamDailyLog
    {
        $data['khatam_program_id'] = $programId;
        $data['log_date'] = $date;

        return KhatamDailyLog::query()->updateOrCreate(
            ['khatam_program_id' => $programId, 'log_date' => $date],
            $data,
        );
    }

    public function create(array $data): KhatamDailyLog
    {
        return KhatamDailyLog::query()->create($data);
    }

    public function getRecentByProgramId(int $programId, int $days = 7): Collection
    {
        return KhatamDailyLog::query()
            ->where('khatam_program_id', $programId)
            ->orderBy('log_date', 'desc')
            ->limit($days)
            ->get()
            ->sortBy('log_date')
            ->values();
    }
}
