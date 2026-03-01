<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Purchase Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Track inventory purchases and supplier orders
                    </p>
                </div>
                <Link
                    :href="route('purchases.create')"
                    class="bg-gradient-to-r from-amber-500 to-orange-500 ml-5 hover:from-amber-600 hover:to-orange-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Purchase
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
                                placeholder="Search by supplier name or item name..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-md p-5 border border-amber-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-semibold">
                            Total Purchases
                        </p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">
                            {{ statistics.total_purchases }}
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
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-5 border border-green-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-semibold">
                            Total Spent
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ money(statistics.total_spent) }}
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            This Month
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ money(statistics.this_month) }}
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
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow-md p-5 border border-purple-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">
                            Suppliers
                        </p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statistics.unique_suppliers }}
                        </p>
                    </div>
                    <div class="bg-purple-500 rounded-full p-3">
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
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchases Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-gradient-to-r from-amber-500 to-orange-500 text-white"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Date
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Supplier
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Invoice Number
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Total Amount
                            </th>
                            <th
                                class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="purchase in purchases.data"
                            :key="purchase.id"
                            class="hover:bg-amber-50 transition duration-150"
                        >
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ formatDate(purchase.purchased_at) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ purchase.supplier?.name || "-" }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ purchase.invoice_number || "-" }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm font-semibold text-amber-600"
                            >
                                {{ money(purchase.total_amount) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <Link
                                        :href="route('purchases.show', purchase.id)"
                                        class="group relative inline-flex items-center justify-center p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 transition-all duration-200 hover:shadow-md"
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
                                        <span
                                            class="absolute bottom-full mb-2 hidden group-hover:block px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap"
                                        >
                                            View Details
                                        </span>
                                    </Link>
                                    <Link
                                        :href="route('purchases.edit', purchase.id)"
                                        class="group relative inline-flex items-center justify-center p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 hover:text-amber-700 transition-all duration-200 hover:shadow-md"
                                        title="Edit Record"
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
                                        <span
                                            class="absolute bottom-full mb-2 hidden group-hover:block px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap"
                                        >
                                            Edit Record
                                        </span>
                                    </Link>
                                    <button
                                        @click="printInvoice(purchase.id)"
                                        class="group relative inline-flex items-center justify-center p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 transition-all duration-200 hover:shadow-md"
                                        title="Print Invoice"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5 4V2a2 2 0 012-2h6a2 2 0 012 2v2h2a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h2zm0 2h10v7H5V6zm6 10a1 1 0 100 2h2a1 1 0 100-2h-2z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <span
                                            class="absolute bottom-full mb-2 hidden group-hover:block px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap"
                                        >
                                            Print Invoice
                                        </span>
                                    </button>
                                    <button
                                        @click="confirmDelete(purchase)"
                                        class="group relative inline-flex items-center justify-center p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-all duration-200 hover:shadow-md"
                                        title="Delete Record"
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
                                        <span
                                            class="absolute bottom-full mb-2 hidden group-hover:block px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap"
                                        >
                                            Delete Record
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!purchases.data.length">
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-gray-500"
                            >
                                No purchases found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="purchases.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in purchases.links"
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

        <!-- Delete Modal -->
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
                    Are you sure you want to delete this purchase? This action
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
                        @click="deletePurchase"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete
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
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    purchases: Object,
    statistics: Object,
    filters: Object,
});
const searchQuery = ref(props.filters?.q || "");
const showDeleteModal = ref(false);
const purchaseToDelete = ref(null);

const { money } = useMoneyFormatter();

const handleSearch = () => {
    Inertia.get(
        "/purchases",
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
        "/purchases",
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (purchase) => {
    purchaseToDelete.value = purchase;
    showDeleteModal.value = true;
};
const deletePurchase = () => {
    Inertia.delete(`/purchases/${purchaseToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            purchaseToDelete.value = null;
        },
    });
};
const printInvoice = (id) => {
    window.open(route("purchases.print", id), "_blank");
};
const formatDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString();
};
// Removed formatNumber as formatCurrency will be used
// const formatNumber = (value) => {
//     if (value === null || value === undefined) return "0.00";
//     return parseFloat(value).toLocaleString("en-US", {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2,
//     });
// };
</script>
