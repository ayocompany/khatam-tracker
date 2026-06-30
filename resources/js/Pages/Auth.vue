<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, ref, watch } from 'vue';

const page = usePage();

const sendForm = useForm({
    phone: '',
});

const verifyForm = useForm({
    phone: '',
    otp: '',
});

const authState = computed(() => page.props.flash?.auth_state ?? null);
const authMessage = computed(() => authState.value?.message ?? '');

const forceVerifyStep = ref(false);
const isVerifyStep = computed(() => forceVerifyStep.value || authState.value?.step === 'verify');

const otpDigits = ref(['', '', '', '', '', '']);
const otpRefs = ref([]);

const syncOtpFromDigits = () => {
    verifyForm.otp = otpDigits.value.join('');
};

const syncDigitsFromOtp = (otpValue) => {
    const digitsOnly = String(otpValue ?? '').replace(/\D/g, '').slice(0, 6);
    otpDigits.value = Array.from({ length: 6 }, (_, i) => digitsOnly[i] ?? '');
    verifyForm.otp = digitsOnly;
};

watch(
    authState,
    (state) => {
        if (!state) return;

        if (state.phone) {
            sendForm.phone = state.phone;
            verifyForm.phone = state.phone;
        }

        verifyForm.otp = '';
        otpDigits.value = ['', '', '', '', '', ''];

        if (state.step === 'verify') {
            forceVerifyStep.value = true;
            nextTick(() => {
                otpRefs.value[0]?.focus();
            });
        }
    },
    { immediate: true }
);

const phoneError = computed(() => sendForm.errors.phone || verifyForm.errors.phone);
const otpError = computed(() => verifyForm.errors.otp);

const canSendOtp = computed(() => {
    const phone = (sendForm.phone ?? '').trim();
    return phone.length >= 9;
});

const canVerifyOtp = computed(() => {
    return ((verifyForm.phone ?? '').trim().length >= 9) && ((verifyForm.otp ?? '').trim().length === 6);
});

const focusOtpIndex = (index) => {
    if (index < 0 || index > 5) return;
    otpRefs.value[index]?.focus();
};

const onOtpInput = (index, event) => {
    const raw = String(event.target.value ?? '');
    const value = raw.replace(/\D/g, '');

    if (value.length > 1) {
        const pasted = value.slice(0, 6);
        otpDigits.value = Array.from({ length: 6 }, (_, i) => pasted[i] ?? '');
        syncOtpFromDigits();

        const nextIndex = Math.min(pasted.length, 5);
        nextTick(() => focusOtpIndex(nextIndex));
        return;
    }

    otpDigits.value[index] = value.slice(0, 1);
    syncOtpFromDigits();

    if (otpDigits.value[index] && index < 5) {
        nextTick(() => focusOtpIndex(index + 1));
    }
};

const onOtpKeydown = (index, event) => {
    if (event.key === 'Backspace') {
        if (!otpDigits.value[index] && index > 0) {
            otpDigits.value[index - 1] = '';
            syncOtpFromDigits();
            nextTick(() => focusOtpIndex(index - 1));
            event.preventDefault();
        }
        return;
    }

    if (event.key === 'ArrowLeft' && index > 0) {
        nextTick(() => focusOtpIndex(index - 1));
        event.preventDefault();
    }

    if (event.key === 'ArrowRight' && index < 5) {
        nextTick(() => focusOtpIndex(index + 1));
        event.preventDefault();
    }
};

const onOtpPaste = (event) => {
    const pasted = String(event.clipboardData?.getData('text') ?? '')
        .replace(/\D/g, '')
        .slice(0, 6);

    if (!pasted) return;

    event.preventDefault();
    syncDigitsFromOtp(pasted);

    const nextIndex = Math.min(pasted.length, 5);
    nextTick(() => focusOtpIndex(nextIndex));
};

const submitSendOtp = () => {
    verifyForm.clearErrors();

    sendForm.post(route('auth.otp.send'), {
        preserveScroll: true,
        onSuccess: () => {
            forceVerifyStep.value = true;
            verifyForm.phone = sendForm.phone;
            verifyForm.otp = '';
            otpDigits.value = ['', '', '', '', '', ''];

            nextTick(() => {
                focusOtpIndex(0);
            });
        },
    });
};

const submitVerifyOtp = () => {
    sendForm.clearErrors();
    verifyForm.post(route('auth.otp.verify'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Login - Quran Khatam Tracker" />

    <div class="min-h-screen bg-gradient-to-b from-emerald-50 via-cyan-50 to-white text-slate-800">
        <div class="mx-auto flex min-h-screen w-full max-w-md flex-col px-5 pb-10 pt-6 sm:max-w-xl">
            <header class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/20 ring-1 ring-emerald-300/60">
                        <span class="text-xl">☪️</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium tracking-wide text-emerald-700">Bismillah, selamat datang</p>
                        <h1 class="text-xl font-bold leading-tight text-slate-900">Masuk ke Quran Khatam Tracker</h1>
                    </div>
                </div>

                <Link
                    :href="route('welcome')"
                    class="rounded-lg border border-emerald-200 bg-white/90 px-3 py-2 text-sm font-medium text-emerald-700 hover:border-emerald-400 hover:text-emerald-800"
                >
                    Kembali
                </Link>
            </header>

            <main class="mt-6 flex-1 space-y-4">
                <section class="rounded-2xl bg-white/80 p-4 ring-1 ring-emerald-200/70 backdrop-blur-sm">
                    <p class="text-sm leading-relaxed text-slate-700">
                        <span class="font-semibold text-emerald-700">“Sebaik-baik kalian adalah yang belajar Al-Qur’an dan mengajarkannya.”</span>
                    </p>
                    <p class="mt-2 text-xs text-slate-500">
                        Login cepat dengan OTP WhatsApp untuk lanjutkan progres tilawah harian.
                    </p>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
                    <div class="space-y-4">
                        <div v-if="authMessage" class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                            {{ authMessage }}
                        </div>

                        <p class="text-xs font-semibold tracking-wide text-emerald-700">Langkah 1 · Kirim OTP</p>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-700">Nomor WhatsApp</label>
                            <input
                                v-model="sendForm.phone"
                                type="tel"
                                inputmode="numeric"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                            />
                            <p v-if="phoneError" class="text-xs text-rose-600">{{ phoneError }}</p>
                        </div>

                        <button
                            type="button"
                            class="w-full rounded-xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="sendForm.processing || !canSendOtp"
                            @click="submitSendOtp"
                        >
                            {{ sendForm.processing ? 'Mengirim...' : 'Kirim OTP' }}
                        </button>
                    </div>
                </section>

                <section
                    v-if="isVerifyStep"
                    class="rounded-2xl border border-cyan-200 bg-white p-4 shadow-sm"
                >
                    <div class="space-y-4">
                        <p class="text-xs font-semibold tracking-wide text-cyan-700">Langkah 2 · Verifikasi OTP</p>
                        <p class="text-xs text-slate-500">
                            Masukkan 6 digit OTP yang masuk ke WhatsApp kamu. Bisa langsung paste.
                        </p>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-700">Kode OTP</label>

                            <div class="grid grid-cols-6 gap-2">
                                <input
                                    v-for="(_, index) in otpDigits"
                                    :key="index"
                                    :ref="(el) => { otpRefs[index] = el; }"
                                    :value="otpDigits[index]"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="h-12 w-full rounded-xl border border-slate-300 bg-white text-center text-lg font-semibold outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                                    @input="onOtpInput(index, $event)"
                                    @keydown="onOtpKeydown(index, $event)"
                                    @paste="onOtpPaste"
                                />
                            </div>

                            <p v-if="otpError" class="text-xs text-rose-600">{{ otpError }}</p>
                        </div>

                        <button
                            type="button"
                            class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="verifyForm.processing || !canVerifyOtp"
                            @click="submitVerifyOtp"
                        >
                            {{ verifyForm.processing ? 'Memverifikasi...' : 'Verifikasi OTP & Lanjut' }}
                        </button>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white/90 p-4">
                    <h2 class="text-sm font-semibold text-slate-800">Pengingat hari ini</h2>
                    <ul class="mt-2 space-y-1 text-xs text-slate-600">
                        <li>• Niatkan tilawah karena Allah.</li>
                        <li>• Sedikit tapi konsisten lebih baik.</li>
                        <li>• Catat progres agar tetap istiqamah.</li>
                    </ul>
                </section>
            </main>

            <footer class="mt-8 text-center">
                <p class="text-xs text-slate-500">
                    Semoga Allah mudahkan langkah kita dalam berinteraksi dengan Al-Qur’an.
                </p>
            </footer>
        </div>
    </div>
</template>
