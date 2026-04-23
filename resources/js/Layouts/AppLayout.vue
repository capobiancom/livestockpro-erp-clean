<template>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Sidebar -->
        <Sidebar :showing="showingSidebar" />

        <!-- Main Content Area -->
        <div
            :class="{ 'ml-64': showingSidebar }"
            class="flex-1 flex flex-col h-screen transition-all duration-300 ease-in-out lg:ml-64"
        >
            <!-- Header - Fixed at top -->
            <header
                class="bg-white border-b shadow-sm p-4 flex justify-between items-center sticky top-0 z-20 app-header"
            >
                <!-- Hamburger for mobile -->
                <button
                    @click="showingSidebar = !showingSidebar"
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

            <!-- Main Content - Scrollable -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <slot />
            </main>
        </div>

        <!-- Toast Notification -->
        <ToastNotification ref="toast" />
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, reactive, onMounted, ref, watch } from "vue";
import ToastNotification from "@/Components/ToastNotification.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Sidebar from "@/Layouts/Components/Sidebar.vue";
 // Import ApplicationLogo
import FarmSwitcher from "@/Components/FarmSwitcher.vue";
import LanguageSelector from "@/Components/LanguageSelector.vue";

const page = usePage();
const user = computed(() => page.props.value.auth?.user ?? null);
const roles = computed(() => {
    const rawRoles = page.props.value.auth?.user?.roles ?? [];
    return rawRoles.map((r) => (typeof r === "string" ? r : r.name));
});
const appSettings = computed(() => usePage().props.value?.appSettings || {}); // Safely access appSettings with optional chaining
const subscription = computed(() => usePage().props.value?.subscription || {});
const enabledFeatures = computed(() => subscription.value?.features || []);
const hasFeature = (key) => enabledFeatures.value.includes(key);

// In single-license mode we bypass subscription feature gating entirely.
const hasFeatureOrSingle = (key) =>
    isSingleLicenseMode.value ? true : hasFeature(key);

const hasRole = (roleName) => {
    if (Array.isArray(roleName)) {
        return roleName.some((r) => roles.value.includes(r));
    }
    return roles.value.includes(roleName);
};

// App mode flags (provided by HandleInertiaRequests)
const isSaasMode = computed(() => !!page.props.value?.app_mode?.saas_mode);
const isSingleLicenseMode = computed(
    () => !!page.props.value?.app_mode?.single_license_mode,
);

const farmContext = computed(() => page.props.value?.farm_context ?? {});

const showingSidebar = ref(window.innerWidth >= 1024); // Show sidebar by default on larger screens



const currentPath = computed(() => page.url.value || "");

onMounted(() => {
    // Close sidebar on smaller screens when navigating
    if (window.innerWidth < 1024) {
        showingSidebar.value = false;
    }
});

// Watch for route changes to close sidebar on mobile
watch(currentPath, () => {
    if (window.innerWidth < 1024) {
        showingSidebar.value = false;
    }
});

// Update showingSidebar on window resize
onMounted(() => {
    window.addEventListener("resize", () => {
        showingSidebar.value = window.innerWidth >= 1024;
    });
});

const toast = ref(null);

watch(
    () => page.props.value.flash ?? {},
    (flash) => {
        console.log("Inertia Flash:", flash); // Add console log for debugging flash messages
        if (toast.value) {
            if (flash.success) {
                toast.value.showToast(flash.success, "success");
            } else if (flash.error) {
                toast.value.showToast(flash.error, "error");
            } else if (flash.info) {
                toast.value.showToast(flash.info, "info");
            }
        }
    },
    { deep: true },
);

// Watch for Inertia errors
watch(
    () => page.props.value.errors ?? {},
    (errors) => {
        console.log("Inertia Errors:", errors); // Add console log for debugging
        if (toast.value && Object.keys(errors).length > 0) {
            // Display the first error message found
            const firstError = Object.values(errors)[0];
            toast.value.showToast(firstError, "error");
        }
    },
    { deep: true },
);

function logout() {
    Inertia.post(route("logout"));
}
</script>

<style scoped>
/* Custom scrollbar styling for sidebar */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #1f2937;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

@media print {
    aside,
    header {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>
