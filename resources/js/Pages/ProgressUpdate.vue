<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const surahs = computed(() => page.props.surahs ?? []);
const currentProgress = computed(() => page.props.currentProgress ?? null);

const suggestedReadFrom = computed(() => page.props.suggestedReadFrom ?? null);
const suggestedReadTo = computed(() => page.props.suggestedReadTo ?? null);
const targetPages = computed(() => page.props.targetPages ?? 0);
const suggestedSurahId = computed(() => page.props.suggestedSurahId ?? null);
const suggestedVerseNumber = computed(() => page.props.suggestedVerseNumber ?? null);
const suggestedAyatSurahId = computed(() => page.props.suggestedAyatSurahId ?? null);
const suggestedAyatVerseNumber = computed(() => page.props.suggestedAyatVerseNumber ?? null);
const suggestedAyatEndSurahId = computed(() => page.props.suggestedAyatEndSurahId ?? null);
const suggestedAyatEndVerseNumber = computed(() => page.props.suggestedAyatEndVerseNumber ?? null);
const pagesDetail = computed(() => page.props.pagesDetail ?? []);
const activeLayout = computed(() => page.props.activeLayout ?? null);
const targetType = computed(() => page.props.targetType ?? 'daily_pages');
const targetValue = computed(() => page.props.targetValue ?? 0);

const isAyatMode = computed(() => targetType.value === 'daily_verses');

// Next unread position (last + 1)
const nextPage = computed(() => {
    const last = suggestedReadTo.value ?? currentProgress.value?.current_page_in_home_mushaf ?? 0;
    return last > 0 ? last + 1 : 1;
});

function computeNextVerse() {
    const endSurahId = suggestedAyatEndSurahId.value ?? suggestedAyatSurahId.value;
    const endVerse = suggestedAyatEndVerseNumber.value ?? suggestedAyatVerseNumber.value;
    if (!endSurahId || !endVerse) {
        const lastSurahId = currentProgress.value?.last_surah_id ?? 1;
        const lastVerse = currentProgress.value?.last_verse_number ?? 1;
        const s = surahs.value.find(s => s.id === Number(lastSurahId));
        if (lastVerse < (s?.total_verses ?? 0)) return { id: lastSurahId, verse: lastVerse + 1 };
        return { id: Math.min(114, lastSurahId + 1), verse: 1 };
    }
    const s = surahs.value.find(s => s.id === Number(endSurahId));
    if (endVerse < (s?.total_verses ?? 0)) return { id: endSurahId, verse: endVerse + 1 };
    return { id: Math.min(114, endSurahId + 1), verse: 1 };
}

const nextStartAyat = computed(() => computeNextVerse());

const form = ref(isAyatMode.value ? {
    surah_id: nextStartAyat.value.id,
    verse_number: nextStartAyat.value.verse,
} : {
    current_page_in_home_mushaf: nextPage.value,
});

const errors = ref({});
const submitting = ref(false);

// Compute suggested surah name for ayat mode
const suggestedAyatSurahName = computed(() => {
    if (!suggestedAyatSurahId.value) return null;
    const s = surahs.value.find(s => s.id === Number(suggestedAyatSurahId.value));
    return s?.name_id ?? null;
});

function submit() {
    submitting.value = true;
    errors.value = {};

    const payload = isAyatMode.value
        ? { surah_id: Number(form.value.surah_id), verse_number: Number(form.value.verse_number) }
        : { current_page_in_home_mushaf: Number(form.value.current_page_in_home_mushaf) };

    router.post(route('progress.update.store'), payload, {
        preserveScroll: true,
        onError: (errs) => {
            errors.value = errs;
            submitting.value = false;
        },
        onFinish: () => { submitting.value = false; },
    });
}

// For ayat mode, compute surah name for preview
const selectedSurahName = computed(() => {
    if (!form.value.surah_id) return '—';
    const s = surahs.value.find(s => s.id === Number(form.value.surah_id));
    return s?.name_id ?? '—';
});
</script>

<template>
    <Head title="Update Progress" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Update Progress Tilawah
            </h2>
        </template>

            <div class="py-8">
            <div class="mx-auto max-w-lg space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Guide Banner: page mode -->
                <section v-if="!isAyatMode && suggestedReadFrom && suggestedReadTo && targetPages > 0" class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-cyan-50 p-5">
                    <p class="text-sm font-semibold text-emerald-800">📖 Ngaji apa hari ini?</p>
                    <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                        Bacaan kamu hari ini dari
                        <strong class="text-emerald-700">halaman {{ suggestedReadFrom }}</strong>
                        sampai
                        <strong class="text-emerald-700">halaman {{ suggestedReadTo }}</strong>
                        ({{ targetPages }} halaman).
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Kolom di bawah sudah terisi otomatis. Sesuaikan jika perlu, lalu simpan.
                    </p>
                </section>

                <!-- Guide Banner: ayat mode -->
                <section v-if="isAyatMode && currentProgress && targetValue > 0 && suggestedAyatEndSurahId" class="rounded-2xl border border-purple-200 bg-gradient-to-r from-purple-50 to-pink-50 p-5">
                    <p class="text-sm font-semibold text-purple-800">📖 Ngaji apa hari ini?</p>
                    <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                        Hari ini baca <strong>{{ targetValue }} ayat</strong> dari
                        <strong class="text-purple-700">{{ surahs.find(s => s.id === currentProgress.last_surah_id)?.name_id ?? '—' }} : {{ currentProgress.last_verse_number }}</strong>
                        →
                        <strong class="text-purple-700">{{ suggestedAyatSurahName }} : {{ suggestedAyatEndVerseNumber }}</strong>.
                    </p>
                    <p class="mt-1 text-xs text-slate-600">
                        ✅ Form di bawah sudah terisi dengan ayat selanjutnya: <strong>{{ surahs.find(s => s.id === nextStartAyat.id)?.name_id }} : {{ nextStartAyat.verse }}</strong>.
                        Sesuaikan jika bacaan berbeda.
                    </p>
                </section>

                <section v-else-if="!isAyatMode" class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5">
                    <p class="text-sm text-slate-700 leading-relaxed">
                        Catat posisi bacaan terakhir kamu hari ini.
                    </p>
                </section>

                <!-- Layout info -->
                <section v-if="activeLayout" class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <p class="text-xs text-slate-500">
                        📖 Mushaf: <span class="font-semibold text-slate-700">{{ activeLayout.name }}</span>
                    </p>
                </section>

                <!-- Flexibility note -->
                <section class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs text-amber-800">
                        💡 Kamu bisa update bertahap: baca 1 halaman → simpan, nanti lanjut halaman berikutnya → simpan lagi.
                        Atau baca sekaligus 2 halaman → simpan sekali. Progress harian akan terakumulasi otomatis.
                    </p>
                </section>

                <!-- 📄 Breakdown per halaman (page mode only) -->
                <section v-if="!isAyatMode && pagesDetail.length" class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-sky-800">📄 Detail bacaan per halaman</h3>
                    <div class="mt-3 space-y-2">
                        <div
                            v-for="(p, i) in pagesDetail"
                            :key="i"
                            class="flex items-center gap-3 rounded-xl bg-sky-50 px-4 py-2.5 text-sm"
                        >
                            <span class="font-bold text-sky-700 min-w-[5rem]">Halaman {{ p.page }}</span>
                            <span class="text-slate-700">
                                <span class="font-semibold">{{ p.surah_name }}</span>
                                <template v-if="p.to_ayah !== null && p.from_ayah !== p.to_ayah">
                                    : {{ p.from_ayah }} – {{ p.to_ayah }}
                                </template>
                                <template v-else-if="p.to_ayah !== null && p.from_ayah === p.to_ayah">
                                    : {{ p.from_ayah }}
                                </template>
                                <template v-else-if="p.to_ayah === null && p.to_surah_name">
                                    : {{ p.from_ayah }} → {{ p.to_surah_name }}: {{ p.to_ayah_end }}
                                </template>
                                <template v-else>
                                    : {{ p.from_ayah }}
                                </template>
                            </span>
                            <span class="ml-auto text-xs text-slate-400">({{ p.verse_count }} ayat)</span>
                        </div>
                    </div>
                </section>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Page input (page mode) -->
                    <section v-if="!isAyatMode" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">Halaman Terakhir</h3>
                        <p class="mt-1 text-xs text-slate-500">Sampai halaman berapa kamu membaca?</p>
                        <input
                            v-model="form.current_page_in_home_mushaf"
                            type="number"
                            min="1"
                            class="mt-3 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400 sm:max-w-xs"
                            placeholder="Misal: 12"
                        />
                        <p v-if="errors.current_page_in_home_mushaf" class="mt-1 text-xs text-red-500">{{ errors.current_page_in_home_mushaf }}</p>
                        <p class="mt-2 text-xs text-slate-400" v-if="suggestedSurahId && suggestedVerseNumber">
                            Berdasarkan halaman {{ suggestedReadTo }}, posisi ayat: {{ surahs.find(s => s.id === suggestedSurahId)?.name_id ?? '—' }} ayat {{ suggestedVerseNumber }}
                        </p>
                    </section>

                    <!-- Surah + Verse selector (ayat mode) -->
                    <section v-if="isAyatMode" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">Ayat terakhir dibaca</h3>
                        <p class="mt-1 text-xs text-slate-500">Sudah terisi otomatis berdasarkan target. Sesuaikan jika bacaan berbeda.</p>

                        <div class="mt-3 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Surah</label>
                                <select
                                    v-model="form.surah_id"
                                    class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-purple-400 focus:ring-purple-400"
                                >
                                    <option
                                        v-for="s in surahs"
                                        :key="s.id"
                                        :value="s.id"
                                    >{{ s.name_id }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Ayat ke-</label>
                                <input
                                    v-model="form.verse_number"
                                    type="number"
                                    min="1"
                                    class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-purple-400 focus:ring-purple-400 sm:max-w-xs"
                                    placeholder="Contoh: 15"
                                />
                                <p v-if="errors.verse_number" class="mt-1 text-xs text-red-500">{{ errors.verse_number }}</p>
                                <p v-if="errors.surah_id" class="mt-1 text-xs text-red-500">{{ errors.surah_id }}</p>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-slate-500" v-if="suggestedAyatEndSurahId">
                            ✨ Terisi otomatis: <strong>{{ surahs.find(s => s.id === nextStartAyat.id)?.name_id }} : {{ nextStartAyat.verse }}</strong>.
                            Sesuaikan jika selesai di ayat berbeda.
                        </p>
                    </section>

                    <!-- Summary card -->
                    <section v-if="!isAyatMode" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                        <h3 class="text-sm font-semibold text-emerald-800">Ringkasan yang akan disimpan</h3>
                        <p class="mt-1 text-sm text-slate-700">
                            Halaman <strong>{{ form.current_page_in_home_mushaf || '—' }}</strong>
                            (surah & ayat akan terisi otomatis dari halaman yang dipilih)
                        </p>
                    </section>

                    <section v-if="isAyatMode" class="rounded-2xl border border-purple-100 bg-purple-50 p-5">
                        <h3 class="text-sm font-semibold text-purple-800">Ringkasan yang akan disimpan</h3>
                        <p class="mt-1 text-sm text-slate-700">
                            <strong>{{ selectedSurahName }}</strong> ayat <strong>{{ form.verse_number || '—' }}</strong>
                            (halaman akan terisi otomatis dari posisi tersebut)
                        </p>
                    </section>

                    <!-- Submit -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Simpan Progress' }}
                        </button>
                        <Link
                            :href="route('dashboard')"
                            class="text-sm font-medium text-slate-500 hover:text-slate-700"
                        >
                            Batal
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
