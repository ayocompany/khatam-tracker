<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

// ── Animated Counter ──
const countRef = ref(null);
const ayatCount = ref(0);
const userCount = ref(0);
const halamanCount = ref(0);
let counterObs = null;

function animateCounter(target, el, duration = 2000) {
  const start = performance.now();
  function tick(now) {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    // ease-out cubic
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(eased * target).toLocaleString();
    if (progress < 1) requestAnimationFrame(tick);
    else el.textContent = target.toLocaleString();
  }
  requestAnimationFrame(tick);
}

onMounted(() => {
  counterObs = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const els = entry.target.querySelectorAll('[data-count]');
        els.forEach((el) => animateCounter(Number(el.dataset.count), el));
        counterObs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });
  if (countRef.value) counterObs.observe(countRef.value);
});

onUnmounted(() => counterObs && counterObs.disconnect());

// ── Testimonial carousel ──
const testimonials = [
  { text: 'Alhamdulillah, sekarang saya bisa tracking bacaan tanpa bingung halaman mushaf rumah.', name: '— Fajri, Jakarta' },
  { text: 'Aplikasi yang sangat membantu untuk tetap istiqamah tilawah setiap hari.', name: '— Aisyah, Bandung' },
  { text: 'Saya suka karena simpel, cepat, dan pas untuk target khatam bulan ini.', name: '— Rizky, Surabaya' },
];
const activeTesti = ref(0);
let testiInterval = null;

onMounted(() => {
  testiInterval = setInterval(() => {
    activeTesti.value = (activeTesti.value + 1) % testimonials.length;
  }, 4500);
});

onUnmounted(() => clearInterval(testiInterval));
</script>

<template>
  <Head title="Quran Khatam Tracker - Gratis & Mudah" />

  <div class="relative min-h-screen overflow-hidden bg-gradient-to-b from-emerald-50 via-cyan-50 to-white text-slate-800">
    <!-- Animated background blobs -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -left-20 -top-20 h-80 w-80 animate-pulse rounded-full bg-emerald-200/40 blur-3xl"></div>
      <div class="absolute -right-20 top-1/3 h-96 w-96 animate-pulse rounded-full bg-cyan-200/30 blur-3xl" style="animation-delay: 1s; animation-duration: 4s;"></div>
      <div class="absolute -bottom-20 left-1/4 h-72 w-72 animate-pulse rounded-full bg-teal-200/30 blur-3xl" style="animation-delay: 2s; animation-duration: 5s;"></div>
    </div>

    <div class="relative mx-auto min-h-screen w-full max-w-md px-5 pb-10 pt-6 sm:max-w-xl">
      <!-- ===== HEADER ===== -->
      <header class="flex items-start justify-between animate-fade-in-down">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/20 ring-1 ring-emerald-300/60 animate-bounce-in">
            <span class="text-xl">🕌</span>
          </div>
          <div>
            <p class="text-xs font-medium tracking-wide text-emerald-700">Assalamu'alaikum 🌙</p>
            <h1 class="text-base font-semibold text-slate-900">Quran Khatam Tracker</h1>
          </div>
        </div>
        <Link
          :href="route('signin')"
          class="rounded-lg border border-emerald-200 bg-white/90 px-3 py-2 text-sm font-medium text-emerald-700 shadow-sm transition-all duration-300 hover:border-emerald-400 hover:shadow-md hover:-translate-y-0.5"
        >
          Masuk
        </Link>
      </header>

      <!-- ===== HERO ===== -->
      <main class="mt-8 flex-1 space-y-8">
        <section class="animate-fade-in-up rounded-2xl bg-gradient-to-br from-emerald-100 via-teal-100 to-cyan-100 p-5 ring-1 ring-emerald-200/70 shadow-lg" style="animation-delay: 0.1s;">
          <p class="text-xs font-semibold tracking-wide text-emerald-700">✨ Gratis — Istiqamah — Berkah</p>
          <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900">
            Khatam Al-Qur'an<br />
            <span class="bg-gradient-to-r from-emerald-600 to-cyan-600 bg-clip-text text-transparent">Lebih Mudah & Terarah</span>
          </h2>
          <p class="mt-3 text-sm text-slate-700 leading-relaxed">
            Cukup input surah & ayat terakhir. Sistem langsung ngasih tau halaman mushaf rumah kamu.
            Gak perlu bingung lagi!
          </p>

          <!-- Stats counter -->
          <div ref="countRef" class="mt-5 grid grid-cols-3 gap-2">
            <div class="rounded-xl bg-white/80 p-3 ring-1 ring-emerald-200 text-center transition-all duration-300 hover:scale-105 hover:bg-white">
              <p class="text-xs text-slate-500">Ayat Al-Qur'an</p>
              <p class="mt-1 text-lg font-bold text-emerald-700">
                <span data-count="6236">0</span>
              </p>
            </div>
            <div class="rounded-xl bg-white/80 p-3 ring-1 ring-emerald-200 text-center transition-all duration-300 hover:scale-105 hover:bg-white" style="transition-delay: 0.05s;">
              <p class="text-xs text-slate-500">Surah</p>
              <p class="mt-1 text-lg font-bold text-emerald-700">
                <span data-count="114">0</span>
              </p>
            </div>
            <div class="rounded-xl bg-white/80 p-3 ring-1 ring-emerald-200 text-center transition-all duration-300 hover:scale-105 hover:bg-white" style="transition-delay: 0.1s;">
              <p class="text-xs text-slate-500">Halaman Mushaf</p>
              <p class="mt-1 text-lg font-bold text-emerald-700">
                <span data-count="604">0</span>
              </p>
            </div>
          </div>
        </section>

        <!-- ===== MOTIVASI HADITS ===== -->
        <section class="animate-fade-in-up rounded-2xl border border-emerald-200 bg-white/90 p-5 shadow-sm transition-all duration-500 hover:shadow-md" style="animation-delay: 0.2s;">
          <div class="flex items-start gap-3">
            <span class="mt-0.5 text-lg">💚</span>
            <div>
              <p class="text-sm font-semibold text-slate-800">Pengingat Kebaikan</p>
              <p class="mt-2 text-sm leading-relaxed text-slate-600 italic">
                "Sebaik-baik kalian adalah yang mempelajari Al-Qur'an dan mengajarkannya."
              </p>
              <p class="mt-1 text-xs text-emerald-600 font-medium">— HR. Bukhari</p>
              <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Mulai dari satu ayat, jaga konsistensi, niscaya khatam bukan lagi mimpi.
              </p>
            </div>
          </div>
        </section>

        <!-- ===== FITUR UTAMA ===== -->
        <section class="animate-fade-in-up space-y-3" style="animation-delay: 0.3s;">
          <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <span>⚡</span> Kenapa ribuan pengguna pilih ini?
          </h3>
          <div class="space-y-2">
            <div class="group rounded-xl border border-slate-200 bg-white p-3 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5">
              <p class="text-sm font-medium text-slate-800 group-hover:text-emerald-700 transition-colors">🎯 Tracking Surah & Ayat Akurat</p>
              <p class="mt-1 text-xs text-slate-500">Tidak peduli beda cetakan mushaf. Cukup input surah + ayat, sistem urus sisanya.</p>
            </div>
            <div class="group rounded-xl border border-slate-200 bg-white p-3 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5" style="transition-delay: 0.05s;">
              <p class="text-sm font-medium text-slate-800 group-hover:text-emerald-700 transition-colors">🏠 Auto-Mapping Halaman Rumah</p>
              <p class="mt-1 text-xs text-slate-500">Langsung tahu lanjut baca di halaman berapa — cocok untuk mushaf Madinah, Indonesia, dll.</p>
            </div>
            <div class="group rounded-xl border border-slate-200 bg-white p-3 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5" style="transition-delay: 0.1s;">
              <p class="text-sm font-medium text-slate-800 group-hover:text-emerald-700 transition-colors">⚡ Input Super Cepat</p>
              <p class="mt-1 text-xs text-slate-500">Minim friksi, maksimal semangat. Cocok buat kamu yang sibuk tapi ingin tetap istiqamah.</p>
            </div>
            <div class="group rounded-xl border border-slate-200 bg-white p-3 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5" style="transition-delay: 0.15s;">
              <p class="text-sm font-medium text-slate-800 group-hover:text-emerald-700 transition-colors">📊 Progress Tracker Harian</p>
              <p class="mt-1 text-xs text-slate-500">Pantau target harian, lihat perkembangan, dan tetap termotivasi setiap hari.</p>
            </div>
          </div>
        </section>

        <!-- ===== CARA KERJA ===== -->
        <section class="animate-fade-in-up rounded-2xl border border-emerald-200 bg-emerald-50 p-5 transition-all duration-500 hover:shadow-md" style="animation-delay: 0.4s;">
          <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
            <span>📖</span> Cara Kerja (1 Menit!)
          </p>
          <div class="mt-4 grid grid-cols-4 gap-2">
            <div class="flex flex-col items-center text-center">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white shadow-sm">1</div>
              <p class="mt-1 text-[10px] text-slate-600 leading-tight">Login via OTP WhatsApp</p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white shadow-sm">2</div>
              <p class="mt-1 text-[10px] text-slate-600 leading-tight">Input surah & ayat terakhir</p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white shadow-sm">3</div>
              <p class="mt-1 text-[10px] text-slate-600 leading-tight">Sistem konversi halaman</p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white shadow-sm">4</div>
              <p class="mt-1 text-[10px] text-slate-600 leading-tight">Lanjut baca! 🎉</p>
            </div>
          </div>
        </section>

        <!-- ===== TESTIMONIAL CAROUSEL ===== -->
        <section class="animate-fade-in-up rounded-2xl border border-cyan-200 bg-cyan-50/70 p-5 transition-all duration-500 hover:shadow-md" style="animation-delay: 0.5s;">
          <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
            <span>💬</span> Kata Mereka
          </p>
          <div class="mt-3 min-h-[5rem] relative">
            <transition-group name="testi">
              <div v-for="(t, i) in testimonials" :key="i" v-show="i === activeTesti" class="text-sm text-slate-600 leading-relaxed">
                <p class="italic">"{{ t.text }}"</p>
                <p class="mt-2 text-xs font-medium text-cyan-700">{{ t.name }}</p>
              </div>
            </transition-group>
          </div>
          <!-- Dots -->
          <div class="mt-3 flex justify-center gap-1.5">
            <button
              v-for="(_, i) in testimonials"
              :key="i"
              @click="activeTesti = i"
              class="h-2 rounded-full transition-all duration-300"
              :class="i === activeTesti ? 'w-6 bg-emerald-500' : 'w-2 bg-slate-300 hover:bg-slate-400'"
            ></button>
          </div>
        </section>

        <!-- ===== FITUR GRATIS ===== -->
        <section class="animate-fade-in-up rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-5 text-center" style="animation-delay: 0.6s;">
          <p class="text-2xl">🎁</p>
          <p class="mt-2 text-sm font-bold text-slate-900">Gratis Selamanya ✨</p>
          <p class="mt-1 text-xs text-slate-600">
            Tidak ada biaya, tidak ada trial. Akses penuh semua fitur — selamanya.
          </p>
        </section>
      </main>

      <!-- ===== CTA FOOTER ===== -->
      <footer class="mt-8 space-y-4 animate-fade-in-up" style="animation-delay: 0.7s;">
        <Link
          :href="route('signin')"
          class="group relative block w-full overflow-hidden rounded-xl bg-emerald-500 px-4 py-3.5 text-center text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:bg-emerald-400 hover:shadow-xl hover:-translate-y-0.5 active:scale-95"
        >
          <span class="relative z-10">Mulai Tracking Gratis →</span>
          <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-emerald-400 to-emerald-300 transition-transform duration-500 group-hover:translate-x-0"></div>
        </Link>

        <!-- Trust badges -->
        <div class="flex items-center justify-center gap-4 text-[10px] text-slate-400">
          <span class="flex items-center gap-1">🔒 Data aman</span>
          <span class="flex items-center gap-1">⚡ No ribet</span>
          <span class="flex items-center gap-1">💚 100% gratis</span>
        </div>

        <p class="text-center text-xs text-slate-500 leading-relaxed">
          Semoga Allah mudahkan ikhtiar kita<br />
          menjaga kedekatan dengan Al-Qur'an. Aamiin. 🤲
        </p>
      </footer>
    </div>
  </div>
</template>

<style scoped>
/* ── FADE IN ANIMATIONS ── */
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in-down {
  from { opacity: 0; transform: translateY(-16px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes bounce-in {
  0%   { opacity: 0; transform: scale(0.3); }
  50%  { transform: scale(1.1); }
  70%  { transform: scale(0.9); }
  100% { opacity: 1; transform: scale(1); }
}
.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out both;
}
.animate-fade-in-down {
  animation: fade-in-down 0.5s ease-out both;
}
.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55) both;
}

/* ── TESTIMONIAL TRANSITION ── */
.testi-enter-active {
  transition: all 0.5s ease-out;
}
.testi-leave-active {
  transition: all 0.3s ease-in;
  position: absolute;
}
.testi-enter-from {
  opacity: 0; transform: translateX(30px);
}
.testi-leave-to {
  opacity: 0; transform: translateX(-30px);
}
</style>
