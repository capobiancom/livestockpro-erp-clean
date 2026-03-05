<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

const page = usePage();

/**
 * `app_logo_path` comes from HandleInertiaRequests::share()
 * It is stored as a relative path in storage (e.g. "settings/logo.png")
 */
const logoSrc = computed(() => {
    const p = page.props.value?.app_logo_path;

    if (!p) return null;

    // If backend ever returns a full URL, keep it as-is
    if (
        typeof p === "string" &&
        (p.startsWith("http://") ||
            p.startsWith("https://") ||
            p.startsWith("/"))
    ) {
        return p;
    }

    return `/storage/${p}`;
});
</script>

<template>
    <img v-if="logoSrc" :src="logoSrc" alt="App Logo" class="block" />
    <span v-else class="text-2xl font-bold text-gray-200">🐄</span>
</template>
