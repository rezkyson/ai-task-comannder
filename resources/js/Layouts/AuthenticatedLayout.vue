<script setup>
import { ref } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, ListTodo, Plus, User, LogOut, Menu, X } from 'lucide-vue-next';

const showMobileMenu = ref(false);
const showProfileMenu = ref(false);

const user = usePage().props.auth.user;

const navLinks = [
    { name: 'Dashboard', route: 'dashboard', match: 'dashboard', icon: LayoutDashboard },
    { name: 'Tugas', route: 'tasks.index', match: 'tasks.*', icon: ListTodo },
];

const isActive = (match) => route().current(match);

const closeProfileMenu = () => showProfileMenu.value = false;
</script>

<template>
    <div>
        <FlashMessage />
        <div class="min-h-screen bg-gray-50/50 dark:bg-gray-950 transition-colors duration-200">

            <nav
                class="sticky top-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-b border-gray-100 dark:border-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-14 items-center justify-between">

                        <div class="flex items-center gap-6">
                            <!-- Logo -->
                            <Link :href="route('dashboard')" class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-md bg-violet-600 flex items-center justify-center">
                                    <svg viewBox="0 0 16 16" fill="none" class="w-3.5 h-3.5">
                                        <path d="M3 8.5L6.5 12L13 4" stroke="white" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <span
                                    class="font-semibold text-sm text-gray-900 dark:text-white tracking-tight hidden sm:block">TaskCommander</span>
                            </Link>

                            <!-- Desktop Nav -->
                            <div class="hidden sm:flex items-center gap-0.5">
                                <Link v-for="link in navLinks" :key="link.route" :href="route(link.route)"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors"
                                    :class="isActive(link.match)
                                        ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'
                                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                                    <component :is="link.icon" :size="14" :stroke-width="2" />
                                    {{ link.name }}
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <DarkModeToggle />

                            <!-- Quick Add -->
                            <Link :href="route('tasks.create')"
                                class="hidden sm:flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-violet-600 hover:bg-violet-700 text-white text-[13px] font-medium transition-colors">
                                <Plus :size="14" :stroke-width="2.5" />
                                <span>Buat</span>
                            </Link>

                            <!-- Profile -->
                            <div class="hidden sm:block relative">
                                <button @click="showProfileMenu = !showProfileMenu"
                                    @blur="setTimeout(closeProfileMenu, 150)"
                                    class="flex items-center gap-2 px-2 py-1.5 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <div
                                        class="w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="text-[13px] text-gray-600 dark:text-gray-300 max-w-[100px] truncate">{{
                                        user.name }}</span>
                                </button>

                                <Transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75" leave-from-class="opacity-100"
                                    leave-to-class="opacity-0 scale-95">
                                    <div v-if="showProfileMenu"
                                        class="absolute right-0 mt-1.5 w-48 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg py-1 z-50">
                                        <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-[13px] font-medium text-gray-900 dark:text-white truncate">{{
                                                user.name }}</p>
                                            <p class="text-[11px] text-gray-400 truncate">{{ user.email }}</p>
                                        </div>
                                        <Link :href="route('profile.edit')"
                                            class="flex items-center gap-2 px-3 py-2 text-[13px] text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <User :size="14" />
                                            Profil
                                        </Link>
                                        <Link :href="route('logout')" method="post" as="button"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                            <LogOut :size="14" />
                                            Keluar
                                        </Link>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Mobile toggle -->
                            <button @click="showMobileMenu = !showMobileMenu"
                                class="sm:hidden p-1.5 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <X v-if="showMobileMenu" :size="18" />
                                <Menu v-else :size="18" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <Transition enter-active-class="transition ease-out duration-150"
                    enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                    leave-to-class="opacity-0 -translate-y-1">
                    <div v-if="showMobileMenu"
                        class="sm:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 pb-3">
                        <div class="px-3 pt-3 space-y-0.5">
                            <Link v-for="link in navLinks" :key="link.route" :href="route(link.route)"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm font-medium transition-colors"
                                :class="isActive(link.match) ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'">
                                <component :is="link.icon" :size="16" />
                                {{ link.name }}
                            </Link>
                            <Link :href="route('tasks.create')"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm font-medium text-violet-600 dark:text-violet-400">
                                <Plus :size="16" />
                                Buat Tugas
                            </Link>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-800 mt-2 pt-2 px-3">
                            <div class="flex items-center gap-2.5 px-3 py-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400">{{ user.email }}</p>
                                </div>
                            </div>
                            <Link :href="route('profile.edit')"
                                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <User :size="16" /> Profil
                            </Link>
                            <Link :href="route('logout')" method="post" as="button"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">
                                <LogOut :size="16" /> Keluar
                            </Link>
                        </div>
                    </div>
                </Transition>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header"
                class="border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900/50">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
