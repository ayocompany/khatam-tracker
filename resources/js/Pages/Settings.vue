<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const layouts = computed(() => page.props.layouts ?? []);
const currentLayout = computed(() => page.props.currentLayout ?? null);
const initialReminderTimes = computed(() => page.props.reminderTimes ?? []);
const initialWaReminderEnabled = computed(() => page.props.waReminderEnabled ?? false);
const userPhone = computed(() => page.props.auth?.user?.phone ?? '');

const selectedLayoutCode = ref(currentLayout.value?.code ?? '');
const updating = ref(false);

function updateLayout() {
    if (!selectedLayoutCode.value || selectedLayoutCode.value === currentLayout.value?.code) return;
    updating.value = true;
    router.post(route('settings.updateLayout'), {
        layout_code: selectedLayoutCode.value,
    }, {
        preserveScroll: true,
        onFinish: () => { updating.value = false; },
    });
}

const selectedLayoutInfo = computed(() => {
    return layouts.value.find(l => l.code === selectedLayoutCode.value) ?? null;
});

// ---- Reminder (WA) ----
const reminderTimes = ref([...initialReminderTimes.value]);
const waReminderEnabled = ref(initialWaReminderEnabled.value);
const saving = ref(false);

function addReminderTime() {
    if (reminderTimes.value.length >= 5) return;
    const now = new Date();
    const h = String((now.getHours() + 1) % 24).padStart(2, '0');
    reminderTimes.value.push(`${h}:00`);
}

function removeReminderTime(index) {
    reminderTimes.value.splice(index, 1);
}

const resetting = ref(false);

function resetAll() {
    if (!confirm('Yakin reset semua progress? Program, progress, dan log harian akan dihapus. Kamu harus membuat program baru dari onboarding.')) return;
    resetting.value = true;
    router.post(route('settings.resetAll'), {}, {
        onFinish: () => { resetting.value = false; },
    });
}

function save() {
    saving.value = true;
    router.post(route('settings.updateReminders'), {
        reminder_times: reminderTimes.value,
        wa_reminder_enabled: waReminderEnabled.value,
    }, {
        preserveScroll: true,
        onFinish: () => { saving.value = false; },
    });
}
</script>

<template>
    <Head title="Pengaturan - Quran Khatam Tracker" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Pengaturan
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Layout Mushaf -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Jenis Mushaf (Layout Al-Qur'an)</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Pilih jenis mushaf yang sesuai dengan Al-Qur'an yang kamu baca sehari-hari.
                        Ini penting agar perhitungan halaman dan progress tilawah akurat.
                    </p>

                    <div class="mt-4 flex flex-wrap items-end gap-3">
                        <div class="min-w-0 flex-1 sm:max-w-xs">
                            <label for="settings-layout" class="block text-xs font-medium text-slate-600">Pilih Mushaf</label>
                            <select
                                id="settings-layout"
                                v-model="selectedLayoutCode"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                            >
                                <option value="" disabled>Pilih mushaf...</option>
                                <option
                                    v-for="layout in layouts"
                                    :key="layout.code"
                                    :value="layout.code"
                                >
                                    {{ layout.name }}
                                </option>
                            </select>
                        </div>
                        <button
                            type="button"
                            :disabled="updating || selectedLayoutCode === (currentLayout?.code ?? '')"
                            class="rounded-lg bg-emerald-500 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="updateLayout"
                        >
                            {{ updating ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>

                    <!-- Info layout terpilih -->
                    <div v-if="selectedLayoutInfo" class="mt-5 grid gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 sm:grid-cols-4">
                        <div>
                            <p class="text-xs text-emerald-700">Baris / Halaman</p>
                            <p class="text-lg font-bold text-emerald-800">{{ selectedLayoutInfo.lines_per_page }} baris</p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-700">Total Halaman</p>
                            <p class="text-lg font-bold text-emerald-800">{{ selectedLayoutInfo.total_pages }} hlm</p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-700">Total Surat</p>
                            <p class="text-lg font-bold text-emerald-800">{{ selectedLayoutInfo.total_surahs }} surah</p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-700">Total Ayat</p>
                            <p class="text-lg font-bold text-emerald-800">{{ selectedLayoutInfo.total_verses }} ayat</p>
                        </div>
                    </div>
                </section>

                <!-- Perbedaan Layout -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Perbedaan Jenis Mushaf</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Meskipun isi Al-Qur'an sama (114 surah, 6236 ayat, 604 halaman), tata letak per halaman berbeda antar mushaf.
                    </p>

                    <div class="mt-4 space-y-4">
                        <!-- Madinah 15 -->
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="font-semibold text-slate-900">Mushaf Madinah (15 baris)</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                Standar cetakan Arab Saudi (Mujamma' Malik Fahd). <strong>15 baris per halaman</strong>.
                                Paling banyak digunakan di dunia. Setiap juz terdiri dari 20 halaman (genap).
                                Pojok halaman selalu berakhiran ayat genap.
                            </p>
                        </div>

                        <!-- Madinah 13 -->
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="font-semibold text-slate-900">Mushaf Madinah (13 baris)</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                Sama dengan Mushaf Madinah standar, tapi <strong>13 baris per halaman</strong>.
                                Font lebih besar dan lega, nyaman untuk lansia atau pemula.
                                Jumlah halaman tetap 604, jadi komposisi per halaman berbeda dari versi 15 baris.
                            </p>
                        </div>

                        <!-- Indonesia -->
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="font-semibold text-slate-900">Mushaf Indonesia</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                <strong>15 baris per halaman</strong>. Cetakan populer di Indonesia (Kemenag, Syamil, Diponegoro, dll).
                                Menggunakan Rasm Usmani standar. Posisi juz dan hizb mengikuti standar Mushaf Madinah.
                                Ini yang paling umum dipakai di pesantren dan masjid Indonesia.
                            </p>
                        </div>

                        <!-- Pakistan/India -->
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="font-semibold text-slate-900">Mushaf Pakistan / India</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                <strong>13 baris per halaman</strong>. Banyak beredar di anak benua India-Pakistan.
                                Perbedaan utama: penanda juz dan hizb di margin berbeda dari Mushaf Madinah.
                                Beberapa cetakan menggunakan rasm yang sedikit berbeda (Imla'i).
                            </p>
                        </div>

                        <!-- Timur Tengah 17 -->
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="font-semibold text-slate-900">Mushaf Timur Tengah (17 baris)</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                <strong>17 baris per halaman</strong>. Ukuran compact, banyak dipakai di masjid-masjid Timur Tengah
                                dan mushaf saku/ukuran kecil. Dengan 17 baris, font lebih kecil tapi sekali duduk bisa lebih banyak ayat.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-cyan-200 bg-cyan-50 p-4">
                        <p class="text-sm font-medium text-cyan-800">💡 Catatan Penting</p>
                        <ul class="mt-2 space-y-1 text-sm text-cyan-700">
                            <li>• Semua mushaf memiliki <strong>604 halaman, 114 surah, 6236 ayat</strong> — isinya sama.</li>
                            <li>• Yang berbeda: <strong>jumlah baris per halaman</strong> (13/15/17) dan <strong>posisi akhir/awal juz</strong> di halaman tertentu.</li>
                            <li>• Jika mushaf kamu tidak ada di daftar, pilih yang paling mendekati jumlah baris per halamannya.</li>
                        </ul>
                    </div>
                </section>

                <!-- Pengingat Baca Al-Qur'an (WhatsApp) -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">📖 Pengingat Tilawah Harian</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Atur jadwal pengingat tilawah harian via WhatsApp. Pesan akan dikirim otomatis ke nomor akun kamu setiap waktu yang dipilih.
                    </p>

                    <div class="mt-4 space-y-4">
                        <!-- Waktu -->
                        <div>
                            <p class="text-xs font-medium text-slate-600">Waktu pengingat</p>
                            <div class="mt-2 space-y-2">
                                <div
                                    v-for="(time, index) in reminderTimes"
                                    :key="index"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        type="time"
                                        v-model="reminderTimes[index]"
                                        class="block w-40 rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                                    />
                                    <button
                                        type="button"
                                        class="rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50"
                                        @click="removeReminderTime(index)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            <button
                                v-if="reminderTimes.length < 5"
                                type="button"
                                class="mt-2 text-sm font-medium text-emerald-600 hover:text-emerald-500"
                                @click="addReminderTime"
                            >
                                + Tambah waktu
                            </button>
                            <p class="mt-1 text-xs text-slate-400">Maksimal 5 waktu. Pengingat dikirim setiap hari pada waktu yang dipilih.</p>
                        </div>

                        <!-- Toggle WA -->
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    v-model="waReminderEnabled"
                                    class="h-5 w-5 rounded border-slate-300 text-emerald-500 focus:ring-emerald-400"
                                />
                                <span class="text-sm text-slate-700">Kirim via WhatsApp</span>
                            </label>
                        </div>

                        <!-- Nomor tujuan (read-only) -->
                        <div v-if="waReminderEnabled" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-medium text-slate-600">Nomor tujuan</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ userPhone || '—' }}</p>
                            <p class="mt-1 text-xs text-slate-400">Menggunakan nomor yang terdaftar di akun kamu.</p>
                        </div>

                        <!-- Simpan -->
                        <button
                            type="button"
                            :disabled="saving"
                            class="rounded-lg bg-emerald-500 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="save"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </div>
                </section>

                <!-- Reset Semua Progress -->
                <section class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-red-700">⚠️ Reset Semua Progress</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Hapus seluruh program khatam, progress bacaan, dan log harian.
                        Kamu akan diarahkan ke halaman onboarding untuk memulai dari awal.
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Riwayat khatam tidak akan dihapus. Total khatam saat ini: <strong>{{ page.props.khatamCount ?? 0 }}</strong> kali.
                    </p>

                    <button
                        type="button"
                        class="mt-4 rounded-lg bg-red-500 px-5 py-2 text-sm font-semibold text-white hover:bg-red-400"
                        @click="resetAll"
                    >
                        Reset Semua Progress
                    </button>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
