<?php

namespace App\Http\Controllers;

use App\Models\QuranLayout;
use App\Repositories\Contracts\KhatamDailyLogRepositoryInterface;
use App\Repositories\Contracts\KhatamHistoryRepositoryInterface;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use App\Repositories\Contracts\KhatamProgressRepositoryInterface;
use App\Repositories\Contracts\PageMappingRepositoryInterface;
use App\Repositories\Contracts\QuranSurahRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressUpdateController extends Controller
{
    public function __construct(
        protected KhatamProgramRepositoryInterface $programRepo,
        protected KhatamProgressRepositoryInterface $progressRepo,
        protected KhatamDailyLogRepositoryInterface $dailyLogRepo,
        protected QuranSurahRepositoryInterface $surahRepo,
        protected PageMappingRepositoryInterface $pageMappingRepo,
        protected KhatamHistoryRepositoryInterface $khatamHistoryRepo,
    ) {}

    public function create(Request $request)
    {
        $user = $request->user();
        $program = $this->programRepo->findActiveByUserId($user->id);
        if (!$program) {
            return redirect()->route('onboarding');
        }

        $progress = $this->progressRepo->findByProgramId($program->id);
        $surahs = $this->surahRepo->getAllOrdered();

        $targetType = $program->target_type;
        $targetValue = (int) $program->target_value;
        $currentPage = $progress?->current_page_in_home_mushaf ?? 1;
        $layoutCode = $user->quran_layout_code ?? 'madinah_15';

        $readFrom = $currentPage;
        $readTo = $targetType === 'daily_pages' ? $currentPage + $targetValue - 1 : $currentPage;
        $totalPages = $targetValue;

        // Suggested surah/verse (for daily_pages)
        $suggestedSurahId = null;
        $suggestedVerseNumber = null;
        if ($targetType !== 'daily_verses') {
            $pageMapping = $this->pageMappingRepo->findLastVerseByPageAndLayout($readTo, $layoutCode);
            if ($pageMapping && $pageMapping->verse) {
                $suggestedSurahId = $pageMapping->verse->surah_id;
                $suggestedVerseNumber = $pageMapping->verse->verse_number;
            }
        }

        // For daily_verses: compute suggested endpoint = current position + targetValue ayat
        $suggestedAyatSurahId = null;
        $suggestedAyatVerseNumber = null;
        if ($targetType === 'daily_verses') {
            $startSurahId = $progress?->last_surah_id ?? 1;
            $startVerse = $progress?->last_verse_number ?? 1;
            $end = $this->resolveVerseOffset($startSurahId, $startVerse, $targetValue);
            if ($end) {
                $suggestedAyatSurahId = $end[0];
                $suggestedAyatVerseNumber = $end[1];
            }
        }

        // Pages detail only for page-based
        $pagesDetail = $targetType !== 'daily_verses'
            ? $this->pageMappingRepo->getPagesDetail($readFrom, $readTo, $layoutCode)
            : [];

        $layout = QuranLayout::find($layoutCode);

        return Inertia::render('ProgressUpdate', [
            'surahs' => $surahs->map(fn ($s) => [
                'id' => $s->id,
                'name_id' => $s->name_id,
                'total_verses' => $s->total_verses,
            ]),
            'currentProgress' => $progress ? [
                'last_surah_id' => $progress->last_surah_id,
                'last_verse_number' => $progress->last_verse_number,
                'current_page_in_home_mushaf' => $progress->current_page_in_home_mushaf,
            ] : null,
            'suggestedReadFrom'     => $readFrom,
            'suggestedReadTo'       => $readTo,
            'targetPages'           => $totalPages,
            'suggestedSurahId'      => $suggestedSurahId,
            'suggestedVerseNumber'  => $suggestedVerseNumber,
            'suggestedAyatSurahId'  => $suggestedAyatSurahId,
            'suggestedAyatVerseNumber' => $suggestedAyatVerseNumber,
            'pagesDetail'           => $pagesDetail,
            'activeLayout'          => $layout ? [
                'code' => $layout->code,
                'name' => $layout->name,
            ] : ['code' => $layoutCode, 'name' => $layoutCode],
            'targetType'            => $targetType,
            'targetValue'           => $targetValue,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $program = $this->programRepo->findActiveByUserId($user->id);
        if (!$program) {
            return redirect()->route('onboarding');
        }

        $layoutCode = $user->quran_layout_code ?? 'madinah_15';

        if ($program->target_type === 'daily_verses') {
            $validated = $request->validate([
                'surah_id' => 'required|integer|exists:quran_surahs,id',
                'verse_number' => 'required|integer|min:1',
            ]);

            $newSurahId = (int) $validated['surah_id'];
            $newVerse = (int) $validated['verse_number'];

            // Resolve page number from surah+verse
            $newPage = $this->resolvePageNumber($layoutCode, $newSurahId, $newVerse);
        } else {
            $validated = $request->validate([
                'current_page_in_home_mushaf' => 'required|integer|min:1',
            ]);

            $newPage = (int) $validated['current_page_in_home_mushaf'];

            // Resolve surah & ayat from page
            $pageMapping = $this->pageMappingRepo->findLastVerseByPageAndLayout($newPage, $layoutCode);
            $newSurahId = $pageMapping?->verse?->surah_id ?? 1;
            $newVerse = $pageMapping?->verse?->verse_number ?? 1;
        }

        // Daily log (accumulate)
        $today = Carbon::today()->toDateString();
        $existingLog = $this->dailyLogRepo->findByProgramAndDate($program->id, $today);

        if ($existingLog && $newPage > $existingLog->page_to) {
            $increment = $newPage - $existingLog->page_to;
            $existingLog->update([
                'page_to'     => $newPage,
                'pages_read'  => $existingLog->pages_read + $increment,
                'surah_id_to' => $newSurahId,
                'verse_to'    => $newVerse,
            ]);
        } elseif (!$existingLog) {
            $prevProgress = $this->progressRepo->findPreviousByProgramId($program->id);
            $pageFrom = $prevProgress?->current_page_in_home_mushaf ?? 1;
            $pagesRead = max(1, $newPage - $pageFrom + 1);

            $this->dailyLogRepo->create([
                'khatam_program_id' => $program->id,
                'log_date'          => $today,
                'page_from'         => $pageFrom,
                'page_to'           => $newPage,
                'pages_read'        => $pagesRead,
                'surah_id_from'     => $prevProgress?->last_surah_id,
                'verse_from'        => $prevProgress?->last_verse_number,
                'surah_id_to'       => $newSurahId,
                'verse_to'          => $newVerse,
            ]);
        }

        // Update progress utama
        $this->progressRepo->updateOrCreateByProgramId($program->id, [
            'last_surah_id'              => $newSurahId,
            'last_verse_number'          => $newVerse,
            'current_page_in_home_mushaf' => $newPage,
        ]);

        // Auto-khatam jika mencapai total page
        $layout = $user->quranLayout;
        $totalPages = $layout?->total_pages ?? 604;
        if ($newPage >= $totalPages) {
            $this->progressRepo->updateOrCreateByProgramId($program->id, [
                'last_surah_id'              => 1,
                'last_verse_number'          => 1,
                'current_page_in_home_mushaf' => 1,
            ]);
            $user->refresh();
            $layout = $user->quranLayout;
            $this->khatamHistoryRepo->create([
                'user_id'           => $user->id,
                'quran_layout_code' => $layout?->code ?? 'madinah_15',
                'total_pages_read'  => $layout?->total_pages ?? 604,
                'completed_at'      => now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Progress berhasil diperbarui!');
    }

    protected function resolvePageNumber(string $layoutCode, int $surahId, int $verseNumber): int
    {
        $verse = \App\Models\QuranVerse::query()
            ->where('surah_id', $surahId)
            ->where('verse_number', $verseNumber)
            ->first();

        if (!$verse) {
            return 1;
        }

        $mapping = $this->pageMappingRepo->findByVerseAndLayout($verse->id, $layoutCode);

        return $mapping?->page_number ?? 1;
    }

    /**
     * Compute (surah_id, verse_number) after adding N verses from a starting position.
     * Crosses surah boundaries using known verse counts.
     *
     * @return array{int, int}|null [surah_id, verse_number]
     */
    protected function resolveVerseOffset(int $surahId, int $verseNumber, int $offset): ?array
    {
        $verseCounts = \Database\Seeders\QuranVerseSeeder::VERSE_COUNTS;

        if ($offset <= 0) {
            return [$surahId, $verseNumber];
        }

        $currentSurah = $surahId;
        $remaining = $offset;
        $currentVerse = $verseNumber;

        while ($remaining > 0 && $currentSurah <= 114) {
            $surahTotal = $verseCounts[$currentSurah] ?? 0;
            $available = $surahTotal - $currentVerse + 1;

            if ($remaining <= $available) {
                return [$currentSurah, $currentVerse + $remaining - 1];
            }

            // Skip to next surah
            $remaining -= $available;
            $currentSurah++;
            $currentVerse = 1;
        }

        // Beyond last surah → cap at An-Nas:6
        return [114, 6];
    }
}
