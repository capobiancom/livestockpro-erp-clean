<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    ScaleIcon,
    PrinterIcon,
    ArrowPathIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    asOf: { type: String, required: true },
    rows: { type: Array, required: true },
    totals: { type: Object, required: true },
});

const page = usePage();
const currencySymbol = computed(
    () => page.props.value.app_currency_symbol || "BDT",
);

const form = ref({
    as_of: props.asOf || "",
});

const apply = () => {
    Inertia.get(route("balance-sheet.index"), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetToToday = () => {
    form.value.as_of = new Date().toISOString().slice(0, 10);
    apply();
};

const formatCurrency = (value) => {
    const num = Number(value || 0);
    return `${currencySymbol.value} ${num.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const sectionLabel = (type) => {
    if (type === "asset") return "Assets";
    if (type === "liability") return "Liabilities";
    if (type === "equity") return "Equity";
    return type;
};

const sectionMeta = (type) => {
    const map = {
        asset: {
            gradient: "from-emerald-700 via-emerald-600 to-teal-600",
            chip: "bg-emerald-50 text-emerald-700 border-emerald-200",
            amount: "text-emerald-700",
        },
        liability: {
            gradient: "from-orange-700 via-orange-600 to-amber-600",
            chip: "bg-orange-50 text-orange-700 border-orange-200",
            amount: "text-orange-700",
        },
        equity: {
            gradient: "from-indigo-700 via-indigo-600 to-blue-600",
            chip: "bg-indigo-50 text-indigo-700 border-indigo-200",
            amount: "text-indigo-700",
        },
    };
    return map[type] || map.asset;
};

const grouped = computed(() => {
    const groups = { asset: [], liability: [], equity: [] };
    for (const r of props.rows) {
        if (groups[r.type]) groups[r.type].push(r);
    }
    return groups;
});

const sectionTotal = (type) =>
    grouped.value[type].reduce((sum, r) => sum + Number(r.balance || 0), 0);

const isBalanced = computed(() => Number(props.totals?.difference || 0) === 0);

const printReport = () => window.print();
</script>

<template>
    <Layout title="Balance Sheet">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Balance Sheet
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8 space-y-5">
                <!-- Hero -->
                <div
                    class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 rounded-xl shadow-lg p-6 text-white"
                >
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20"
                            >
                                <ScaleIcon class="h-6 w-6 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">
                                    Balance Sheet
                                </h1>
                                <p class="text-slate-200 text-sm mt-0.5">
                                    Statement of financial position as of
                                    <span class="font-semibold">{{
                                        asOf
                                    }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                @click="printReport"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 hover:bg-white/25 text-white text-sm font-semibold rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/20"
                            >
                                <PrinterIcon class="h-4 w-4" />
                                Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                >
                    <div
                        class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                    >As of date</label
                                >
                                <input
                                    type="date"
                                    v-model="form.as_of"
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                />
                                <p class="text-xs text-gray-400 mt-1">
                                    Includes posted journal entries up to and
                                    including this date.
                                </p>
                            </div>

                            <div class="flex items-end gap-2">
                                <button
                                    @click="apply"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:from-indigo-700 hover:to-blue-700 transition-all duration-200"
                                >
                                    <ArrowPathIcon class="h-4 w-4" />
                                    Refresh
                                </button>
                                <button
                                    @click="resetToToday"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-all duration-200"
                                >
                                    Today
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border"
                                :class="
                                    isBalanced
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : 'bg-amber-50 text-amber-800 border-amber-200'
                                "
                            >
                                <span
                                    v-if="isBalanced"
                                    class="inline-flex items-center gap-1.5"
                                >
                                    Balanced
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5"
                                >
                                    <ExclamationTriangleIcon class="h-4 w-4" />
                                    Out of balance
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Total Assets
                        </p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1">
                            {{ formatCurrency(totals.assets) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Total Liabilities
                        </p>
                        <p class="text-2xl font-bold text-orange-700 mt-1">
                            {{ formatCurrency(totals.liabilities) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Total Equity
                        </p>
                        <p class="text-2xl font-bold text-indigo-700 mt-1">
                            {{ formatCurrency(totals.equity) }}
                        </p>
                    </div>
                </div>

                <!-- Main layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Assets -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r"
                            :class="sectionMeta('asset').gradient"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Assets
                                    </h3>
                                    <p class="text-xs text-white/80 mt-0.5">
                                        Resources owned by the business
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">
                                        Total Assets
                                    </p>
                                    <p class="text-lg font-bold">
                                        {{ formatCurrency(totals.assets) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                        >
                                            Account
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                        >
                                            Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-100"
                                >
                                    <tr
                                        v-for="r in grouped.asset"
                                        :key="r.id"
                                        class="hover:bg-emerald-50/40 transition-colors duration-150"
                                    >
                                        <td class="px-6 py-3">
                                            <div
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                {{ r.name }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ r.code }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-semibold text-emerald-700"
                                        >
                                            {{ formatCurrency(r.balance) }}
                                        </td>
                                    </tr>

                                    <tr v-if="grouped.asset.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No asset balances found for this
                                            date.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Total Assets
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold text-emerald-700"
                                        >
                                            {{ formatCurrency(totals.assets) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Liabilities + Equity -->
                    <div class="space-y-5">
                        <!-- Liabilities -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                        >
                            <div
                                class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r"
                                :class="sectionMeta('liability').gradient"
                            >
                                <div
                                    class="flex items-center justify-between text-white"
                                >
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            Liabilities
                                        </h3>
                                        <p class="text-xs text-white/80 mt-0.5">
                                            Obligations owed to others
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-white/80">
                                            Total Liabilities
                                        </p>
                                        <p class="text-lg font-bold">
                                            {{
                                                formatCurrency(
                                                    totals.liabilities,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-100"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Account
                                            </th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-100"
                                    >
                                        <tr
                                            v-for="r in grouped.liability"
                                            :key="r.id"
                                            class="hover:bg-orange-50/40 transition-colors duration-150"
                                        >
                                            <td class="px-6 py-3">
                                                <div
                                                    class="text-sm font-semibold text-gray-900"
                                                >
                                                    {{ r.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-400"
                                                >
                                                    {{ r.code }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-3 text-right text-sm font-semibold text-orange-700"
                                            >
                                                {{ formatCurrency(r.balance) }}
                                            </td>
                                        </tr>

                                        <tr
                                            v-if="
                                                grouped.liability.length === 0
                                            "
                                        >
                                            <td
                                                colspan="2"
                                                class="px-6 py-10 text-center text-sm text-gray-500"
                                            >
                                                No liability balances found for
                                                this date.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td
                                                class="px-6 py-3 text-sm font-semibold text-gray-700"
                                            >
                                                Total Liabilities
                                            </td>
                                            <td
                                                class="px-6 py-3 text-right text-sm font-bold text-orange-700"
                                            >
                                                {{
                                                    formatCurrency(
                                                        totals.liabilities,
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Equity -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                        >
                            <div
                                class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r"
                                :class="sectionMeta('equity').gradient"
                            >
                                <div
                                    class="flex items-center justify-between text-white"
                                >
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            Equity
                                        </h3>
                                        <p class="text-xs text-white/80 mt-0.5">
                                            Owner’s interest in the business
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-white/80">
                                            Total Equity
                                        </p>
                                        <p class="text-lg font-bold">
                                            {{ formatCurrency(totals.equity) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-100"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Account
                                            </th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                            >
                                                Amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-100"
                                    >
                                        <tr
                                            v-for="r in grouped.equity"
                                            :key="r.id"
                                            class="hover:bg-indigo-50/40 transition-colors duration-150"
                                        >
                                            <td class="px-6 py-3">
                                                <div
                                                    class="text-sm font-semibold text-gray-900"
                                                >
                                                    {{ r.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-400"
                                                >
                                                    {{ r.code }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-3 text-right text-sm font-semibold text-indigo-700"
                                            >
                                                {{ formatCurrency(r.balance) }}
                                            </td>
                                        </tr>

                                        <tr v-if="grouped.equity.length === 0">
                                            <td
                                                colspan="2"
                                                class="px-6 py-10 text-center text-sm text-gray-500"
                                            >
                                                No equity balances found for
                                                this date.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td
                                                class="px-6 py-3 text-sm font-semibold text-gray-700"
                                            >
                                                Total Equity
                                            </td>
                                            <td
                                                class="px-6 py-3 text-right text-sm font-bold text-indigo-700"
                                            >
                                                {{
                                                    formatCurrency(
                                                        totals.equity,
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Check -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Accounting equation
                                    </p>
                                    <p class="text-sm text-gray-700 mt-1">
                                        Assets should equal Liabilities + Equity
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        Liabilities + Equity
                                    </p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{
                                            formatCurrency(
                                                totals.liabilities_and_equity,
                                            )
                                        }}
                                    </p>
                                    <p
                                        class="text-xs mt-1"
                                        :class="
                                            isBalanced
                                                ? 'text-emerald-700'
                                                : 'text-amber-800'
                                        "
                                    >
                                        Difference:
                                        {{ formatCurrency(totals.difference) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="text-xs text-gray-400">
                    <p>
                        Notes: This report is generated from posted journal
                        entries. If the report is out of balance, review
                        unbalanced vouchers, missing opening balances, or
                        incorrect account classifications.
                    </p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
