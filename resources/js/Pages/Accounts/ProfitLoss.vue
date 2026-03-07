<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    BanknotesIcon,
    PrinterIcon,
    ArrowPathIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    from: { type: String, required: true },
    to: { type: String, required: true },
    rows: { type: Array, required: true },
    totals: { type: Object, required: true },
});

const page = usePage();
const currencySymbol = computed(
    () => page.props.value.app_currency_symbol || "BDT",
);

const form = ref({
    from: props.from || "",
    to: props.to || "",
});

const apply = () => {
    Inertia.get(route("profit-loss.index"), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetToThisMonth = () => {
    const now = new Date();
    const from = new Date(now.getFullYear(), now.getMonth(), 1)
        .toISOString()
        .slice(0, 10);
    const to = now.toISOString().slice(0, 10);
    form.value.from = from;
    form.value.to = to;
    apply();
};

const formatCurrency = (value) => {
    const num = Number(value || 0);
    return `${currencySymbol.value} ${num.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const incomeRows = computed(() =>
    (props.rows || [])
        .filter((r) => r.type === "income")
        .filter((r) => Number(r.amount || 0) !== 0),
);

const expenseRows = computed(() =>
    (props.rows || [])
        .filter((r) => r.type === "expense")
        .filter((r) => Number(r.amount || 0) !== 0),
);

const netProfit = computed(() => Number(props.totals?.net_profit || 0));
const isProfit = computed(() => netProfit.value >= 0);

const printReport = () => {
    const url = route("profit-loss.print", {
        from: form.value.from,
        to: form.value.to,
    });
    window.open(url, "_blank", "noopener,noreferrer");
};
</script>

<template>
    <Layout title="Profit & Loss">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profit & Loss
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
                                <BanknotesIcon class="h-6 w-6 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">
                                    Profit & Loss
                                </h1>
                                <p class="text-slate-200 text-sm mt-0.5">
                                    Income statement for the period
                                    <span class="font-semibold"
                                        >{{ from }} → {{ to }}</span
                                    >
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
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                    >From</label
                                >
                                <input
                                    type="date"
                                    v-model="form.from"
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                />
                                <p class="text-xs text-gray-400 mt-1">
                                    Start date (posted entries only).
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                    >To</label
                                >
                                <input
                                    type="date"
                                    v-model="form.to"
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                />
                                <p class="text-xs text-gray-400 mt-1">
                                    End date (inclusive).
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
                                    @click="resetToThisMonth"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-all duration-200"
                                >
                                    This month
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border"
                                :class="
                                    isProfit
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : 'bg-rose-50 text-rose-700 border-rose-200'
                                "
                            >
                                <span class="inline-flex items-center gap-1.5">
                                    <ArrowTrendingUpIcon
                                        v-if="isProfit"
                                        class="h-4 w-4"
                                    />
                                    <ArrowTrendingDownIcon
                                        v-else
                                        class="h-4 w-4"
                                    />
                                    {{ isProfit ? "Net Profit" : "Net Loss" }}
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
                            Total Income
                        </p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1">
                            {{ formatCurrency(totals.total_income) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Total Expenses
                        </p>
                        <p class="text-2xl font-bold text-rose-700 mt-1">
                            {{ formatCurrency(totals.total_expenses) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Net Result
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="
                                isProfit ? 'text-emerald-700' : 'text-rose-700'
                            "
                        >
                            {{ formatCurrency(totals.net_profit) }}
                        </p>
                    </div>
                </div>

                <!-- Main layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Income -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Income
                                    </h3>
                                    <p class="text-xs text-white/80 mt-0.5">
                                        Revenue earned during the period
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">
                                        Total Income
                                    </p>
                                    <p class="text-lg font-bold">
                                        {{
                                            formatCurrency(totals.total_income)
                                        }}
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
                                        v-for="r in incomeRows"
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
                                            {{ formatCurrency(r.amount) }}
                                        </td>
                                    </tr>

                                    <tr v-if="incomeRows.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No income found for this period.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Total Income
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold text-emerald-700"
                                        >
                                            {{
                                                formatCurrency(
                                                    totals.total_income,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Expenses -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-700 via-rose-600 to-pink-600"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Expenses
                                    </h3>
                                    <p class="text-xs text-white/80 mt-0.5">
                                        Costs incurred during the period
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">
                                        Total Expenses
                                    </p>
                                    <p class="text-lg font-bold">
                                        {{
                                            formatCurrency(
                                                totals.total_expenses,
                                            )
                                        }}
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
                                        v-for="r in expenseRows"
                                        :key="r.id"
                                        class="hover:bg-rose-50/40 transition-colors duration-150"
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
                                            class="px-6 py-3 text-right text-sm font-semibold text-rose-700"
                                        >
                                            {{ formatCurrency(r.amount) }}
                                        </td>
                                    </tr>

                                    <tr v-if="expenseRows.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No expenses found for this period.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Total Expenses
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold text-rose-700"
                                        >
                                            {{
                                                formatCurrency(
                                                    totals.total_expenses,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="text-xs text-gray-400">
                    <p>
                        Notes: This report is generated from posted journal
                        entries within the selected period. Ensure income and
                        expense accounts are correctly classified in the Chart
                        of Accounts for accurate results.
                    </p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped></style>
