<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Payroll Items Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage and track individual payroll items
                    </p>
                </div>
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
                                placeholder="Search by employee name..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent"
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
                    <div class="w-1/4">
                        <select
                            v-model="selectedPayrollRun"
                            @change="handleSearch"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option :value="null">All Payroll Runs</option>
                            <option
                                v-for="pr in payrollRuns"
                                :key="pr.id"
                                :value="pr.id"
                            >
                                {{ getMonthName(pr.month) }} {{ pr.year }}
                            </option>
                        </select>
                    </div>
                    <button
                        v-if="searchQuery || selectedPayrollRun"
                        @click="clearSearch"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Clear
                    </button>
                    <button
                        @click="generateSalarySheet"
                        :disabled="!selectedPayrollRun"
                        class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-900 focus:outline-none focus:border-rose-900 focus:ring ring-rose-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5 4V2a2 2 0 012-2h6a2 2 0 012 2v2h2a2 2 0 012 2v7a2 2 0 01-2 2h-2v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4H3a2 2 0 01-2-2V6a2 2 0 012-2h2zm0 2H3v7h2V6zm10 0h2v7h-2V6zM7 2h6v2H7V2zm0 16h6v-4H7v4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Generate Salary Sheet
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

        <!-- Payroll Items Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div
                v-if="payrollItems.data.length === 0"
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
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Payroll Items Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Get started by adding your first payroll item to the system.
                </p>
                <Link
                    :href="
                        payrollRun
                            ? `/payroll-items/create?payroll_run_id=${payrollRun.id}`
                            : '/payroll-items/create'
                    "
                    class="inline-flex items-center bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold"
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
                    Add Your First Payroll Item
                </Link>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-rose-500 to-pink-500">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Payroll Run
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Employee
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Basic Salary
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Allowances
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Overtime
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Gross Salary
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Deductions
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Net Salary
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template
                        v-for="(payrollItem, index) in payrollItems.data"
                        :key="payrollItem?.id || index"
                    >
                        <tr
                            v-if="payrollItem"
                            class="hover:bg-rose-50 transition duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{
                                        getMonthName(
                                            payrollItem.payroll_run?.month,
                                        )
                                    }}
                                    {{ payrollItem.payroll_run?.year }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollItem.employee?.first_name }}
                                    {{ payrollItem.employee?.last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollItem.basic_salary }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    H: {{ payrollItem.house_allowance }}, M:
                                    {{ payrollItem.medical_allowance }}, T:
                                    {{ payrollItem.transport_allowance }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    Hrs: {{ payrollItem.overtime_hours }}, Rate:
                                    {{ payrollItem.overtime_rate }}, Amt:
                                    {{ payrollItem.overtime_amount }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollItem.gross_salary }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollItem.deductions }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollItem.net_salary }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                            >
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <Link
                                        :href="route('payroll-items.show', payrollItem.id)"
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
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="payrollItems.links.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in payrollItems.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                        link.active
                            ? 'bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow'
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
                    Are you sure you want to delete this payroll item for
                    <span class="font-semibold"
                        >{{ payrollItemToDelete?.employee?.first_name }}
                        {{ payrollItemToDelete?.employee?.last_name }}</span
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
                        @click="deletePayrollItem"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Payroll Item
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    payrollItems: Object,
    payrollRuns: Array,
    filters: Object,
    payrollRun: Object, // Optional, if coming from a specific payroll run's show page
});

const searchQuery = ref(props.filters?.q || "");
const selectedPayrollRun = ref(props.filters?.payroll_run_id || null);
const showDeleteModal = ref(false);
const payrollItemToDelete = ref(null);

const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

const getMonthName = (monthNumber) => {
    return monthNames[monthNumber - 1] || "Unknown";
};

const handleSearch = () => {
    Inertia.get(
        "/payroll-items",
        { q: searchQuery.value, payroll_run_id: selectedPayrollRun.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearSearch = () => {
    searchQuery.value = "";
    selectedPayrollRun.value = null;
    Inertia.get(
        "/payroll-items",
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (payrollItem) => {
    payrollItemToDelete.value = payrollItem;
    showDeleteModal.value = true;
};

const deletePayrollItem = () => {
    Inertia.delete(`/payroll-items/${payrollItemToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            payrollItemToDelete.value = null;
        },
    });
};

const generateSalarySheet = () => {
    if (selectedPayrollRun.value) {
        console.log("selectedPayrollRun.value:", selectedPayrollRun.value);
        const url = `/payroll-items/generate-salary-sheet?payroll_run_id=${selectedPayrollRun.value}`;
        console.log("Generated URL:", url);
        window.open(url, "_blank");
    }
};
</script>
