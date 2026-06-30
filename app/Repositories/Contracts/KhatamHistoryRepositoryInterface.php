<?php

namespace App\Repositories\Contracts;

interface KhatamHistoryRepositoryInterface
{
    public function countByUser(int $userId): int;

    public function create(array $data): mixed;
}
