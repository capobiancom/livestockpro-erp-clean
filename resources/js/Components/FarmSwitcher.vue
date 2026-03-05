<template>
    <div class="flex items-center gap-2">
        <label class="text-xs text-gray-500">Farm</label>
        <select
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            :value="selectedFarmId ?? ''"
            @change="onChange"
        >
            <option
                v-for="farm in availableFarms"
                :key="farm.id"
                :value="farm.id"
            >
                {{ farm.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    availableFarms: {
        type: Array,
        required: true,
    },
    selectedFarmId: {
        type: [Number, String, null],
        default: null,
    },
});

function onChange(e) {
    const farmId = parseInt(e.target.value, 10);

    Inertia.post(
        route("farm.switch"),
        { farm_id: farmId },
        {
            preserveScroll: true,
            preserveState: false,
        },
    );
}
</script>
