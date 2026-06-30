<?php


namespace App\Repositories\Contracts;

use App\Models\QuranVerse;

interface QuranVerseRepositoryInterface
{
    public function findBySurahAndVerseNumber(int $surahId, int $verseNumber): ?QuranVerse;

    public function countAll(): int;
}
