<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use App\Repositories\Contracts\KhatamProgressRepositoryInterface;
use App\Repositories\Contracts\QuranSurahRepositoryInterface;
use App\Repositories\Contracts\PageMappingRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\MessageBag;

class OnboardingController extends Controller
{
    public function __construct(
        protected QuranSurahRepositoryInterface $surahRepo,
        protected KhatamProgramRepositoryInterface $programRepo,
        protected KhatamProgressRepositoryInterface $progressRepo,
        protected PageMappingRepositoryInterface $pageMappingRepo,
    ) {}

    public function create(Request $request)
    {
        // Guard: redirect jika sudah punya program aktif
        $user = $request->user();
        if ($this->programRepo->findActiveByUserId($user->id)) {
            return redirect()->route('dashboard');
        }

        $surahs = $this->surahRepo->getAllOrdered();

        \Inertia\Inertia::share('errors', new MessageBag);

        return Inertia::render('Onboarding', [
            'surahs' => $surahs->map(fn ($s) => [
                'id' => $s->id,
                'name_id' => $s->name_id,
                'total_verses' => $s->total_verses,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Guard: jangan duplikat
        if ($this->programRepo->findActiveByUserId($user->id)) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'target_type' => 'required|in:daily_pages,daily_verses',
            'target_value' => 'required|integer|min:1',
            'start_choice' => 'required|in:from_beginning,from_position',
            'start_surah_id' => [
                'nullable',
                'integer',
                'exists:quran_surahs,id',
            ],
            'start_verse_number' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        // Default posisi awal (Al-Fatihah 1:1)
        $surahId = 1;
        $verseNumber = 1;

        if ($validated['start_choice'] === 'from_position' && !empty($validated['start_surah_id'])) {
            $surahId = (int) $validated['start_surah_id'];
            $verseNumber = (int) $validated['start_verse_number'];
        }

        // Find page number for the starting position
        $pageNumber = $this->resolvePageNumber($user->quran_layout_code ?? 'madinah_15', $surahId, $verseNumber);

        // Buat program
        $program = $this->programRepo->create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'target_type' => $validated['target_type'],
            'target_value' => $validated['target_value'],
            'status' => 'active',
        ]);

        // Buat progress awal
        $this->progressRepo->updateOrCreateByProgramId($program->id, [
            'last_surah_id' => $surahId,
            'last_verse_number' => $verseNumber,
            'current_page_in_home_mushaf' => $pageNumber,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Program khatam berhasil dibuat. Semangat istiqomah!');
    }

    protected function resolvePageNumber(string $layoutCode, int $surahId, int $verseNumber): int
    {
        // Cari verse_id berdasarkan surah + verse
        $verse = \App\Models\QuranVerse::query()
            ->where('surah_id', $surahId)
            ->where('verse_number', $verseNumber)
            ->first();

        if (!$verse) {
            return 1; // fallback
        }

        $mapping = $this->pageMappingRepo->findByVerseAndLayout($verse->id, $layoutCode);

        return $mapping?->page_number ?? 1;
    }
}
