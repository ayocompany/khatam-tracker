<?php

namespace App\Http\Controllers;

use App\Models\QuranLayout;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(
        protected KhatamProgramRepositoryInterface $programRepo,
    ) {}

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $layouts = QuranLayout::query()
            ->active()
            ->orderBy('name')
            ->get(['code', 'name', 'total_pages', 'total_surahs', 'total_verses', 'lines_per_page']);

        $currentLayout = $user->quranLayout;

        $totalKhatam = $user->khatamHistories()->count();

        return Inertia::render('Settings', [
            'khatamCount'     => $totalKhatam,
            'layouts'       => $layouts,
            'currentLayout' => $currentLayout
                ? [
                    'code'           => $currentLayout->code,
                    'name'           => $currentLayout->name,
                    'total_pages'    => $currentLayout->total_pages,
                    'total_surahs'   => $currentLayout->total_surahs,
                    'total_verses'   => $currentLayout->total_verses,
                    'lines_per_page' => $currentLayout->lines_per_page,
                ]
                : null,
            'reminderTimes'       => $user->reminder_times ?? [],
            'waReminderEnabled'   => $user->wa_reminder_enabled ?? false,
        ]);
    }

    public function updateLayout(Request $request)
    {
        $validated = $request->validate([
            'layout_code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::exists('quran_layouts', 'code')->where('is_active', true),
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->update(['quran_layout_code' => $validated['layout_code']]);

        return redirect()->back()
            ->with('success', 'Layout berhasil diperbarui.');
    }

    public function resetAll(Request $request)
    {
        $user = $request->user();

        $this->programRepo->deleteByUserId($user->id);

        return redirect()->route('onboarding')
            ->with('success', 'Semua progress direset. Silakan buat program baru.');
    }

    public function updateReminders(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'reminder_times'      => ['required', 'array', 'max:5'],
            'reminder_times.*'    => ['required', 'string', 'date_format:H:i'],
            'wa_reminder_enabled' => ['required', 'boolean'],
        ]);

        if ($validated['wa_reminder_enabled'] && empty($user->phone)) {
            return redirect()->back()
                ->withErrors(['wa_reminder_enabled' => 'Nomor telepon belum terdaftar. Silakan hubungi admin.']);
        }

        $user->update([
            'reminder_times'      => $validated['reminder_times'],
            'wa_reminder_enabled' => $validated['wa_reminder_enabled'],
        ]);

        return redirect()->back()
            ->with('success', 'Pengingat tilawah berhasil disimpan.');
    }
}
