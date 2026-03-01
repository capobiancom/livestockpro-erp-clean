<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Supplier Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your farm suppliers and vendors
                    </p>
                </div>
                <Link
                    :href="route('suppliers.create')"
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Supplier
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
                                placeholder="Search by supplier name or contact person..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-md p-5 border border-indigo-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-indigo-600 font-semibold">Total Suppliers</p>
                        <p class="text-3xl font-bold text-indigo-700 mt-1">
                            {{ statistics.total_suppliers }}
                        </p>
                    </div>
                    <div class="bg-indigo-500 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-5 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-semibold">Active Suppliers</p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ statistics.active_suppliers }}
                        </p>
                    </div>
                    <div class="bg-green-500 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-md p-5 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">With Email</p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics.with_email }}
                        </p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow-md p-5 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">With Phone</p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statistics.with_phone }}
                        </p>
                    </div>
                    <div class="bg-purple-500 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Contact Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Items Count</th>
                            <th class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="supplier in suppliers.data"
                            :key="supplier.id"
                            class="hover:bg-indigo-50 transition duration-150"
                        >
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ supplier.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ supplier.contact_name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ supplier.phone || '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ supplier.email || '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="px-2 py-1 bg-indigo-100 rounded text-xs font-medium">
                                    {{ supplier.items_count || 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('suppliers.show', supplier.id)"
                                        class="text-blue-600 hover:text-blue-800 font-semibold transition"
                                        title="View Details"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="route('suppliers.edit', supplier.id)"
                                        class="text-indigo-600 hover:text-indigo-800 font-semibold transition"
                                        title="Edit Supplier"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="confirmDelete(supplier)"
                                        class="text-red-600 hover:text-red-800 font-semibold transition"
                                        title="Delete Supplier"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!suppliers.data.length">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No suppliers found</p>
                                    <p class="text-gray-400 text-sm mt-1">Click "Add Supplier" to create your first supplier</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="suppliers.links.length > 3" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in suppliers.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow'
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
                    <h3 class="text-xl font-bold text-gray-900">Confirm Deletion</h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete the supplier
                    <span class="font-semibold">{{ supplierToDelete?.name }}</span>?
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
                        @click="deleteSupplier"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Supplier
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
    suppliers: Object,
    statistics: Object,
    filters: Object,
});

const showDeleteModal = ref(false);
const supplierToDelete = ref(null);
const searchQuery = ref(props.filters?.q || "");

const handleSearch = () => {
    Inertia.get("/suppliers", { q: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    searchQuery.value = "";
    Inertia.get("/suppliers", {}, {
        preserveState: true,
        replace: true,
    });
};

const confirmDelete = (supplier) => {
    supplierToDelete.value = supplier;
    showDeleteModal.value = true;
};

const deleteSupplier = () => {
    Inertia.delete(`/suppliers/${supplierToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            supplierToDelete.value = null;
        },
    });
};

const formatDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString();
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
</script>
