<?php


namespace App\Repositories\Eloquent;

use App\Models\KhatamProgress;
use App\Repositories\Contracts\KhatamProgressRepositoryInterface;

final class KhatamProgressRepository implements KhatamProgressRepositoryInterface
{
    public function findByProgramId(int $programId): ?KhatamProgress
    {
        return KhatamProgress::query()
            ->where('khatam_program_id', $programId)
            ->first();
    }

    public function findPreviousByProgramId(int $programId): ?KhatamProgress
    {
        return KhatamProgress::query()
            ->where('khatam_program_id', $programId)
            ->orderByDesc('updated_at')
            ->first();
    }

    public function updateOrCreateByProgramId(int $programId, array $data): KhatamProgress
    {
        /** @var KhatamProgress $progress */
        $progress = KhatamProgress::query()->updateOrCreate(
            ['khatam_program_id' => $programId],
            $data,
        );

        return $progress;
    }
}
