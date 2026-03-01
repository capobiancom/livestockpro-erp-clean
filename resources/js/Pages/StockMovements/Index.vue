<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    stockMovements: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    statistics: {
        type: Object,
        default: () => ({
            total_movements: 0,
            total_in: 0,
            total_out: 0,
            unit_wise_movements: [], // Add unit_wise_movements to props
        }),
    },
});

const q = ref(props.filters.q);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const searchQuery = ref(props.filters?.q || "");

const stockMovementsData = computed(() => props.stockMovements?.data || []);
const stockMovementLinks = computed(() => props.stockMovements?.links || []);

const handleSearch = () => {
    Inertia.get(
        route("stock-movements.index"),
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
        route("stock-movements.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const deleteItem = () => {
    // Stock movements are typically not directly deletable from the UI,
    // but keeping the structure for consistency if needed in the future.
    // For now, this will just close the modal.
    console.log(
        `Attempting to delete stock movement with ID: ${itemToDelete.value.id}`,
    );
    showDeleteModal.value = false;
    itemToDelete.value = null;
    // If actual deletion is required, uncomment the Inertia.delete call:
    /*
    Inertia.delete(route('stock-movements.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            itemToDelete.value = null;
        },
        onError: (errors) => {
            console.error("Error deleting stock movement:", errors);
            showDeleteModal.value = false;
        }
    });
    */
};
</script>

<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Stock Movement Records
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Track all inventory stock movements
                    </p>
                </div>
                <!-- No "Add New Item" button for stock movements, as they are recorded automatically -->
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
                                placeholder="Search by item name, type, or source..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

        <!-- Unit-wise Stock Statistics -->
        <div class="mb-8">
            <h3
                class="text-2xl font-bold text-gray-800 mb-5 flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7 text-purple-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"
                    />
                </svg>
                Unit-wise Stock Overview
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-if="statistics.unit_wise_movements.length === 0"
                    class="text-center py-12 col-span-full"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12 text-gray-300 mx-auto mb-3"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4m0-10h4m-4 0h-4m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <p class="text-gray-500 text-sm">
                        No unit-wise stock movements to display.
                    </p>
                </div>
                <div
                    v-for="movement in statistics.unit_wise_movements"
                    :key="movement.unit"
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-3 border-l-4 border-purple-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-violet-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-purple-600 text-xs font-semibold bg-purple-50 px-3 py-1 rounded-full"
                        >
                            {{ movement.unit.toUpperCase() }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        Total In / Out ({{ movement.unit }})
                    </p>
                    <p class="text-2xl font-extrabold text-gray-900">
                        {{ formatNumber(movement.total_in) }} /
                        {{ formatNumber(movement.total_out) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Unit-wise stock movement
                    </p>
                </div>
            </div>
        </div>

        <!-- Stock Movements Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div
                v-if="stockMovementsData.length === 0"
                class="text-center py-12"
            >
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
                        d="M8 13v-1m4 1v-1m4 1v-1M2 11a2 2 0 012-2h16a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6zM6 7V5a2 2 0 012-2h8a2 2 0 012 2v2"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Stock Movements Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Stock movements will appear here as inventory changes occur.
                </p>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-blue-500">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Item
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Type
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Quantity
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Unit Cost
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Source
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Date
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Recorded By
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
                        v-for="movement in stockMovementsData"
                        :key="movement.id"
                        class="hover:bg-indigo-50 transition duration-150"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ movement.item?.name ?? "N/A" }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ movement.item_type.split("\\").pop() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="
                                    movement.movement_type === 'in'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            >
                                {{ movement.movement_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $formatNumber(movement.quantity) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $formatCurrency(movement.unit_cost) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ movement.source_event_type ?? "N/A" }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ movement.source_type.split("\\").pop() }} #{{
                                    movement.source_id
                                }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(movement.movement_date) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ movement.user?.name ?? "N/A" }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <!-- Stock movements are typically not editable/deletable directly from this list -->
                                <!-- Add view link if a detailed view is desired -->
                                <Link
                                    :href="route('stock-movements.show', movement.id)"
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
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="stockMovementLinks.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-lg mt-6"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in stockMovementLinks"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                        link.active
                            ? 'bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow'
                            : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'opacity-50 cursor-not-allowed',
                    ]"
                />
            </div>
        </div>

        <!-- Delete Confirmation Modal (Not applicable for stock movements, but keeping structure for consistency) -->
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
                    Are you sure you want to delete this stock movement record?
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
                        @click="deleteItem"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Item
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any specific styles for this page here */
</style>
