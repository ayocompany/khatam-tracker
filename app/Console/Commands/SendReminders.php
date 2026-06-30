<?php

namespace App\Console\Commands;

use App\Models\KhatamProgram;
use App\Models\KhatamDailyLog;
use App\Models\User;
use App\Services\SagaWhatsappGatewayService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Kirim pengingat tilawah via WhatsApp ke user yang sudah mengaktifkan.';

    public function handle(SagaWhatsappGatewayService $saga): int
    {
        $now = Carbon::now('Asia/Jakarta');
        $time = $now->format('H:i');
        $today = $now->format('Y-m-d');
        $todayFormatted = $now->isoFormat('dddd, D MMMM YYYY');

        $users = User::query()
            ->where('wa_reminder_enabled', true)
            ->whereNotNull('phone')
            ->where('reminder_times', 'like', "%\"{$time}\"%")
            ->get();

        $sent = 0;
        $errors = 0;

        foreach ($users as $user) {
            try {
                $program = KhatamProgram::query()
                    ->where('user_id', $user->id)
                    ->active()
                    ->first();

                $todayRead = 0;
                if ($program) {
                    $log = KhatamDailyLog::query()
                        ->where('khatam_program_id', $program->id)
                        ->where('log_date', $today)
                        ->first();
                    $todayRead = $log ? $log->pages_read : 0;
                }

                $link = url('/dashboard');

                $msg = "📖 *Pengingat Tilawah Harian*\n\n"
                     . "🗓️ {$todayFormatted}\n"
                     . "⏰ Pukul {$time} WIB\n\n";

                if ($todayRead > 0) {
                    $msg .= "Alhamdulillah, hari ini kamu sudah membaca {$todayRead} halaman.\n"
                          . "Jaga istiqamah! 🤲\n\n";
                } else {
                    $msg .= "Yuk baca Al-Qur'an sekarang! Catat progresmu biar makin semangat 🚀\n\n";
                }

                $msg .= "➡️ " . $link;

                $saga->sendText($user->phone, $msg);
                $sent++;

            } catch (\Throwable $e) {
                $errors++;
                Log::error('SendReminders WA failed', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $this->info("WA reminders: sent={$sent} errors={$errors}");

        return Command::SUCCESS;
    }
}
