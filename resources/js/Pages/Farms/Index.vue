<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Farm Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your farm locations and properties
                    </p>
                </div>
                <Link
                    :href="route('farms.create')"
                    class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Farm
                </Link>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-green-700 font-medium">
                    {{ $page.props.flash.success }}
                </p>
            </div>
        </div>

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
                                placeholder="Search by farm name..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            />
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 absolute left-3 top-3 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                    </div>
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-5 border border-green-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-semibold">
                            Total Farms
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ farms.total }}
                        </p>
                    </div>
                    <div class="bg-green-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            Total Animals
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ totalAnimals }}
                        </p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-md p-5 border border-amber-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-semibold">
                            Total Staff
                        </p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">
                            {{ totalStaff }}
                        </p>
                    </div>
                    <div class="bg-amber-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Farms Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div v-if="farms.data.length === 0" class="text-center py-12">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 mx-auto text-gray-300 mb-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Farms Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Start managing your farms by adding your first farm
                    location.
                </p>
                <Link
                    :href="route('farms.create')"
                    class="inline-flex items-center bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add First Farm
                </Link>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-500 to-emerald-500">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Code
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Address
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Contact
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Animals
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="farm in farms.data"
                        :key="farm.id"
                        class="hover:bg-green-50 transition duration-150"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-green-400 to-emerald-400 rounded-full flex items-center justify-center text-white font-bold"
                                >
                                    {{ getInitials(farm.name) }}
                                </div>
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ farm.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                v-if="farm.code"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800"
                            >
                                {{ farm.code }}
                            </span>
                            <span v-else class="text-gray-400">—</span>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="text-sm text-gray-900 max-w-xs truncate"
                            >
                                {{ farm.address || "—" }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div v-if="farm.contact_name">
                                    {{ farm.contact_name }}
                                </div>
                                <div
                                    v-if="farm.contact_phone"
                                    class="text-gray-500"
                                >
                                    {{ farm.contact_phone }}
                                </div>
                                <span
                                    v-if="
                                        !farm.contact_name &&
                                        !farm.contact_phone
                                    "
                                    >—</span
                                >
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ farm.animals_count || 0 }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    :href="route('farms.show', farm.id)"
                                    class="text-blue-600 hover:text-blue-800 font-semibold transition"
                                    title="View Details"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                        />
                                        <path
                                            fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </Link>
                                <Link
                                    :href="route('farms.edit', farm.id)"
                                    class="text-green-600 hover:text-green-800 font-semibold transition"
                                    title="Edit Farm"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                        />
                                    </svg>
                                </Link>
                                <button
                                    @click="confirmDelete(farm)"
                                    class="text-red-600 hover:text-red-800 font-semibold transition"
                                    title="Delete Farm"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="farms.links.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-lg mt-6"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in farms.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                        link.active
                            ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow'
                            : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'opacity-50 cursor-not-allowed',
                    ]"
                />
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
                    Are you sure you want to delete this farm? This action
                    cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteFarm"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Farm
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    farms: Object,
    filters: Object,
});

const showDeleteModal = ref(false);
const farmToDelete = ref(null);
const searchQuery = ref(props.filters?.q || "");

const handleSearch = () => {
    Inertia.get(
        "/farms",
        { q: searchQuery.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearSearch = () => {
    searchQuery.value = "";
    Inertia.get(
        "/farms",
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

// Compute statistics
const totalAnimals = computed(() => {
    return props.farms.data.reduce(
        (sum, farm) => sum + (farm.animals_count || 0),
        0,
    );
});

const totalStaff = computed(() => {
    return props.farms.data.reduce(
        (sum, farm) => sum + (farm.staff_count || 0),
        0,
    );
});

const getInitials = (name) => {
    const words = name.split(" ");
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const confirmDelete = (farm) => {
    farmToDelete.value = farm;
    showDeleteModal.value = true;
};

const deleteFarm = () => {
    Inertia.delete(`/farms/${farmToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            farmToDelete.value = null;
        },
    });
};
</script>
