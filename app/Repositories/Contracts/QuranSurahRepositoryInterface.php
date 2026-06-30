<?php


namespace App\Repositories\Contracts;

use App\Models\QuranSurah;
use Illuminate\Support\Collection;

interface QuranSurahRepositoryInterface
{
    /**
     * @return Collection<int, QuranSurah>
     */
    public function getAllOrdered(): Collection;

    public function findById(int $id): ?QuranSurah;
}
