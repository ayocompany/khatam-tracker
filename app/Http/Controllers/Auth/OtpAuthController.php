<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Models\WhatsappOtp;
use App\Services\SagaWhatsappGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OtpAuthController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth');
    }

    public function sendOtp(SendOtpRequest $request, SagaWhatsappGatewayService $saga): RedirectResponse
    {
        $validated = $request->validated();
        $phone = $validated['phone'];

        $otp = (string) random_int(100000, 999999);

        $provider = $saga->sendOtp($phone, $otp);

        if (! ($provider['success'] ?? false)) {
            return back()->withErrors([
                'otp' => $provider['error'] ?? 'Gagal mengirim OTP. Coba lagi.',
            ])->withInput();
        }

        WhatsappOtp::query()->create([
            'phone' => $phone,
            'otp_code_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempt_count' => 0,
            'provider_message_id' => $provider['provider_message_id'] ?? null,
        ]);

        return back()->with('auth_state', [
            'step' => 'verify',
            'phone' => $phone,
            'simulated' => (bool) ($provider['simulated'] ?? false),
            'message' => ($provider['simulated'] ?? false)
                ? 'OTP dikirim dalam mode simulasi (credential Saga belum diisi).'
                : 'OTP berhasil dikirim ke WhatsApp kamu.',
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $phone = $validated['phone'];
        $otp = $validated['otp'];

        $otpRow = WhatsappOtp::query()->activeByPhone($phone)->first();

        if (! $otpRow) {
            return back()->withErrors([
                'otp' => 'OTP tidak ditemukan atau sudah kedaluwarsa.',
            ])->withInput();
        }

        if ($otpRow->attempt_count >= 5) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan. Silakan kirim OTP ulang.',
            ])->withInput();
        }

        if (! Hash::check($otp, $otpRow->otp_code_hash)) {
            $otpRow->increment('attempt_count');

            return back()->withErrors([
                'otp' => 'Kode OTP tidak sesuai.',
            ])->withInput();
        }

        $otpRow->forceFill([
            'verified_at' => now(),
        ])->save();

        $user = User::query()->where('phone', $phone)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => '',
                'phone' => $phone,
                'phone_verified_at' => now(),
                'password' => Str::random(40),
            ]);
        } else {
            $user->phone_verified_at = now();
            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
