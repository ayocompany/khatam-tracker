<?php


namespace App\Repositories\Eloquent;

use App\Models\QuranSurah;
use App\Repositories\Contracts\QuranSurahRepositoryInterface;
use Illuminate\Support\Collection;

final class QuranSurahRepository implements QuranSurahRepositoryInterface
{
    public function getAllOrdered(): Collection
    {
        return QuranSurah::query()
            ->ordered()
            ->get();
    }

    public function findById(int $id): ?QuranSurah
    {
        return QuranSurah::query()->find($id);
    }
}
