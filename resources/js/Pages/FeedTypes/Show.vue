<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Feed Type Details
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        View feed type information
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('feed-types.edit', feedType.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit Feed Type
                    </Link>
                    <Link
                        :href="route('feed-types.index')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-8">
                <div class="flex items-center mb-8">
                    <div class="flex-shrink-0 h-20 w-20 flex items-center justify-center rounded-full bg-amber-100 text-amber-600 font-bold text-3xl">
                        {{ feedType.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ feedType.name }}</h3>
                        <p v-if="feedType.category" class="mt-2">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold"
                                :class="getCategoryClass(feedType.category)"
                            >
                                {{ feedType.category }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Feed Type Name
                        </h4>
                        <p class="text-lg font-medium text-gray-900">
                            {{ feedType.name }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Category
                        </h4>
                        <p v-if="feedType.category">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold"
                                :class="getCategoryClass(feedType.category)"
                            >
                                {{ feedType.category }}
                            </span>
                        </p>
                        <p v-else class="text-lg text-gray-400">Not categorized</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Unit
                        </h4>
                        <p v-if="feedType.unit" class="text-lg font-medium text-gray-900">
                            <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">
                                {{ feedType.unit }}
                            </span>
                        </p>
                        <p v-else class="text-lg text-gray-400">No unit specified</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Unit Cost
                        </h4>
                        <p v-if="feedType.unit_cost" class="text-lg font-semibold text-green-600">
                            ${{ feedType.unit_cost }}
                        </p>
                        <p v-else class="text-lg text-gray-400">No cost specified</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Description
                        </h4>
                        <p v-if="feedType.description" class="text-lg text-gray-900">
                            {{ feedType.description }}
                        </p>
                        <p v-else class="text-lg text-gray-400">No description provided</p>
                    </div>

                    <div v-if="feedType.nutrient_info && Object.keys(feedType.nutrient_info).length > 0" class="bg-gray-50 p-6 rounded-lg md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Nutrient Information
                        </h4>
                        <div class="space-y-2">
                            <div v-for="(value, key) in feedType.nutrient_info" :key="key" class="flex items-start">
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ key }}:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ value }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Created At
                        </h4>
                        <p class="text-lg text-gray-900">
                            {{ formatDate(feedType.created_at) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Last Updated
                        </h4>
                        <p class="text-lg text-gray-900">
                            {{ formatDate(feedType.updated_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

defineProps({
    feedType: Object,
});

function getCategoryClass(category) {
    const classes = {
        grass: "bg-green-100 text-green-800",
        grain: "bg-yellow-100 text-yellow-800",
        hay: "bg-amber-100 text-amber-800",
        silage: "bg-lime-100 text-lime-800",
        supplements: "bg-purple-100 text-purple-800",
        concentrates: "bg-orange-100 text-orange-800",
        other: "bg-gray-100 text-gray-800",
    };
    return classes[category] || classes.other;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
