<?php


namespace App\Repositories\Eloquent;

use App\Models\PageMapping;
use App\Repositories\Contracts\PageMappingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final class PageMappingRepository implements PageMappingRepositoryInterface
{
    private function cacheKey(string $layoutType): string
    {
        return "page_mappings_v2:{$layoutType}";
    }

    /**
     * Load all page mappings for a layout into memory (cached as plain arrays, avoids serialization issues).
     *
     * @return array<int, array{id:int,page_number:int,verse_id:int,surah_id:int,surah_name:string,verse_number:int}>
     */
    private function loadLayout(string $layoutType): array
    {
        $key = $this->cacheKey($layoutType);

        return Cache::rememberForever($key, function () use ($layoutType) {
            return PageMapping::select('page_mappings.id', 'page_mappings.page_number', 'page_mappings.verse_id', 'qv.surah_id', 'qv.verse_number', 'qs.name_id as surah_name')
                ->join('quran_verses as qv', 'qv.id', '=', 'page_mappings.verse_id')
                ->join('quran_surahs as qs', 'qs.id', '=', 'qv.surah_id')
                ->where('page_mappings.layout_type', $layoutType)
                ->orderBy('page_mappings.page_number')
                ->orderBy('page_mappings.verse_id')
                ->get()
                ->toArray();
        });
    }

    public function findByVerseAndLayout(int $verseId, string $layoutType): ?PageMapping
    {
        $row = collect($this->loadLayout($layoutType))->firstWhere('verse_id', $verseId);
        if (!$row) {
            return null;
        }
        // Return a fresh model instance hydrated from the cached row
        $model = new PageMapping();
        $model->forceFill([
            'id' => $row['id'],
            'page_number' => $row['page_number'],
            'verse_id' => $row['verse_id'],
        ]);
        $model->setRelation('verse', new \App\Models\QuranVerse([
            'id' => $row['verse_id'],
            'surah_id' => $row['surah_id'],
            'verse_number' => $row['verse_number'],
        ]));
        return $model;
    }

    public function getVerseIdsByPageAndLayout(int $page, string $layoutType): array
    {
        return collect($this->loadLayout($layoutType))
            ->where('page_number', $page)
            ->pluck('verse_id')
            ->toArray();
    }

    public function getPagesDetail(int $fromPage, int $toPage, string $layoutType): array
    {
        $rows = collect($this->loadLayout($layoutType))
            ->where('page_number', '>=', $fromPage)
            ->where('page_number', '<=', $toPage)
            ->groupBy('page_number');

        $result = [];

        foreach ($rows as $page => $pageMappings) {
            $first = $pageMappings->first();
            $last = $pageMappings->last();

            $sameSurah = $first['surah_id'] === $last['surah_id'];

            $result[] = [
                'page'           => (int) $page,
                'surah_name'     => $first['surah_name'],
                'surah_id'       => $first['surah_id'],
                'from_ayah'      => $first['verse_number'],
                'to_ayah'        => $sameSurah ? $last['verse_number'] : null,
                'to_surah_name'  => $sameSurah ? null : $last['surah_name'],
                'to_ayah_end'    => $sameSurah ? null : $last['verse_number'],
                'verse_count'    => $pageMappings->count(),
            ];
        }

        return $result;
    }

    public function findLastVerseByPageAndLayout(int $page, string $layoutType): ?PageMapping
    {
        $rows = collect($this->loadLayout($layoutType))->where('page_number', $page);
        if ($rows->isEmpty()) {
            return null;
        }
        $row = $rows->last();
        $model = new PageMapping();
        $model->forceFill([
            'id' => $row['id'],
            'page_number' => $row['page_number'],
            'verse_id' => $row['verse_id'],
        ]);
        $model->setRelation('verse', new \App\Models\QuranVerse([
            'id' => $row['verse_id'],
            'surah_id' => $row['surah_id'],
            'verse_number' => $row['verse_number'],
        ]));
        return $model;
    }

    public function findFirstVerseByPageAndLayout(int $page, string $layoutType): ?PageMapping
    {
        $rows = collect($this->loadLayout($layoutType))->where('page_number', $page);
        if ($rows->isEmpty()) {
            return null;
        }
        $row = $rows->first();
        $model = new PageMapping();
        $model->forceFill([
            'id' => $row['id'],
            'page_number' => $row['page_number'],
            'verse_id' => $row['verse_id'],
        ]);
        $model->setRelation('verse', new \App\Models\QuranVerse([
            'id' => $row['verse_id'],
            'surah_id' => $row['surah_id'],
            'verse_number' => $row['verse_number'],
        ]));
        return $model;
    }
}
