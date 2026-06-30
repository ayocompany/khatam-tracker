<?php


namespace App\Repositories\Contracts;

use App\Models\PageMapping;

interface PageMappingRepositoryInterface
{
    public function findByVerseAndLayout(int $verseId, string $layoutType): ?PageMapping;
    public function findFirstVerseByPageAndLayout(int $page, string $layoutType): ?PageMapping;
    public function findLastVerseByPageAndLayout(int $page, string $layoutType): ?PageMapping;
    public function getVerseIdsByPageAndLayout(int $page, string $layoutType): array;
    public function getPagesDetail(int $fromPage, int $toPage, string $layoutType): array;
}
