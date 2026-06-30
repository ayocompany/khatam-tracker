<?php

namespace App\Repositories\Eloquent;

use App\Models\KhatamHistory;
use App\Repositories\Contracts\KhatamHistoryRepositoryInterface;

final class KhatamHistoryRepository implements KhatamHistoryRepositoryInterface
{
    public function countByUser(int $userId): int
    {
        return KhatamHistory::where('user_id', $userId)->count();
    }

    public function create(array $data): KhatamHistory
    {
        return KhatamHistory::create($data);
    }
}
