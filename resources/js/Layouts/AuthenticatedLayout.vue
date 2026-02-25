<script setup>
import { ref } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showMobileMenu = ref(false);
const showProfileMenu = ref(false);

const user = usePage().props.auth.user;

const navLinks = [
    { name: 'Dashboard', route: 'dashboard', match: 'dashboard' },
    { name: 'Tugas', route: 'tasks.index', match: 'tasks.*' },
];

const isActive = (match) => route().current(match);

const closeProfileMenu = () => {
    showProfileMenu.value = false;
};
</script>

<template>
    <div>
        <FlashMessage />
        <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
            <!-- Navbar -->
            <nav
                class="sticky top-0 z-40 backdrop-blur-xl bg-white/80 dark:bg-slate-900/80 border-b border-slate-200/60 dark:border-slate-700/60">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Left: Logo + Nav -->
                        <div class="flex items-center gap-8">
                            <Link :href="route('dashboard')" class="flex items-center gap-2.5 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm shadow-indigo-200 dark:shadow-indigo-900/50 group-hover:shadow-md group-hover:shadow-indigo-300 dark:group-hover:shadow-indigo-800/50 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        class="w-4 h-4">
                                        <path d="m5 12 5 5L20 7" />
                                    </svg>
                                </div>
                                <span
                                    class="text-base font-bold text-slate-800 dark:text-white hidden sm:block">TaskCommander</span>
                            </Link>

                            <!-- Desktop Nav -->
                            <div class="hidden sm:flex items-center gap-1">
                                <Link v-for="link in navLinks" :key="link.route" :href="route(link.route)"
                                    class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all"
                                    :class="isActive(link.match)
                                        ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400'
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800'">
                                    {{ link.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Right -->
                        <div class="flex items-center gap-3">
                            <DarkModeToggle />

                            <Link :href="route('tasks.create')"
                                class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium transition-all shadow-sm hover:shadow-md hover:shadow-indigo-200 dark:hover:shadow-indigo-900/50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                Tambah
                            </Link>

                            <!-- Profile Dropdown -->
                            <div class="hidden sm:block relative">
                                <button @click="showProfileMenu = !showProfileMenu"
                                    @blur="setTimeout(closeProfileMenu, 150)"
                                    class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                    <div
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span
                                        class="text-sm font-medium text-slate-600 dark:text-slate-300 max-w-[120px] truncate">{{
                                        user.name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400">
                                        <polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </button>

                                <Transition enter-active-class="transition ease-out duration-150"
                                    enter-from-class="opacity-0 scale-95 -translate-y-1"
                                    enter-to-class="opacity-100 scale-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-100"
                                    leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                                    <div v-if="showProfileMenu"
                                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg shadow-slate-200/50 dark:shadow-black/30 py-1.5 z-50">
                                        <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700">
                                            <p
                                                class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                                {{ user.name }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ user.email }}</p>
                                        </div>
                                        <Link :href="route('profile.edit')"
                                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-white transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="w-4 h-4">
                                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                                <circle cx="12" cy="7" r="4" />
                                            </svg>
                                            Profil
                                        </Link>
                                        <Link :href="route('logout')" method="post" as="button"
                                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="w-4 h-4">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                                <polyline points="16 17 21 12 16 7" />
                                                <line x1="21" y1="12" x2="9" y2="12" />
                                            </svg>
                                            Keluar
                                        </Link>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Mobile Hamburger -->
                            <button @click="showMobileMenu = !showMobileMenu"
                                class="sm:hidden p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-colors">
                                <svg v-if="!showMobileMenu" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="w-5 h-5">
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                    <line x1="3" y1="12" x2="21" y2="12" />
                                    <line x1="3" y1="18" x2="21" y2="18" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="w-5 h-5">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <Transition enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100"
                    leave-to-class="opacity-0 -translate-y-2">
                    <div v-if="showMobileMenu"
                        class="sm:hidden border-t border-slate-200/60 dark:border-slate-700/60 bg-white dark:bg-slate-900">
                        <div class="px-4 py-3 space-y-1">
                            <Link v-for="link in navLinks" :key="link.route" :href="route(link.route)"
                                class="block px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="isActive(link.match)
                                    ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400'
                                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'">
                                {{ link.name }}
                            </Link>
                            <Link :href="route('tasks.create')"
                                class="block px-3 py-2.5 rounded-lg text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/15 transition-colors">
                                + Tambah Tugas
                            </Link>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-700 px-4 py-3">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ user.name }}
                                    </p>
                                    <p class="text-xs text-slate-400">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <Link :href="route('profile.edit')"
                                    class="block px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    Profil
                                </Link>
                                <Link :href="route('logout')" method="post" as="button"
                                    class="w-full text-left block px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    Keluar
                                </Link>
                            </div>
                        </div>
                    </div>
                </Transition>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header"
                class="bg-white dark:bg-slate-800/50 border-b border-slate-200/60 dark:border-slate-700/60">
                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
