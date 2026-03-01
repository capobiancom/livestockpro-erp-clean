<template>
    <nav class="flex items-center space-x-2">
        <template v-for="link in links || []" :key="link?.label">
            <button
                v-if="link?.url"
                @click.prevent="visit(link.url)"
                class="px-3 py-1 rounded bg-white border"
            >
                {{ link?.label }}
            </button>
            <span v-else class="px-3 py-1 text-gray-400">{{
                link?.label
            }}</span>
        </template>
    </nav>
</template>

<script setup>
import { Inertia } from "@inertiajs/inertia";
const props = defineProps({ links: Array });
function visit(url) {
    if (!url) return;
    Inertia.get(url);
}
</script>
