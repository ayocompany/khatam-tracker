<?php

namespace App\Http\Controllers;

use App\Models\QuranLayout;
use App\Repositories\Contracts\KhatamDailyLogRepositoryInterface;
use App\Repositories\Contracts\KhatamHistoryRepositoryInterface;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use App\Repositories\Contracts\KhatamProgressRepositoryInterface;
use App\Repositories\Contracts\PageMappingRepositoryInterface;
use App\Repositories\Contracts\QuranSurahRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected KhatamProgramRepositoryInterface $programRepo,
        protected KhatamProgressRepositoryInterface $progressRepo,
        protected QuranSurahRepositoryInterface $surahRepo,
        protected KhatamDailyLogRepositoryInterface $dailyLogRepo,
        protected PageMappingRepositoryInterface $pageMappingRepo,
        protected KhatamHistoryRepositoryInterface $khatamHistoryRepo,
    ) {}

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Cek apakah user punya program aktif
        $activeProgram = $this->programRepo->findActiveByUserId($user->id);

        if (!$activeProgram) {
            return redirect()->route('onboarding');
        }

        $progress = $this->progressRepo->findByProgramId($activeProgram->id);

        $currentLayout = $user->quranLayout;

        $totalPages = $currentLayout?->total_pages ?? 604;
        $currentPage = $progress?->current_page_in_home_mushaf ?? 1;
        $completionPercent = $totalPages > 0
            ? max(0, min(100, (int) round(($currentPage / $totalPages) * 100)))
            : 0;

        $remainingPages = $totalPages - $currentPage;
        $remainingPages = max(0, $remainingPages);

        // Nama surah terakhir
        $lastSurah = $progress
            ? $this->surahRepo->findById($progress->last_surah_id)
            : null;

        $lastPosition = $lastSurah && $progress
            ? $lastSurah->name_id . ': ' . $progress->last_verse_number
            : 'Al-Fatihah: 1';

        $targetType = $activeProgram->target_type;
        $targetValue = (int) $activeProgram->target_value;

        // Rentang bacaan hari ini
        $targetPages = $targetType === 'daily_pages' ? $targetValue : 0;
        $readFrom = $currentPage;
        $readTo = $targetPages > 0 ? $currentPage + $targetPages - 1 : $currentPage;

        // Untuk daily_verses: compute suggested endpoint
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

        // Resolve surah/ayat start & end dari page mapping
        $layoutCode = $user->quran_layout_code ?? 'home_mushaf';
        $startMapping = $this->pageMappingRepo->findFirstVerseByPageAndLayout($readFrom, $layoutCode);
        $endMapping = $this->pageMappingRepo->findLastVerseByPageAndLayout($readTo, $layoutCode);

        if ($startMapping && !$startMapping->relationLoaded('verse')) {
            $startMapping->load('verse');
        }
        if ($endMapping && !$endMapping->relationLoaded('verse')) {
            $endMapping->load('verse');
        }

        $startSurah = $startMapping?->verse
            ? $this->surahRepo->findById($startMapping->verse->surah_id)
            : null;
        $endSurah = $endMapping?->verse
            ? $this->surahRepo->findById($endMapping->verse->surah_id)
            : null;

        // Data untuk card terstruktur (surah, ayat, halaman)
        $todayReadSurah = null;
        $todayReadFromAyah = null;
        $todayReadToAyah = null;
        $todayReadFromPage = $readFrom;
        $todayReadToPage = $readTo;

        if ($startMapping && $startMapping->verse) {
            $startName = $startSurah?->name_id ?? '—';
            $startAyah = $startMapping->verse->verse_number;
            $endName = $endSurah?->name_id ?? $startName;
            $endAyah = $endMapping?->verse?->verse_number ?? $startAyah;

            $todayReadSurah = ($startName === $endName) ? $startName : "{$startName} → {$endName}";
            $todayReadFromAyah = $startAyah;
            $todayReadToAyah = $endAyah;
        }

        // Target harian
        $dailyTarget = (int) $activeProgram->target_value;
        $dailyTargetUnit = $targetType === 'daily_verses' ? 'ayat' : 'halaman';

        // Estimate khatam completion date
        $estimatedCompletionDate = null;
        if ($dailyTarget > 0) {
            if ($targetType === 'daily_pages') {
                $daysNeeded = (int) ceil($remainingPages / $dailyTarget);
                $estimatedCompletionDate = now('Asia/Jakarta')->addDays($daysNeeded)->format('d M Y');
            } elseif ($targetType === 'daily_verses') {
                $verseCounts = \Database\Seeders\QuranVerseSeeder::VERSE_COUNTS;
                $totalVerses = array_sum($verseCounts);
                $currentVerseIndex = 0;
                $lastSurahId = $progress?->last_surah_id ?? 1;
                $lastVerseNumber = $progress?->last_verse_number ?? 1;
                for ($s = 1; $s < $lastSurahId; $s++) {
                    $currentVerseIndex += $verseCounts[$s] ?? 0;
                }
                $currentVerseIndex += $lastVerseNumber;
                $remainingVerses = max(0, $totalVerses - $currentVerseIndex);
                $daysNeeded = (int) ceil($remainingVerses / $dailyTarget);
                $estimatedCompletionDate = now('Asia/Jakarta')->addDays($daysNeeded)->format('d M Y');
            }
        }

        // Today's read
        $today = now('Asia/Jakarta')->format('Y-m-d');
        $todayLog = $this->dailyLogRepo->findByProgramAndDate($activeProgram->id, $today);
        $todayRead = 0;
        if ($todayLog) {
            if ($targetType === 'daily_verses' && $todayLog->surah_id_from && $todayLog->surah_id_to) {
                $verseCounts = \Database\Seeders\QuranVerseSeeder::VERSE_COUNTS;
                $fromSurah = $todayLog->surah_id_from;
                $fromVerse = $todayLog->verse_from;
                $toSurah = $todayLog->surah_id_to;
                $toVerse = $todayLog->verse_to;
                $total = 0;
                if ($fromSurah === $toSurah) {
                    $total = $toVerse - $fromVerse + 1;
                } else {
                    $total += ($verseCounts[$fromSurah] ?? 0) - $fromVerse + 1;
                    for ($s = $fromSurah + 1; $s < $toSurah; $s++) {
                        $total += $verseCounts[$s] ?? 0;
                    }
                    $total += $toVerse;
                }
                $todayRead = $total;
            } else {
                $todayRead = $todayLog->pages_read;
            }
        }

        // Weekly chart (last 7 days)
        $weeklyLogs = $this->dailyLogRepo->getRecentByProgramId($activeProgram->id, 7);
        $weeklyChart = $weeklyLogs->map(function ($l) use ($targetType) {
            $value = $l->pages_read;
            if ($targetType === 'daily_verses' && $l->surah_id_from && $l->surah_id_to) {
                $verseCounts = \Database\Seeders\QuranVerseSeeder::VERSE_COUNTS;
                $fromSurah = $l->surah_id_from;
                $fromVerse = $l->verse_from;
                $toSurah = $l->surah_id_to;
                $toVerse = $l->verse_to;
                $total = 0;
                if ($fromSurah === $toSurah) {
                    $total = $toVerse - $fromVerse + 1;
                } else {
                    $total += ($verseCounts[$fromSurah] ?? 0) - $fromVerse + 1;
                    for ($s = $fromSurah + 1; $s < $toSurah; $s++) {
                        $total += $verseCounts[$s] ?? 0;
                    }
                    $total += $toVerse;
                }
                $value = $total;
            }
            return [
                'date'       => $l->log_date,
                'pages_read' => $value,
            ];
        })->values();

        $surahs = $this->surahRepo->getAllOrdered();

        return Inertia::render('Dashboard', [
            'surahs'            => $surahs,
            'program'           => [
                'title'        => $activeProgram->title,
                'target_type'  => $activeProgram->target_type,
                'target_value' => $activeProgram->target_value,
                'status'       => $activeProgram->status,
            ],
            'progress'          => [
                'current_page'             => $currentPage,
                'total_pages'              => $totalPages,
                'completion_percent'       => $completionPercent,
                'remaining_pages'          => $remainingPages,
                'last_surah_id'            => $progress?->last_surah_id ?? 1,
                'last_verse_number'        => $progress?->last_verse_number ?? 1,
                'last_position_label'      => $lastPosition,
            ],
            'dailyTarget'       => $dailyTarget,
            'dailyTargetUnit'   => $dailyTargetUnit,
            'suggestedReadFrom' => (int) $readFrom,
            'suggestedReadTo'   => (int) $readTo,
            'targetPages'        => (int) $targetPages,
            'todayReadSurah'     => $todayReadSurah,
            'todayReadFromAyah'  => $todayReadFromAyah,
            'todayReadToAyah'    => $todayReadToAyah,
            'todayReadFromPage'  => $todayReadFromPage,
            'todayReadToPage'    => $todayReadToPage,
            'todayPagesDetail'   => $this->pageMappingRepo->getPagesDetail($todayReadFromPage, $todayReadToPage, $layoutCode),
            'todayRead'              => $todayRead,
            'weeklyChart'            => $weeklyChart,
            'totalKhatam'            => $this->khatamHistoryRepo->countByUser($user->id),
            'suggestedAyatSurahId'   => $suggestedAyatSurahId,
            'suggestedAyatVerseNumber' => $suggestedAyatVerseNumber,
            'estimatedCompletionDate'  => $estimatedCompletionDate,
        ]);
    }

    /**
     * Compute (surah_id, verse_number) after adding N verses from a starting position.
     *
     * @return array{int, int}|null
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

            $remaining -= $available;
            $currentSurah++;
            $currentVerse = 1;
        }

        return [114, 6];
    }
}
