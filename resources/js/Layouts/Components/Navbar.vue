<template>
    <header
        class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center gap-4 sticky top-0 z-20 app-header"
    >
        <!-- Hamburger (mobile only) -->
        <button
            type="button"
            @click="emit('toggle-sidebar')"
            :aria-label="showingSidebar ? $t('close_menu') : $t('open_menu')"
            :aria-expanded="showingSidebar"
            aria-controls="primary-sidebar"
            class="lg:hidden p-2 text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 transition-colors"
        >
            <Menu
                v-if="!showingSidebar"
                aria-hidden="true"
                class="h-5 w-5"
                :stroke-width="2"
            />
            <X
                v-else
                aria-hidden="true"
                class="h-5 w-5"
                :stroke-width="2"
            />
        </button>

        <!-- Page title slot -->
        <div class="flex-1 min-w-0">
            <slot name="title"></slot>
        </div>

        <!-- Right cluster -->
        <div class="flex items-center gap-2 lg:gap-3">
            <!-- Global search (auth + desktop only) -->
            <div v-if="user" class="relative hidden md:block">
                <label class="sr-only" for="nav-search">
                    {{ $t('search_placeholder') }}
                </label>
                <Search
                    aria-hidden="true"
                    class="h-4 w-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                    :stroke-width="2"
                />
                <input
                    id="nav-search"
                    type="search"
                    :placeholder="$t('search_placeholder')"
                    class="pl-9 pr-3 py-2 w-56 lg:w-72 rounded-full bg-slate-100 text-sm text-slate-700 placeholder:text-slate-400 border-0 focus:outline-none focus:ring-2 focus:ring-green-600 focus:bg-white transition-colors"
                />
            </div>

            <!-- Notifications bell -->
            <button
                v-if="user"
                type="button"
                :aria-label="$t('notifications')"
                class="relative p-2 rounded-full text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 transition-colors"
            >
                <Bell aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                <span
                    v-if="notificationCount > 0"
                    class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold leading-[18px] text-center"
                    aria-hidden="true"
                >
                    {{ notificationCount > 99 ? '99+' : notificationCount }}
                </span>
                <span v-if="notificationCount > 0" class="sr-only">
                    {{ notificationCount }} {{ $t('notifications') }}
                </span>
            </button>

            <!-- Language selector -->
            <LanguageSelector v-if="user" />

            <!-- User avatar with dropdown -->
            <div v-if="user" class="relative" ref="userMenuRef">
                <button
                    type="button"
                    @click="userMenuOpen = !userMenuOpen"
                    :aria-expanded="userMenuOpen"
                    aria-haspopup="menu"
                    :aria-label="$t('open_user_menu')"
                    class="flex items-center gap-2 p-1 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 hover:bg-slate-100 transition-colors"
                >
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-600 text-white text-sm font-semibold shadow-sm"
                        aria-hidden="true"
                    >
                        {{ initials }}
                    </span>
                    <ChevronDown
                        aria-hidden="true"
                        class="h-4 w-4 text-slate-400 transition-transform hidden sm:block"
                        :class="{ 'rotate-180': userMenuOpen }"
                        :stroke-width="2"
                    />
                </button>
                <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-if="userMenuOpen"
                        role="menu"
                        class="absolute right-0 mt-2 w-60 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5 focus:outline-none z-[100] py-1"
                    >
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-900 truncate">
                                {{ user.name }}
                            </p>
                            <p
                                v-if="user.email"
                                class="text-xs text-slate-500 truncate"
                            >
                                {{ user.email }}
                            </p>
                        </div>
                        <Link
                            :href="route('profile.edit')"
                            role="menuitem"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                            @click="userMenuOpen = false"
                        >
                            <UserCircle
                                aria-hidden="true"
                                class="h-4 w-4 text-slate-400"
                                :stroke-width="2"
                            />
                            {{ $t('profile') }}
                        </Link>
                        <Link
                            v-if="!isSingleLicenseMode && hasRole(['Super Admin', 'admin'])"
                            :href="route('admin.dashboard')"
                            role="menuitem"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                            @click="userMenuOpen = false"
                        >
                            <ShieldCheck
                                aria-hidden="true"
                                class="h-4 w-4 text-slate-400"
                                :stroke-width="2"
                            />
                            {{ $t('admin_dashboard') }}
                        </Link>
                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <button
                                type="button"
                                role="menuitem"
                                @click="logout"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors focus:outline-none focus-visible:bg-red-50"
                            >
                                <LogOut
                                    aria-hidden="true"
                                    class="h-4 w-4"
                                    :stroke-width="2"
                                />
                                {{ $t('logout') }}
                            </button>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Guest links -->
            <div v-if="!user" class="flex items-center gap-2">
                <Link
                    :href="route('login')"
                    class="text-sm text-slate-700 hover:text-slate-900 font-medium px-3 py-2 rounded-lg hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 transition-colors"
                >
                    {{ $t('login') }}
                </Link>
                <Link
                    :href="route('register')"
                    class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2"
                >
                    {{ $t('register') }}
                </Link>
            </div>

            <!-- External actions slot -->
            <div v-if="$slots.actions">
                <slot name="actions"></slot>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, ref, onMounted, onUnmounted } from "vue";
import LanguageSelector from "@/Components/LanguageSelector.vue";
import {
    Menu,
    X,
    Bell,
    Search,
    ChevronDown,
    UserCircle,
    ShieldCheck,
    LogOut,
} from "lucide-vue-next";

defineProps({
    showingSidebar: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["toggle-sidebar"]);

const page = usePage();
const user = computed(() => page.props.value.auth?.user ?? null);
const roles = computed(() => {
    const rawRoles = page.props.value.auth?.user?.roles ?? [];
    return rawRoles.map((r) => (typeof r === "string" ? r : r.name));
});

const isSingleLicenseMode = computed(
    () => !!page.props.value?.app_mode?.single_license_mode,
);

const hasRole = (roleName) => {
    if (Array.isArray(roleName)) {
        return roleName.some((r) => roles.value.includes(r));
    }
    return roles.value.includes(roleName);
};

const initials = computed(() => {
    const name = user.value?.name || "";
    const parts = name.trim().split(/\s+/).filter(Boolean);
    if (parts.length === 0) return "?";
    const first = parts[0][0] || "";
    const last = parts.length > 1 ? parts[parts.length - 1][0] : "";
    return (first + last).toUpperCase();
});

const notificationCount = computed(
    () => page.props.value?.notifications_count || 0,
);

const userMenuOpen = ref(false);
const userMenuRef = ref(null);

const closeUserMenu = (e) => {
    if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
        userMenuOpen.value = false;
    }
};

const onEscape = (e) => {
    if (e.key === "Escape") userMenuOpen.value = false;
};

onMounted(() => {
    document.addEventListener("click", closeUserMenu);
    document.addEventListener("keydown", onEscape);
});

onUnmounted(() => {
    document.removeEventListener("click", closeUserMenu);
    document.removeEventListener("keydown", onEscape);
});

function logout() {
    userMenuOpen.value = false;
    Inertia.post(route("logout"));
}
</script>
