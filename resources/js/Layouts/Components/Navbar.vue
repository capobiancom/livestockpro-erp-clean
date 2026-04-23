<template>
    <header
        class="bg-white border-b shadow-sm p-4 flex justify-between items-center sticky top-0 z-20 app-header"
    >
        <!-- Hamburger for mobile -->
        <button
            @click="emit('toggle-sidebar')"
            class="lg:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md"
        >
            <svg
                class="h-6 w-6"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    :class="{
                        hidden: showingSidebar,
                        'inline-flex': !showingSidebar,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                    :class="{
                        hidden: !showingSidebar,
                        'inline-flex': showingSidebar,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>
        <div>
            <slot name="title"></slot>
        </div>
        <div class="flex items-center gap-4">
            <div v-if="user" class="flex items-center gap-3">
                <span class="text-sm text-gray-600"
                    >Hello, {{ user.name }}</span
                >

                <LanguageSelector />

                <!-- Admin Dashboard quick link (Super Admin/Admin only) -->
                <Link
                    v-if="
                        !isSingleLicenseMode &&
                        hasRole(['Super Admin', 'admin'])
                    "
                    :href="route('admin.dashboard')"
                    class="px-3 py-2 text-sm bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-medium shadow-sm transition duration-200"
                >
                    Admin Dashboard
                </Link>

                <button
                    @click="logout"
                    class="ml-1 px-4 py-2 text-sm bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition duration-200"
                >
                    {{ $t('logout') }}
                </button>
            </div>
            <div v-else>
                <Link
                    :href="route('login')"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >Login</Link
                >
                <Link
                    :href="route('register')"
                    class="ml-3 px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                    >Register</Link
                >
            </div>
            <div>
                <slot name="actions"></slot>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed } from "vue";
import LanguageSelector from "@/Components/LanguageSelector.vue";

const props = defineProps({
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

function logout() {
    Inertia.post(route("logout"));
}
</script>
