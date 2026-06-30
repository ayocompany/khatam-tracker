<?php


namespace App\Repositories\Eloquent;

use App\Models\QuranVerse;
use App\Repositories\Contracts\QuranVerseRepositoryInterface;

final class QuranVerseRepository implements QuranVerseRepositoryInterface
{
    public function findBySurahAndVerseNumber(int $surahId, int $verseNumber): ?QuranVerse
    {
        return QuranVerse::query()
            ->where('surah_id', $surahId)
            ->where('verse_number', $verseNumber)
            ->first();
    }

    public function countAll(): int
    {
        return QuranVerse::query()->count();
    }
}
