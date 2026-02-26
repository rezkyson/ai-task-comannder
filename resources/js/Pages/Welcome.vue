<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { Sparkles, CheckCircle2, Brain, Clock, Shield, Zap, ArrowRight, ListTodo, BarChart3, Globe } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>

    <Head title="Kelola Tugas Lebih Cerdas" />

    <div class="min-h-screen bg-[#f8f9fc] dark:bg-gray-950 transition-colors duration-200 relative overflow-x-hidden">

        <!-- Background decorative -->
        <div class="fixed inset-0 pointer-events-none" aria-hidden="true">
            <div
                class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full bg-violet-400/15 dark:bg-violet-500/[0.07] blur-[100px]">
            </div>
            <div
                class="absolute top-[60%] -left-24 w-[500px] h-[500px] rounded-full bg-sky-400/10 dark:bg-sky-500/[0.05] blur-[100px]">
            </div>
            <div
                class="absolute bottom-0 right-[20%] w-[400px] h-[400px] rounded-full bg-fuchsia-400/10 dark:bg-fuchsia-500/[0.05] blur-[100px]">
            </div>
        </div>
        <div class="fixed inset-0 bg-dot-pattern opacity-[0.4] dark:opacity-[0.06] pointer-events-none"
            aria-hidden="true"></div>

        <!-- Navbar -->
        <nav
            class="sticky top-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-b border-gray-100 dark:border-gray-800">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 h-14 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-violet-600 flex items-center justify-center">
                        <svg viewBox="0 0 16 16" fill="none" class="w-3.5 h-3.5">
                            <path d="M3 8.5L6.5 12L13 4" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span
                        class="font-semibold text-sm text-gray-900 dark:text-white tracking-tight">TaskCommander</span>
                </Link>

                <div class="flex items-center gap-2">
                    <DarkModeToggle />
                    <template v-if="canLogin">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-3 py-1.5 text-[13px] font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-3 py-1.5 text-[13px] font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                                Masuk
                            </Link>
                            <Link v-if="canRegister" :href="route('register')"
                                class="px-3 py-1.5 rounded-md bg-violet-600 hover:bg-violet-700 text-white text-[13px] font-medium transition-colors">
                                Daftar
                            </Link>
                        </template>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="relative pt-16 sm:pt-24 pb-16 px-4 sm:px-6">
            <div class="mx-auto max-w-3xl text-center">
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-violet-50 dark:bg-violet-500/10 border border-violet-100 dark:border-violet-500/20 text-violet-600 dark:text-violet-400 text-[12px] font-medium mb-6">
                    <Sparkles :size="12" />
                    Didukung AI Generatif
                </div>

                <h1 class="text-3xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight">
                    Tulis tugasmu,
                    <br />
                    <span class="text-violet-600 dark:text-violet-400">biarkan AI yang mengatur</span>
                </h1>

                <p class="mt-5 text-base sm:text-lg text-gray-500 dark:text-gray-400 max-w-xl mx-auto leading-relaxed">
                    Cukup ketik dalam bahasa sehari-hari. AI kami akan otomatis mengisi judul, kategori, prioritas, dan
                    deadline. Manajemen tugas belum pernah semudah ini.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8">
                    <Link v-if="canRegister" :href="route('register')"
                        class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium transition-colors">
                        Mulai Gratis
                        <ArrowRight :size="15" />
                    </Link>
                    <Link v-if="canLogin" :href="route('login')"
                        class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Sudah punya akun
                    </Link>
                </div>
            </div>
        </section>

        <!-- Demo Preview -->
        <section class="relative pb-16 px-4 sm:px-6">
            <div class="mx-auto max-w-2xl">
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-lg shadow-gray-200/50 dark:shadow-black/20 p-5">
                    <!-- Fake AI input -->
                    <div class="flex gap-2 items-end mb-4">
                        <div class="flex-1 relative">
                            <div class="absolute left-3 top-2.5 text-violet-500">
                                <Sparkles :size="14" />
                            </div>
                            <div
                                class="w-full pl-8 pr-3 py-2 text-sm bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-200">
                                Meeting klien besok jam 10, prioritas tinggi
                            </div>
                        </div>
                        <div class="h-9 px-3 rounded-lg bg-violet-600 flex items-center justify-center text-white">
                            <ArrowRight :size="15" />
                        </div>
                    </div>

                    <!-- Fake parsed result -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-[13px]">
                            <span class="text-gray-400 dark:text-gray-500 w-16">Judul</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">Meeting klien</span>
                        </div>
                        <div class="flex items-center gap-2 text-[13px]">
                            <span class="text-gray-400 dark:text-gray-500 w-16">Kategori</span>
                            <span
                                class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">Pekerjaan</span>
                        </div>
                        <div class="flex items-center gap-2 text-[13px]">
                            <span class="text-gray-400 dark:text-gray-500 w-16">Prioritas</span>
                            <span
                                class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">Tinggi</span>
                        </div>
                        <div class="flex items-center gap-2 text-[13px]">
                            <span class="text-gray-400 dark:text-gray-500 w-16">Deadline</span>
                            <span class="text-gray-800 dark:text-gray-200 font-medium">Besok, 10:00</span>
                        </div>
                    </div>
                </div>
                <p class="text-center text-[11px] text-gray-400 dark:text-gray-600 mt-3">AI mengurai kalimat bebas
                    menjadi data
                    terstruktur</p>
            </div>
        </section>

        <!-- Features -->
        <section class="relative py-16 px-4 sm:px-6">
            <div class="mx-auto max-w-4xl">
                <div class="text-center mb-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Kenapa TaskCommander?</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Dibuat untuk orang yang menghargai waktu
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center mb-3">
                            <Brain :size="18" class="text-violet-600 dark:text-violet-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">AI Natural Language</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Ketik tugas dalam bahasa
                            Indonesia sehari-hari. AI memahami konteks, waktu, dan urgensi secara otomatis.</p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center mb-3">
                            <Zap :size="18" class="text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Cepat & Responsif</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Optimistic UI membuat
                            setiap
                            interaksi terasa instan. Tidak ada loading yang mengganggu alur kerja.</p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center mb-3">
                            <Clock :size="18" class="text-amber-600 dark:text-amber-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Deadline Tracking</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Pantau tugas yang
                            mendekati
                            deadline. Tidak ada lagi tugas yang terlewat tanpa disadari.</p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mb-3">
                            <BarChart3 :size="18" class="text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Dashboard Analitik</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Lihat progres,
                            statistik, dan
                            distribusi tugas per kategori dalam satu tampilan ringkas.</p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center mb-3">
                            <ListTodo :size="18" class="text-sky-600 dark:text-sky-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Filter & Organisasi</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Filter berdasarkan
                            status,
                            kategori, atau prioritas. Temukan tugas yang kamu butuhkan dalam hitungan detik.</p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-5 group hover:border-violet-200 dark:hover:border-violet-500/20 transition-colors">
                        <div
                            class="w-9 h-9 rounded-md bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center mb-3">
                            <Shield :size="18" class="text-rose-600 dark:text-rose-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Privat & Aman</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">Setiap tugas hanya bisa
                            diakses
                            oleh pemiliknya. Data kamu terlindungi dengan autentikasi standar industri.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="relative py-16 px-4 sm:px-6">
            <div class="mx-auto max-w-3xl">
                <div class="text-center mb-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Cara Kerjanya</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">3 langkah, kurang dari 10 detik</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div
                            class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-500/15 flex items-center justify-center mx-auto mb-3 text-violet-600 dark:text-violet-400 text-sm font-bold">
                            1</div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Ketik</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400">Tulis tugas dalam bahasa bebas seperti
                            chat
                            biasa</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-500/15 flex items-center justify-center mx-auto mb-3 text-violet-600 dark:text-violet-400 text-sm font-bold">
                            2</div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">AI Proses</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400">Gemini AI mengurai jadi judul,
                            prioritas,
                            deadline</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-500/15 flex items-center justify-center mx-auto mb-3 text-violet-600 dark:text-violet-400 text-sm font-bold">
                            3</div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Simpan</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400">Review hasil AI, edit jika perlu, dan
                            simpan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="relative py-16 px-4 sm:px-6">
            <div class="mx-auto max-w-xl text-center">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-8">
                    <CheckCircle2 :size="28" class="text-violet-600 dark:text-violet-400 mx-auto mb-3" />
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Siap mengatur tugasmu?</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-5">Gratis, tanpa kartu kredit, langsung
                        pakai.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <Link v-if="canRegister" :href="route('register')"
                            class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium transition-colors">
                            Daftar Sekarang
                            <ArrowRight :size="15" />
                        </Link>
                        <Link v-if="canLogin" :href="route('login')"
                            class="w-full sm:w-auto text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Atau masuk ke akun →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative py-8 px-4 sm:px-6 border-t border-gray-100 dark:border-gray-800">
            <div class="mx-auto max-w-5xl flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-violet-600 flex items-center justify-center">
                        <svg viewBox="0 0 16 16" fill="none" class="w-2.5 h-2.5">
                            <path d="M3 8.5L6.5 12L13 4" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">TaskCommander</span>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-600">Dibangun dengan Laravel, Vue, dan Gemini AI</p>
            </div>
        </footer>
    </div>
</template>
