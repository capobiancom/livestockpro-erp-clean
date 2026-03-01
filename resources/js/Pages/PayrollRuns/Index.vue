<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Payroll Runs Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage and track payroll runs
                    </p>
                </div>
                <Link
                    :href="route('payroll-runs.create')"
                    class="bg-gradient-to-r ml-5 from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add New Payroll Run
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
                                placeholder="Search by month or year..."
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
                class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg shadow-md p-5 border border-rose-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-rose-600 font-semibold">
                            Total Payroll Runs
                        </p>
                        <p class="text-3xl font-bold text-rose-700 mt-1">
                            {{ payrollRuns.total }}
                        </p>
                    </div>
                    <div class="bg-rose-500 rounded-full p-3">
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 12v-1m-6.5-2.5h11a2 2 0 002-2v-6a2 2 0 00-2-2h-11a2 2 0 00-2 2v6a2 2 0 002 2z"
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
                            Finalized
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ statusCounts.finalized }}
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
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
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
                        <p class="text-sm text-blue-600 font-semibold">Paid</p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statusCounts.paid }}
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
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg shadow-md p-5 border border-purple-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">
                            Draft
                        </p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statusCounts.draft }}
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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Runs Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div v-if="payrollRuns.data.length === 0" class="text-center py-12">
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
                    No Payroll Runs Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Get started by adding your first payroll run to the system.
                </p>
                <Link
                    :href="route('payroll-runs.create')"
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
                    Add Your First Payroll Run
                </Link>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-rose-500 to-pink-500">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Month
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Year
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Farm
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            User
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Generated At
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
                        v-for="(payrollRun, index) in payrollRuns.data"
                        :key="payrollRun?.id || index"
                    >
                        <tr
                            v-if="payrollRun"
                            class="hover:bg-rose-50 transition duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ getMonthName(payrollRun.month) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollRun.year }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollRun.farm?.name || "—" }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ payrollRun.user?.name || "—" }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="
                                        getStatusBadgeClass(payrollRun.status)
                                    "
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                >
                                    {{ payrollRun.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{
                                        payrollRun.generated_at
                                            ? new Date(
                                                  payrollRun.generated_at,
                                              ).toLocaleDateString()
                                            : "—"
                                    }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                            >
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'payroll-runs.show',
                                                payrollRun.id,
                                            )
                                        "
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
                                        v-if="
                                            payrollRun.status !== 'paid' &&
                                            payrollRun.status !== 'finalized'
                                        "
                                        :href="
                                            route(
                                                'payroll-runs.edit',
                                                payrollRun.id,
                                            )
                                        "
                                        class="text-rose-600 hover:text-rose-800 font-semibold transition"
                                        title="Edit Payroll Run"
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
                                    <Link
                                        v-if="
                                            payrollRun.status !== 'paid' &&
                                            payrollRun.status !== 'finalized'
                                        "
                                        :href="
                                            route(
                                                'payroll-runs.generate-payroll',
                                                payrollRun.id,
                                            )
                                        "
                                        method="post"
                                        as="button"
                                        type="button"
                                        class="text-green-600 hover:text-green-800 font-semibold transition"
                                        title="Generate Payroll"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M8.433 7.412A2.001 2.001 0 0010 10.588V18a2 2 0 104 0V3.412a2.001 2.001 0 00-3.567-1.412L10 2.588l-1.567-1.412zM10 12a2 2 0 100-4 2 2 0 000 4z"
                                            />
                                        </svg>
                                    </Link>
                                    <button
                                        v-if="
                                            payrollRun.status !== 'paid' &&
                                            payrollRun.status !== 'finalized'
                                        "
                                        @click="confirmDelete(payrollRun)"
                                        class="text-red-600 hover:text-red-800 font-semibold transition"
                                        title="Delete Payroll Run"
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
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="payrollRuns.links.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in payrollRuns.links"
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
                    Are you sure you want to delete payroll run for
                    <span class="font-semibold"
                        >{{ getMonthName(payrollRunToDelete?.month) }}
                        {{ payrollRunToDelete?.year }}</span
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
                        @click="deletePayrollRun"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Payroll Run
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
import { route } from "ziggy-js";

const props = defineProps({
    payrollRuns: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.q || "");
const showDeleteModal = ref(false);
const payrollRunToDelete = ref(null);

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

// Compute status counts from payrollRuns data
const statusCounts = computed(() => {
    const counts = {
        draft: 0,
        finalized: 0,
        paid: 0,
    };

    props.payrollRuns.data.forEach((payrollRun) => {
        if (counts.hasOwnProperty(payrollRun.status)) {
            counts[payrollRun.status]++;
        }
    });

    return counts;
});

const handleSearch = () => {
    Inertia.get(
        route("payroll-runs.index"),
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
        route("payroll-runs.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-purple-100 text-purple-800",
        finalized: "bg-green-100 text-green-800",
        paid: "bg-blue-100 text-blue-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const confirmDelete = (payrollRun) => {
    payrollRunToDelete.value = payrollRun;
    showDeleteModal.value = true;
};

const deletePayrollRun = () => {
    Inertia.delete(route("payroll-runs.destroy", payrollRunToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            payrollRunToDelete.value = null;
        },
    });
};
</script>
