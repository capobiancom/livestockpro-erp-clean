<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    PrinterIcon,
    FunnelIcon,
    XMarkIcon,
    DocumentTextIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
} from "@heroicons/vue/24/outline";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    journalEntries: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    referenceTypes: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const currencySymbol = computed(
    () => page.props.value.app_currency_symbol || "BDT",
);

const form = ref({
    reference_type: props.filters?.reference_type || "",
    status: props.filters?.status || "",
    date_from: props.filters?.date_from || "",
    date_to: props.filters?.date_to || "",
});

const applyFilters = () => {
    Inertia.get(route("journal-voucher-report.index"), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    form.value = {
        reference_type: "",
        status: "",
        date_from: "",
        date_to: "",
    };
    applyFilters();
};

const hasActiveFilters = computed(() =>
    Object.values(form.value).some((v) => v !== ""),
);

const formatCurrency = (value) => {
    if (value === null || value === undefined)
        return `${currencySymbol.value} 0.00`;
    const numValue = parseFloat(value);
    if (isNaN(numValue)) return `${currencySymbol.value} 0.00`;
    return `${currencySymbol.value} ${numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const entryTotalDebit = (entry) =>
    entry.lines?.reduce(
        (sum, line) => sum + parseFloat(line.debit_amount || 0),
        0,
    ) || 0;

const entryTotalCredit = (entry) =>
    entry.lines?.reduce(
        (sum, line) => sum + parseFloat(line.credit_amount || 0),
        0,
    ) || 0;

const grandTotalDebit = computed(() =>
    props.journalEntries.data.reduce(
        (sum, entry) => sum + entryTotalDebit(entry),
        0,
    ),
);

const grandTotalCredit = computed(() =>
    props.journalEntries.data.reduce(
        (sum, entry) => sum + entryTotalCredit(entry),
        0,
    ),
);

const referenceTypeLabel = (type) => {
    const labels = {
        sale: "Sale Voucher",
        purchase: "Purchase Voucher",
        payroll: "Payroll Voucher",
        treatment: "Treatment Voucher",
        milk_sale: "Milk Sale Voucher",
        expense: "Expense Voucher",
        cash: "Cash Voucher",
    };
    return (
        labels[type] ||
        type
            ?.replace(/_/g, " ")
            ?.replace(/\b\w/g, (l) => l.toUpperCase()) ||
        "—"
    );
};

const referenceTypeBadgeClass = (type) => {
    const palette = {
        sale: "bg-emerald-100 text-emerald-800",
        purchase: "bg-orange-100 text-orange-800",
        payroll: "bg-purple-100 text-purple-800",
        treatment: "bg-pink-100 text-pink-800",
        milk_sale: "bg-cyan-100 text-cyan-800",
        expense: "bg-yellow-100 text-yellow-800",
        cash: "bg-teal-100 text-teal-800",
    };
    return `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${palette[type] || "bg-gray-100 text-gray-800"}`;
};

const statusClasses = (status) => ({
    "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize":
        true,
    "bg-blue-100 text-blue-800": status === "draft",
    "bg-green-100 text-green-800": status === "posted",
    "bg-red-100 text-red-800": status === "reversed",
});
</script>

<template>
    <Layout title="Journal Voucher Report">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Journal Voucher Report
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8 space-y-5">
                <!-- Hero Header -->
                <div
                    class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600 rounded-xl shadow-lg p-6 text-white"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm"
                            >
                                <DocumentTextIcon class="h-6 w-6 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">
                                    Journal Voucher Report
                                </h1>
                                <p class="text-indigo-200 text-sm mt-0.5">
                                    Filter, view and print journal vouchers by
                                    reference type
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('journal-entries.index')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/30"
                            >
                                <DocumentTextIcon class="h-4 w-4" />
                                All Journal Entries
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                >
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center"
                        >
                            <FunnelIcon class="h-4 w-4 text-indigo-600" />
                        </div>
                        <h3
                            class="text-sm font-semibold text-gray-700 uppercase tracking-wide"
                        >
                            Filter Vouchers
                        </h3>
                        <span
                            v-if="hasActiveFilters"
                            class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700"
                        >
                            Filters active
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Voucher Type -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                >Voucher Type</label
                            >
                            <select
                                v-model="form.reference_type"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            >
                                <option value="">All Types</option>
                                <option
                                    v-for="type in referenceTypes"
                                    :key="type"
                                    :value="type"
                                >
                                    {{ referenceTypeLabel(type) }}
                                </option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                >Status</label
                            >
                            <select
                                v-model="form.status"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            >
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="posted">Posted</option>
                                <option value="reversed">Reversed</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                >Date From</label
                            >
                            <input
                                type="date"
                                v-model="form.date_from"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                            />
                        </div>

                        <!-- Date To -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                >Date To</label
                            >
                            <input
                                type="date"
                                v-model="form.date_to"
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                        <button
                            @click="applyFilters"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:from-indigo-700 hover:to-blue-700 transition-all duration-200"
                        >
                            <FunnelIcon class="h-4 w-4" />
                            Apply Filters
                        </button>
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-all duration-200"
                        >
                            <XMarkIcon class="h-4 w-4" />
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"
                    >
                        <div
                            class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0"
                        >
                            <DocumentTextIcon class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                            >
                                Total Vouchers
                            </p>
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">
                                {{ journalEntries.total }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ journalEntries.data.length }} on this page
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"
                    >
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0"
                        >
                            <ArrowTrendingUpIcon
                                class="h-6 w-6 text-emerald-600"
                            />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                            >
                                Total Debit (Page)
                            </p>
                            <p
                                class="text-2xl font-bold text-emerald-600 mt-0.5"
                            >
                                {{ formatCurrency(grandTotalDebit) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"
                    >
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0"
                        >
                            <ArrowTrendingDownIcon
                                class="h-6 w-6 text-blue-600"
                            />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                            >
                                Total Credit (Page)
                            </p>
                            <p class="text-2xl font-bold text-blue-600 mt-0.5">
                                {{ formatCurrency(grandTotalCredit) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <!-- Table Header -->
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                    >
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Journal Vouchers
                                <span
                                    v-if="filters?.reference_type"
                                    class="ml-2 text-indigo-600"
                                >
                                    —
                                    {{
                                        referenceTypeLabel(
                                            filters.reference_type,
                                        )
                                    }}
                                </span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ journalEntries.total }} record(s) found
                            </p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Voucher #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Voucher Type
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Description
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Total Debit
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Total Credit
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr
                                    v-for="entry in journalEntries.data"
                                    :key="entry.id"
                                    class="hover:bg-indigo-50/40 transition-colors duration-150"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap"
                                    >
                                        <span
                                            class="text-sm font-bold text-indigo-600"
                                            >#{{
                                                String(entry.id).padStart(
                                                    4,
                                                    "0",
                                                )
                                            }}</span
                                        >
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ formatDate(entry.entry_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="
                                                referenceTypeBadgeClass(
                                                    entry.reference_type,
                                                )
                                            "
                                        >
                                            {{
                                                referenceTypeLabel(
                                                    entry.reference_type,
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-600 max-w-xs"
                                    >
                                        <span
                                            class="block truncate"
                                            :title="entry.description"
                                            >{{
                                                entry.description || "—"
                                            }}</span
                                        >
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-700 text-right"
                                    >
                                        {{
                                            formatCurrency(
                                                entryTotalDebit(entry),
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-700 text-right"
                                    >
                                        {{
                                            formatCurrency(
                                                entryTotalCredit(entry),
                                            )
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="statusClasses(entry.status)">
                                            {{ entry.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center"
                                    >
                                        <a
                                            :href="
                                                route(
                                                    'journal-entries.print-voucher',
                                                    entry.id,
                                                )
                                            "
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-all duration-200"
                                            title="Print Voucher"
                                        >
                                            <PrinterIcon class="h-3.5 w-3.5" />
                                            Print
                                        </a>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="journalEntries.data.length === 0">
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div
                                            class="flex flex-col items-center gap-3"
                                        >
                                            <div
                                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center"
                                            >
                                                <DocumentTextIcon
                                                    class="h-8 w-8 text-gray-400"
                                                />
                                            </div>
                                            <p
                                                class="text-sm font-semibold text-gray-600"
                                            >
                                                No vouchers found
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Try adjusting your filters to
                                                see more results
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100">
                        <Pagination :links="journalEntries.links" />
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
