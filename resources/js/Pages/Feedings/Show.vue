<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-800">
                    Feeding Record Details
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('feedings.edit', feeding.id)"
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
                    >
                        Edit Record
                    </Link>
                    <Link
                        :href="route('feedings.index')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200"
                    >
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Feeding Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b">
                    Feeding Information
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">
                            Animal
                        </p>
                        <p class="text-lg text-gray-900 mt-1">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-teal-100 text-teal-800"
                            >
                                {{ feeding.animal?.tag }} -
                                {{ feeding.animal?.name }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Group</p>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ feeding.group?.name || "—" }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">
                            Feeding Date
                        </p>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ formatDate(feeding.feeding_date) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">
                            Feeding Time
                        </p>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ feeding.feeding_time || "—" }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feeding Items -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b">
                    Feeding Items
                </h3>
                <div v-if="feeding.feeding_items.length > 0" class="space-y-4">
                    <div
                        v-for="item in feeding.feeding_items"
                        :key="item.id"
                        class="border-b pb-2 last:pb-0 last:border-b-0"
                    >
                        <p class="text-sm text-gray-600 font-semibold">Item</p>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ item.item?.name }} ({{ item.quantity }}
                            {{ item.item?.unit }})
                        </p>
                    </div>
                </div>
                <p v-else class="text-gray-700">No feeding items recorded.</p>
            </div>

            <!-- Audit Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b">
                    Audit Information
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">
                            Record Created
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ formatDate(feeding.created_at) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-semibold">
                            Last Updated
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ formatDate(feeding.updated_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div
                class="bg-white rounded-lg shadow-lg p-6 md:col-span-2"
                v-if="feeding.notes"
            >
                <h3 class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b">
                    Notes
                </h3>
                <p class="text-gray-700 whitespace-pre-wrap">
                    {{ feeding.notes }}
                </p>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    feeding: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};
</script>
