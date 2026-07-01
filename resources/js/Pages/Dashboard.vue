<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const program = computed(() => page.props.program ?? null);
const progress = computed(() => page.props.progress ?? null);
const dailyTarget = computed(() => page.props.dailyTarget ?? 0);
const dailyTargetUnit = computed(() => page.props.dailyTargetUnit ?? 'halaman');

const displayName = computed(() => {
    const rawName = String(user.value?.name ?? '').trim();
    if (rawName.length > 0) return rawName;
    return "Sahabat Qur'an";
});

const displayPhone = computed(() => String(user.value?.phone ?? '-'));

const currentPage = computed(() => progress.value?.current_page ?? 1);
const totalLayoutPages = computed(() => progress.value?.total_pages ?? 604);
const completionPercent = computed(() => progress.value?.completion_percent ?? 0);
const progressWidth = computed(() => `${completionPercent.value}%`);
const remainingPages = computed(() => progress.value?.remaining_pages ?? 0);
const lastPositionLabel = computed(() => progress.value?.last_position_label ?? 'Al-Fatihah: 1');

const suggestedReadFrom = computed(() => page.props.suggestedReadFrom ?? null);
const suggestedReadTo = computed(() => page.props.suggestedReadTo ?? null);
const targetPages = computed(() => page.props.targetPages ?? 0);
const todayReadSurah = computed(() => page.props.todayReadSurah ?? null);
const todayReadFromAyah = computed(() => page.props.todayReadFromAyah ?? null);
const todayReadToAyah = computed(() => page.props.todayReadToAyah ?? null);
const todayReadFromPage = computed(() => page.props.todayReadFromPage ?? null);
const todayReadToPage = computed(() => page.props.todayReadToPage ?? null);
const todayPagesDetail = computed(() => page.props.todayPagesDetail ?? []);
const dailyTargetValue = computed(() => dailyTarget.value ?? 0);
const todayRead = computed(() => page.props.todayRead ?? 0);
const weeklyChart = computed(() => page.props.weeklyChart ?? []);
const totalKhatam = computed(() => page.props.totalKhatam ?? 0);
const suggestedAyatSurahId = computed(() => page.props.suggestedAyatSurahId ?? null);
const suggestedAyatVerseNumber = computed(() => page.props.suggestedAyatVerseNumber ?? null);
const targetType = computed(() => program.value?.target_type ?? 'daily_pages');
const surahs = computed(() => page.props.surahs ?? []);
const estimatedCompletionDate = computed(() => page.props.estimatedCompletionDate ?? null);

const programTitle = computed(() => program.value?.title ?? 'Khatam 30 Juz');
const isAyatMode = computed(() => targetType.value === 'daily_verses');

const suggestedAyatSurahName = computed(() => {
    if (!suggestedAyatSurahId.value) return null;
    const s = surahs.value.find(s => s.id === Number(suggestedAyatSurahId.value));
    return s?.name_id ?? null;
});

const suggestedAyatEndSurahName = computed(() => {
    if (!suggestedAyatEndSurahId.value) return null;
    const s = surahs.value.find(s => s.id === Number(suggestedAyatEndSurahId.value));
    return s?.name_id ?? null;
});

const todayReadFromSurahName = computed(() => {
    const id = progress.value?.last_surah_id;
    if (!id) return null;
    const s = surahs.value.find(s => s.id === Number(id));
    return s?.name_id ?? null;
});

const todayReadFromVerseNumber = computed(() => progress.value?.last_verse_number ?? null);

const suggestedAyatEndSurahId = computed(() => page.props.suggestedAyatEndSurahId ?? null);
const suggestedAyatEndVerseNumber = computed(() => page.props.suggestedAyatEndVerseNumber ?? null);
const suggestedTotalVerses = computed(() => page.props.suggestedTotalVerses ?? 0);
</script>

<template>
    <Head title="Dashboard - Quran Khatam Tracker" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Greeting -->
                <section class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-cyan-50 to-white p-5">
                    <p class="text-sm text-emerald-700">Assalamu'alaikum,</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ displayName }}</h1>
                    <p class="mt-1 text-xs text-slate-600">Nomor terverifikasi: {{ displayPhone }}</p>

                    <!-- ✅ Jawaban: Ngaji apa hari ini? — Mode halaman -->
                    <div v-if="!isAyatMode && todayReadSurah && todayReadFromPage" class="mt-4 rounded-xl bg-white/70 p-4 ring-1 ring-emerald-200">
                        <p class="text-xs text-emerald-600 font-medium">🗓️ Jawaban: Ngaji apa hari ini?</p>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-lg bg-emerald-100 p-2">
                                <p class="text-[10px] text-emerald-600">Surat</p>
                                <p class="mt-0.5 text-sm font-bold text-emerald-800">{{ todayReadSurah }}</p>
                            </div>
                            <div class="rounded-lg bg-emerald-100 p-2">
                                <p class="text-[10px] text-emerald-600">Ayat</p>
                                <p class="mt-0.5 text-sm font-bold text-emerald-800">
                                    {{ todayReadFromAyah }}<span v-if="todayReadToAyah && todayReadToAyah !== todayReadFromAyah"> – {{ todayReadToAyah }}</span>
                                </p>
                            </div>
                            <div class="rounded-lg bg-emerald-100 p-2">
                                <p class="text-[10px] text-emerald-600">Halaman</p>
                                <p class="mt-0.5 text-sm font-bold text-emerald-800">{{ todayReadFromPage }}<span v-if="todayReadToPage && todayReadToPage !== todayReadFromPage"> – {{ todayReadToPage }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ Jawaban: Ngaji apa hari ini? — Mode ayat -->
                    <div v-if="isAyatMode && todayReadFromSurahName && suggestedAyatSurahName" class="mt-4 rounded-xl bg-purple-50/70 p-4 ring-1 ring-purple-200">
                        <p class="text-xs text-purple-600 font-medium">🗓️ Jawaban: Ngaji apa hari ini?</p>
                        <p class="mt-2 text-center text-sm text-slate-700">
                            Baca <strong>{{ dailyTargetValue }} ayat</strong> dari
                            <strong class="text-purple-700">{{ todayReadFromSurahName }} : {{ todayReadFromVerseNumber }}</strong>
                            →
                            <strong class="text-purple-700">{{ suggestedAyatSurahName }} : {{ suggestedAyatVerseNumber }}</strong>
                        </p>
                    </div>

                    <!-- 📄 Breakdown per halaman di Dashboard (page mode only) -->
                    <div v-if="!isAyatMode && todayPagesDetail.length" class="mt-4 space-y-1.5">
                        <p class="text-xs font-medium text-emerald-600">📄 Rincian bacaan:</p>
                        <div v-for="(p, i) in todayPagesDetail" :key="i" class="flex items-center gap-2 rounded-lg bg-white/80 px-3 py-1.5 text-xs">
                            <span class="font-bold text-sky-700 min-w-[4rem]">Hlm {{ p.page }}</span>
                            <span class="text-slate-700">
                                <span class="font-semibold">{{ p.surah_name }}</span>
                                <template v-if="p.to_ayah !== null && p.from_ayah !== p.to_ayah">: {{ p.from_ayah }} – {{ p.to_ayah }}</template>
                                <template v-else-if="p.to_ayah === null && p.to_surah_name">: {{ p.from_ayah }} → {{ p.to_surah_name }}: {{ p.to_ayah_end }}</template>
                                <template v-else>: {{ p.from_ayah }}</template>
                            </span>
                            <span class="ml-auto text-slate-400">({{ p.verse_count }} ayat)</span>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-slate-700">
                        Jaga konsistensi tilawah harian. Sedikit tapi rutin, insyaAllah membawa keberkahan.
                    </p>
                </section>

                <!-- Progress cards -->
                <section class="grid gap-4 lg:grid-cols-3">
                    <article class="rounded-2xl border border-purple-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">🏆 Riwayat Khatam</h3>
                        <p class="mt-2 text-3xl font-bold text-purple-700">{{ totalKhatam }}</p>
                        <p class="mt-1 text-xs text-slate-500">Total khatam yang tercatat</p>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Progress Khatam</h3>
                                <p class="mt-1 text-xs text-slate-500">Update terakhir: Hari ini</p>
                            </div>
                            <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ completionPercent }}%</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 transition-all duration-500" :style="{ width: progressWidth }" />
                            </div>
                            <!-- Page mode: show halaman numbers -->
                            <div v-if="!isAyatMode" class="flex items-center justify-between text-xs text-slate-500">
                                <span>{{ currentPage }} / {{ totalLayoutPages }} halaman</span>
                                <span>Sisa {{ remainingPages }} halaman</span>
                            </div>
                            <!-- Ayat mode: show surah:ayat position -->
                            <div v-else class="flex items-center justify-between text-xs text-slate-500">
                                <span>{{ lastPositionLabel }}</span>
                                <span>{{ completionPercent }}% selesai</span>
                            </div>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Posisi Terakhir</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ lastPositionLabel }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Estimasi Khatam</p>
                                <p v-if="estimatedCompletionDate" class="mt-1 text-sm font-semibold text-slate-800">{{ estimatedCompletionDate }}</p>
                                <p v-else class="mt-1 text-sm font-semibold text-slate-800">—</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Program</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ programTitle }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm space-y-4">
                        <h3 class="text-base font-semibold text-slate-900">🎯 Target Harian</h3>

                        <!-- Guided range card: page mode -->
                        <div v-if="!isAyatMode && suggestedReadFrom && suggestedReadTo && targetPages > 0" class="rounded-xl bg-emerald-50 p-4 ring-1 ring-emerald-200">
                            <p class="text-xs text-emerald-700">Bacaan hari ini</p>
                            <p class="mt-1 text-xl font-bold text-emerald-700">Halaman {{ suggestedReadFrom }} → {{ suggestedReadTo }}</p>
                            <p v-if="suggestedAyatSurahName" class="mt-1 text-sm font-semibold text-emerald-600">
                                {{ suggestedAyatSurahName }} : {{ suggestedAyatVerseNumber }}
                                → {{ suggestedAyatEndSurahName }} : {{ suggestedAyatEndVerseNumber }}
                            </p>
                            <p v-if="suggestedTotalVerses > 0" class="mt-1 text-xs text-slate-600">{{ suggestedTotalVerses }} ayat • {{ targetPages }} halaman • target harian</p>
                            <p v-else class="mt-1 text-xs text-slate-600">{{ targetPages }} halaman • target harian</p>
                        </div>

                        <!-- Guided range card: ayat mode -->
                        <div v-if="isAyatMode && todayReadFromSurahName && suggestedAyatSurahName" class="rounded-xl bg-purple-50 p-4 ring-1 ring-purple-200">
                            <p class="text-xs text-purple-700">Bacaan hari ini</p>
                            <p class="mt-1 text-sm font-semibold text-purple-700">
                                {{ todayReadFromSurahName }} : {{ todayReadFromVerseNumber }}
                                → {{ suggestedAyatSurahName }} : {{ suggestedAyatVerseNumber }}
                            </p>
                            <p class="mt-1 text-xs text-slate-600">Target {{ dailyTargetValue }} ayat</p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 p-4 ring-1 ring-emerald-200">
                            <p class="text-xs text-emerald-700">Target</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-700">{{ dailyTargetValue }} {{ dailyTargetUnit }}</p>
                        </div>

                        <div class="rounded-xl bg-cyan-50 p-4 ring-1 ring-cyan-200">
                            <p class="text-xs text-cyan-700">Progress hari ini</p>
                            <p class="mt-1 text-lg font-semibold text-cyan-800">{{ todayRead }} / {{ dailyTargetValue }} {{ dailyTargetUnit }}</p>
                        </div>

                        <Link :href="route('progress.update')" class="block w-full rounded-xl bg-emerald-500 px-4 py-3 text-center text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                            ➜ Lanjut baca sekarang
                        </Link>
                    </article>
                </section>

                <!-- 7 Hari Terakhir -->
                <section class="grid gap-4 lg:grid-cols-2">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">📊 7 Hari Terakhir</h3>
                        <div class="mt-4 flex items-end gap-1.5" style="height: 80px;">
                            <div v-for="(day, i) in weeklyChart" :key="i" class="flex flex-1 flex-col items-center justify-end">
                                <div class="w-full rounded-t bg-emerald-400 transition-all" :style="{ height: Math.max(4, (day.pages_read / Math.max(...weeklyChart.map(d => d.pages_read), 1)) * 68) + 'px' }" :title="day.date + ': ' + day.pages_read + (isAyatMode ? ' ayat' : ' hlm')" />
                                <span class="mt-1 text-[10px] text-slate-400">{{ day.date.slice(5) }}</span>
                            </div>
                            <div v-if="weeklyChart.length === 0" class="flex w-full items-center justify-center text-xs text-slate-400">Belum ada data</div>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Bar chart bacaan harian ({{ isAyatMode ? 'ayat' : 'halaman' }}) 7 hari terakhir.</p>
                    </article>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
