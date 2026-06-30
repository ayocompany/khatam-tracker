<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const surahs = computed(() => page.props.surahs ?? []);

const form = ref({
    title: 'Khatam 30 Juz',
    target_type: 'daily_pages',
    target_value: 2,
    start_choice: 'from_beginning',
    start_surah_id: null,
    start_verse_number: null,
});

const errors = ref({});
const submitting = ref(false);

const startSurahId = computed(() => Number(form.value.start_surah_id));

const selectedSurah = computed(() => {
    if (!form.value.start_surah_id) return null;
    return surahs.value.find(s => s.id === Number(form.value.start_surah_id)) ?? null;
});

function submit() {
    submitting.value = true;
    errors.value = {};

    router.post(route('onboarding.store'), form.value, {
        preserveScroll: true,
        onError: (errs) => {
            errors.value = errs;
            submitting.value = false;
        },
        onSuccess: () => {
            // redirect handled server side
        },
        onFinish: () => { submitting.value = false; },
    });
}

function resetStartSurah() {
    form.value.start_surah_id = null;
    form.value.start_verse_number = null;
}
</script>

<template>
    <Head title="Mulai Program Khatam" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Mulai Program Khatam
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Info -->
                <section class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-cyan-50 p-5">
                    <p class="text-sm font-medium text-emerald-800">
                        Assalamu'alaikum! 🎉
                    </p>
                    <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                        Sebelum mulai, kami perlu tahu target khatam dan posisi bacaan kamu saat ini.
                        Data ini yang akan jadi acuan progress tilawah kamu ke depannya.
                    </p>
                </section>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nama Program -->
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">Nama Program</h3>
                        <p class="mt-1 text-xs text-slate-500">Beri label agar mudah dikenali.</p>
                        <input
                            v-model="form.title"
                            type="text"
                            maxlength="150"
                            class="mt-3 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                            placeholder="Misal: Khatam 30 Juz"
                        />
                        <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title }}</p>
                    </section>

                    <!-- Target -->
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">Target Khatam</h3>
                        <p class="mt-1 text-xs text-slate-500">Bagaimana cara kamu mengatur target?</p>

                        <div class="mt-4 space-y-3">
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 cursor-pointer">
                                <input
                                    type="radio"
                                    value="daily_pages"
                                    v-model="form.target_type"
                                    class="mt-0.5 text-emerald-500 focus:ring-emerald-400"
                                />
                                <div>
                                    <p class="text-sm font-medium text-slate-800">Target halaman per hari</p>
                                    <p class="text-xs text-slate-500">Tentukan target jumlah halaman yang ingin dibaca setiap hari.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 cursor-pointer">
                                <input
                                    type="radio"
                                    value="daily_verses"
                                    v-model="form.target_type"
                                    class="mt-0.5 text-emerald-500 focus:ring-emerald-400"
                                />
                                <div>
                                    <p class="text-sm font-medium text-slate-800">Target ayat per hari</p>
                                    <p class="text-xs text-slate-500">Tentukan target jumlah ayat yang ingin dibaca setiap hari.</p>
                                </div>
                            </label>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-slate-700">
                                {{
                                    form.target_type === 'daily_pages'
                                        ? 'Jumlah halaman per hari'
                                        : 'Jumlah ayat per hari'
                                }}
                            </label>
                            <input
                                v-model="form.target_value"
                                type="number"
                                min="1"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400 sm:max-w-xs"
                                :placeholder="form.target_type === 'daily_pages' ? '2' : '10'"
                            />
                            <p v-if="errors.target_type" class="mt-1 text-xs text-red-500">{{ errors.target_type }}</p>
                            <p v-if="errors.target_value" class="mt-1 text-xs text-red-500">{{ errors.target_value }}</p>
                        </div>
                    </section>

                    <!-- Posisi Awal -->
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-slate-900">Posisi Awal Bacaan</h3>
                        <p class="mt-1 text-xs text-slate-500">Kamu sudah membaca sampai mana?</p>

                        <div class="mt-4 space-y-3">
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 cursor-pointer">
                                <input
                                    type="radio"
                                    value="from_beginning"
                                    v-model="form.start_choice"
                                    class="mt-0.5 text-emerald-500 focus:ring-emerald-400"
                                    @change="resetStartSurah"
                                />
                                <div>
                                    <p class="text-sm font-medium text-slate-800">Mulai dari awal</p>
                                    <p class="text-xs text-slate-500">Mulai dari Al-Fatihah ayat 1.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 cursor-pointer">
                                <input
                                    type="radio"
                                    value="from_position"
                                    v-model="form.start_choice"
                                    class="mt-0.5 text-emerald-500 focus:ring-emerald-400"
                                />
                                <div>
                                    <p class="text-sm font-medium text-slate-800">Sudah di posisi tertentu</p>
                                    <p class="text-xs text-slate-500">Aku sudah membaca sampai surah dan ayat tertentu.</p>
                                </div>
                            </label>
                        </div>

                        <div v-if="form.start_choice === 'from_position'" class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="start-surah" class="block text-sm font-medium text-slate-700">Surah</label>
                                <select
                                    id="start-surah"
                                    v-model="form.start_surah_id"
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                                >
                                    <option :value="null" disabled>Pilih surah...</option>
                                    <option
                                        v-for="s in surahs"
                                        :key="s.id"
                                        :value="s.id"
                                    >
                                        {{ s.id }}. {{ s.name_id }}
                                    </option>
                                </select>
                                <p v-if="errors.start_surah_id" class="mt-1 text-xs text-red-500">{{ errors.start_surah_id }}</p>
                            </div>
                            <div>
                                <label for="start-verse" class="block text-sm font-medium text-slate-700">Ayat</label>
                                <input
                                    id="start-verse"
                                    v-model="form.start_verse_number"
                                    type="number"
                                    min="1"
                                    :max="selectedSurah?.total_verses ?? 9999"
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                                    placeholder="1"
                                />
                                <p v-if="errors.start_verse_number" class="mt-1 text-xs text-red-500">{{ errors.start_verse_number }}</p>
                                <p v-if="selectedSurah" class="mt-1 text-xs text-slate-400">
                                    Maks: {{ selectedSurah.total_verses }} ayat
                                </p>
                            </div>
                        </div>

                        <p v-if="errors.start_choice" class="mt-1 text-xs text-red-500">{{ errors.start_choice }}</p>
                    </section>

                    <!-- Submit -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Mulai Program Khatam' }}
                        </button>
                        <p v-if="errors.title" class="text-xs text-red-500">{{ errors.title }}</p>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
