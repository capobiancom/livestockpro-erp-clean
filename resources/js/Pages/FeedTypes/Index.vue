<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Feed Types Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage feed types and their costs
                    </p>
                </div>
                <Link
                    :href="route('feed-types.create')"
                    class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add New Feed Type
                </Link>
            </div>
        </template>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex gap-4 items-center">
                    <div class="flex-1">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by name..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button v-if="searchQuery" @click="clearSearch" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Unit Cost</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="feedType in feedTypes.data"
                            :key="feedType.id"
                            class="hover:bg-amber-50 transition duration-150"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-amber-100 text-amber-600 font-bold">
                                        {{ feedType.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ feedType.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="getCategoryClass(feedType.category)"
                                >
                                    {{ feedType.category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span v-if="feedType.unit" class="px-2 py-1 bg-gray-100 rounded text-xs font-mono">
                                    {{ feedType.unit }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span v-if="feedType.unit_cost" class="font-semibold text-green-600">
                                    ${{ feedType.unit_cost }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ feedType.description || "-" }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('feed-types.show', feedType.id)"
                                        class="text-gray-600 hover:text-gray-800 font-medium transition p-2 hover:bg-gray-100 rounded"
                                        title="View"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="route('feed-types.edit', feedType.id)"
                                        class="text-amber-600 hover:text-amber-800 font-medium transition p-2 hover:bg-amber-50 rounded"
                                        title="Edit"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="confirmDelete(feedType)"
                                        class="text-red-600 hover:text-red-800 font-medium transition p-2 hover:bg-red-50 rounded"
                                        title="Delete"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!feedTypes.data.length">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No feed types found</p>
                                    <p class="text-gray-400 text-sm mt-1">Click "Add New Feed Type" to create your first feed type</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="feedTypes.links.length > 3" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in feedTypes.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow'
                                : 'bg-white text-gray-700 hover:bg-gray-100',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Confirm Deletion
                    </h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete
                    <span class="font-semibold">{{ feedTypeToDelete?.name }}</span>?
                    This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteFeedType"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Feed Type
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    feedTypes: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.q || "");
const showDeleteModal = ref(false);
const feedTypeToDelete = ref(null);

const handleSearch = () => {
    Inertia.get("/feed-types", { q: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    searchQuery.value = "";
    Inertia.get("/feed-types", {}, {
        preserveState: true,
        replace: true,
    });
};

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

const confirmDelete = (feedType) => {
    feedTypeToDelete.value = feedType;
    showDeleteModal.value = true;
};

const deleteFeedType = () => {
    Inertia.delete(`/feed-types/${feedTypeToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            feedTypeToDelete.value = null;
        },
    });
};
</script>
