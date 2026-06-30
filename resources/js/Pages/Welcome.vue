<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Quran Khatam Tracker" />

    <div class="min-h-screen bg-gradient-to-b from-emerald-50 via-cyan-50 to-white text-slate-800">
        <div class="mx-auto flex min-h-screen w-full max-w-md flex-col px-5 pb-10 pt-6 sm:max-w-xl">
            <header class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/20 ring-1 ring-emerald-300/60">
                        <span class="text-xl">🕌</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium tracking-wide text-emerald-700">Assalamu’alaikum</p>
                        <h1 class="text-base font-semibold text-slate-900">Quran Khatam Tracker</h1>
                    </div>
                </div>

                <Link
                    :href="route('signin')"
                    class="rounded-lg border border-emerald-200 bg-white/90 px-3 py-2 text-sm font-medium text-emerald-700 hover:border-emerald-400 hover:text-emerald-800"
                >
                    Login
                </Link>
            </header>

            <main class="mt-8 flex-1 space-y-6">
                <section class="rounded-2xl bg-gradient-to-br from-emerald-100 via-teal-100 to-cyan-100 p-5 ring-1 ring-emerald-200/70">
                    <p class="text-xs font-semibold tracking-wide text-emerald-700">Temani langkah tilawahmu</p>
                    <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900">
                        Tetap lanjut membaca Al-Qur’an meski mushaf berbeda halaman.
                    </h2>
                    <p class="mt-3 text-sm text-slate-700">
                        Cukup input Surah dan Ayat terakhir. Sistem membantu konversi ke halaman mushaf rumah dengan cepat.
                    </p>
                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-white/90 p-3 ring-1 ring-emerald-200">
                            <p class="text-xs text-slate-500">Target Harian</p>
                            <p class="mt-1 text-lg font-semibold text-emerald-700">2 Halaman+</p>
                        </div>
                        <div class="rounded-xl bg-white/90 p-3 ring-1 ring-emerald-200">
                            <p class="text-xs text-slate-500">Standar Data</p>
                            <p class="mt-1 text-lg font-semibold text-emerald-700">6.236 Ayat</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-white/90 p-4">
                    <p class="text-sm font-semibold text-slate-800">Pengingat kebaikan</p>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        “Sebaik-baik kalian adalah yang belajar Al-Qur’an dan mengajarkannya.”
                    </p>
                    <p class="mt-2 text-xs text-slate-500">
                        Mulai dari yang ringan, lalu jaga konsistensi setiap hari.
                    </p>
                </section>

                <section class="space-y-3">
                    <h3 class="text-sm font-semibold text-slate-700">Kenapa ini membantu?</h3>
                    <div class="space-y-2">
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-sm font-medium text-slate-800">🎯 Tracking Surah & Ayat</p>
                            <p class="mt-1 text-xs text-slate-500">Lebih akurat walau halaman tiap mushaf berbeda.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-sm font-medium text-slate-800">🏠 Mapping mushaf rumah</p>
                            <p class="mt-1 text-xs text-slate-500">Langsung tahu lanjut baca di halaman berapa saat di rumah.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-sm font-medium text-slate-800">⚡ Input cepat</p>
                            <p class="mt-1 text-xs text-slate-500">Minim friksi agar tetap semangat dan istiqamah.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-slate-800">Cara kerja singkat</p>
                    <ol class="mt-2 space-y-1 text-xs text-slate-600">
                        <li>1. Login dengan OTP WhatsApp.</li>
                        <li>2. Input posisi terakhir: Surah dan Ayat.</li>
                        <li>3. Sistem hitung mapping halaman mushaf rumah.</li>
                        <li>4. Lanjut baca tanpa bingung posisi.</li>
                    </ol>
                </section>
            </main>

            <footer class="mt-8">
                <Link
                    :href="route('signin')"
                    class="block w-full rounded-xl bg-emerald-500 px-4 py-3 text-center text-sm font-semibold text-slate-950 hover:bg-emerald-400"
                >
                    Mulai Login
                </Link>
                <p class="mt-3 text-center text-xs text-slate-500">
                    Semoga Allah mudahkan ikhtiar kita menjaga kedekatan dengan Al-Qur’an.
                </p>
            </footer>
        </div>
    </div>
</template>
