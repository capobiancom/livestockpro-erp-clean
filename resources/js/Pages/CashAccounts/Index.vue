<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Cash & Bank Accounts
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your cash, bank and mobile money accounts
                    </p>
                </div>
                <Link
                    :href="route('cash-accounts.create')"
                    class="bg-gradient-to-r ml-5 from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add New Account
                </Link>
            </div>
        </template>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex gap-4 items-center flex-wrap">
                    <div class="flex-1 min-w-[300px]">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by account name, number or bank..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
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
                        v-model="typeFilter"
                        @change="handleFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                    >
                        <option value="">All Types</option>
                        <option v-for="type in accountTypes" :key="type.value" :value="type.value">
                            {{ type.name }}
                        </option>
                    </select>
                    <button
                        v-if="searchQuery || typeFilter"
                        @click="clearFilters"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Clear
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

        <!-- Error Message -->
        <div
            v-if="$page.props.flash?.error"
            class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-red-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-red-700 font-medium">
                    {{ $page.props.flash.error }}
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg shadow-md p-5 border border-emerald-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-emerald-600 font-semibold">
                            Total Accounts
                        </p>
                        <p class="text-3xl font-bold text-emerald-700 mt-1">
                            {{ statistics?.total_accounts || 0 }}
                        </p>
                    </div>
                    <div class="bg-emerald-500 rounded-full p-3">
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
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
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
                            Cash Accounts
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ statistics?.cash_accounts || 0 }}
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
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
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
                            Bank Accounts
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics?.bank_accounts || 0 }}
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
                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"
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
                            Mobile Accounts
                        </p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statistics?.mobile_accounts || 0 }}
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
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
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
                            Total Balance
                        </p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">
                            {{ formatCurrency(statistics?.total_balance) }}
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Account Name
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Type
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Account Number
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Bank/Provider
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Current Balance
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Status
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
                            v-for="account in cashAccounts.data"
                            :key="account.id"
                            class="hover:bg-gray-50 transition duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ account.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': account.type === 'cash',
                                        'bg-blue-100 text-blue-800': account.type === 'bank',
                                        'bg-purple-100 text-purple-800': account.type === 'mobile',
                                    }"
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                >
                                    {{ account.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ account.account_number || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ account.bank_name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ formatCurrency(account.current_balance) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': account.is_active,
                                        'bg-red-100 text-red-800': !account.is_active,
                                    }"
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                    {{ account.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                            >
                                <div class="flex items-center justify-center space-x-2">
                                    <Link
                                        :href="route('cash-accounts.show', account.id)"
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
                                        :href="route('cash-accounts.edit', account.id)"
                                        class="text-indigo-600 hover:text-indigo-800 transition"
                                        title="Edit Account"
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
                                        @click="confirmDelete(account)"
                                        class="text-red-600 hover:text-red-800 transition"
                                        title="Delete Account"
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
                        <tr v-if="!cashAccounts.data.length">
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
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                        />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">
                                        No accounts found
                                    </p>
                                    <p class="text-gray-400 text-sm mt-1">
                                        Click "Add New Account" to create your first account
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="cashAccounts.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in cashAccounts.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow'
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
                    Are you sure you want to delete the account
                    <span class="font-semibold">{{ accountToDelete?.name }}</span
                    >? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteAccount"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Account
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
    cashAccounts: Object,
    filters: Object,
    accountTypes: Array,
    statistics: Object,
});

const searchQuery = ref(props.filters?.search || "");
const typeFilter = ref(props.filters?.type || "");
const showDeleteModal = ref(false);
const accountToDelete = ref(null);

const handleSearch = debounce(() => {
    Inertia.get(
        route("cash-accounts.index"),
        {
            search: searchQuery.value,
            type: typeFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

const handleFilter = () => {
    Inertia.get(
        route("cash-accounts.index"),
        {
            search: searchQuery.value,
            type: typeFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    searchQuery.value = "";
    typeFilter.value = "";
    Inertia.get(
        route("cash-accounts.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (account) => {
    accountToDelete.value = account;
    showDeleteModal.value = true;
};

const deleteAccount = () => {
    Inertia.delete(route("cash-accounts.destroy", accountToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            accountToDelete.value = null;
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
</script>
