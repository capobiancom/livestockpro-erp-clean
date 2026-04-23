<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Cash Transactions
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Track all cash inflows and outflows
                    </p>
                </div>
                <Link
                    :href="route('cash-transactions.create')"
                    class="bg-gradient-to-r ml-5 from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Transaction
                </Link>
            </div>
        </template>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by description..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                    <select
                        v-model="directionFilter"
                        @change="handleFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                    >
                        <option value="">All Directions</option>
                        <option v-for="direction in directions" :key="direction.value" :value="direction.value">
                            {{ direction.name }}
                        </option>
                    </select>
                    <select
                        v-model="accountFilter"
                        @change="handleFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                    >
                        <option value="">All Accounts</option>
                        <option v-for="account in cashAccounts" :key="account.id" :value="account.id">
                            {{ account.name }}
                        </option>
                    </select>
                    <button
                        v-if="searchQuery || directionFilter || accountFilter"
                        @click="clearFilters"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            Total Transactions
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics?.total_transactions || 0 }}
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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
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
                            Total Cash In
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ formatCurrency(statistics?.total_cash_in) }}
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
                                d="M7 11l5-5m0 0l5 5m-5-5v12"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-red-50 to-rose-50 rounded-lg shadow-md p-5 border border-red-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-600 font-semibold">
                            Total Cash Out
                        </p>
                        <p class="text-3xl font-bold text-red-700 mt-1">
                            {{ formatCurrency(statistics?.total_cash_out) }}
                        </p>
                    </div>
                    <div class="bg-red-500 rounded-full p-3">
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
                                d="M17 13l-5 5m0 0l-5-5m5 5V6"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-md p-5 border border-purple-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">
                            Net Cash Flow
                        </p>
                        <p
                            :class="{
                                'text-green-700': statistics?.net_cash_flow >= 0,
                                'text-red-700': statistics?.net_cash_flow < 0,
                            }"
                            class="text-3xl font-bold mt-1"
                        >
                            {{ formatCurrency(statistics?.net_cash_flow) }}
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
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead
                        class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white"
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
                                Account
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Description
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Direction
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Amount
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Balance After
                            </th>
                            <th
                                class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="transaction in cashTransactions.data"
                            :key="transaction.id"
                            class="hover:bg-gray-50 transition duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatDate(transaction.transaction_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ transaction.cash_account?.name || '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ transaction.description || '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': transaction.direction === 'in',
                                        'bg-red-100 text-red-800': transaction.direction === 'out',
                                    }"
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                >
                                    {{ transaction.direction === 'in' ? 'Cash In' : 'Cash Out' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="{
                                        'text-green-600': transaction.direction === 'in',
                                        'text-red-600': transaction.direction === 'out',
                                    }"
                                    class="text-sm font-bold"
                                >
                                    {{ transaction.direction === 'in' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ formatCurrency(transaction.balance_after) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                            >
                                <div class="flex items-center justify-center space-x-2">
                                    <Link
                                        :href="route('cash-transactions.show', transaction.id)"
                                        class="text-blue-600 hover:text-blue-800 transition"
                                        title="View Details"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 inline-block"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="route('cash-transactions.edit', transaction.id)"
                                        class="text-indigo-600 hover:text-indigo-800 transition"
                                        title="Edit Transaction"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 inline-block"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.38-2.827-2.828z"
                                            />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="confirmDelete(transaction)"
                                        class="text-red-600 hover:text-red-800 transition"
                                        title="Delete Transaction"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 inline-block"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!cashTransactions.data.length">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-12 w-12 text-gray-400 mb-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">
                                        No transactions found
                                    </p>
                                    <p class="text-gray-400 text-sm mt-1">
                                        Click "Add Transaction" to record your first transaction
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="cashTransactions.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in cashTransactions.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow'
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
                    Are you sure you want to delete this transaction? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteTransaction"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Transaction
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
import Layout from "@/Layouts/AppLayout.vue";
import { debounce } from "lodash";

const props = defineProps({
    cashTransactions: Object,
    filters: Object,
    directions: Array,
    cashAccounts: Array,
    statistics: Object,
});

const searchQuery = ref(props.filters?.search || "");
const directionFilter = ref(props.filters?.direction || "");
const accountFilter = ref(props.filters?.cash_account_id || "");
const showDeleteModal = ref(false);
const transactionToDelete = ref(null);

const handleSearch = debounce(() => {
    Inertia.get(
        route("cash-transactions.index"),
        {
            search: searchQuery.value,
            direction: directionFilter.value,
            cash_account_id: accountFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

const handleFilter = () => {
    Inertia.get(
        route("cash-transactions.index"),
        {
            search: searchQuery.value,
            direction: directionFilter.value,
            cash_account_id: accountFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    searchQuery.value = "";
    directionFilter.value = "";
    accountFilter.value = "";
    Inertia.get(
        route("cash-transactions.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (transaction) => {
    transactionToDelete.value = transaction;
    showDeleteModal.value = true;
};

const deleteTransaction = () => {
    Inertia.delete(route("cash-transactions.destroy", transactionToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            transactionToDelete.value = null;
        },
    });
};

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return "0.00";
    return parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>
