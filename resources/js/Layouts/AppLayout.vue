<template>
    <div class="min-h-screen flex bg-gray-50">
        <a
            href="#main-content"
            class="skip-link sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:bg-lime-700 focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2"
        >
            {{ $t('skip_to_main_content') }}
        </a>

        <!-- Sidebar -->
        <Sidebar :showing="showingSidebar" />

        <!-- Main Content Area -->
        <div
            :class="{ 'ml-64': showingSidebar }"
            class="flex-1 flex flex-col h-screen lg:ml-64"
        >
            <!-- Header - Fixed at top -->
            <Navbar
                :showing-sidebar="showingSidebar"
                @toggle-sidebar="showingSidebar = !showingSidebar"
            >
                <template #title>
                    <slot name="title"></slot>
                </template>
                <template #actions>
                    <slot name="actions"></slot>
                </template>
            </Navbar>

            <!-- Main Content - Scrollable -->
            <main
                id="main-content"
                tabindex="-1"
                class="flex-1 overflow-y-auto bg-gray-50 p-6 focus:outline-none"
            >
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
import Navbar from "@/Layouts/Components/Navbar.vue";
 // Import ApplicationLogo
import FarmSwitcher from "@/Components/FarmSwitcher.vue";

const page = usePage();
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
