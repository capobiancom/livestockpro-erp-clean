<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    ScaleIcon,
    PrinterIcon,
    ArrowPathIcon,
    FunnelIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    from: { type: String, required: true },
    to: { type: String, required: true },
    includeZero: { type: Boolean, default: false },
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
    include_zero: props.includeZero || false,
});

const apply = () => {
    Inertia.get(route("trial-balance.index"), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetToThisMonth = () => {
    const now = new Date();
    const first = new Date(now.getFullYear(), now.getMonth(), 1)
        .toISOString()
        .slice(0, 10);
    const today = now.toISOString().slice(0, 10);

    form.value.from = first;
    form.value.to = today;
    apply();
};

const formatCurrency = (value) => {
    const num = Number(value || 0);
    return `${currencySymbol.value} ${num.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const isBalanced = computed(() => Number(props.totals?.difference || 0) === 0);

const printReport = () => window.print();

const rowCount = computed(() => props.rows?.length || 0);
</script>

<template>
    <Layout title="Trial Balance">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Trial Balance
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
                                    Trial Balance
                                </h1>
                                <p class="text-slate-200 text-sm mt-0.5">
                                    Posted entries from
                                    <span class="font-semibold">{{
                                        from
                                    }}</span>
                                    to
                                    <span class="font-semibold">{{ to }}</span>
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
                        class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4"
                    >
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                    >From date</label
                                >
                                <input
                                    type="date"
                                    v-model="form.from"
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                />
                                <p class="text-xs text-gray-400 mt-1">
                                    Start of reporting period.
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5"
                                    >To date</label
                                >
                                <input
                                    type="date"
                                    v-model="form.to"
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                />
                                <p class="text-xs text-gray-400 mt-1">
                                    End of reporting period (inclusive).
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

                            <div class="sm:col-span-2 lg:col-span-3">
                                <label
                                    class="inline-flex items-center gap-2 text-sm text-gray-700 select-none"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.include_zero"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="inline-flex items-center gap-2"
                                    >
                                        <FunnelIcon class="h-4 w-4" />
                                        Include zero-activity accounts
                                    </span>
                                </label>
                                <p class="text-xs text-gray-400 mt-1">
                                    When enabled, accounts with no posted
                                    activity in the period will be shown with
                                    zero totals.
                                </p>
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
                                    <CheckCircleIcon class="h-4 w-4" />
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

                            <span
                                class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-full px-3 py-1"
                            >
                                {{ rowCount }} accounts
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
                            Total Debit (Balances)
                        </p>
                        <p class="text-2xl font-bold text-emerald-700 mt-1">
                            {{ formatCurrency(totals.debit_balance) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Net debit balances across all accounts.
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Total Credit (Balances)
                        </p>
                        <p class="text-2xl font-bold text-indigo-700 mt-1">
                            {{ formatCurrency(totals.credit_balance) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Net credit balances across all accounts.
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Difference
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="
                                isBalanced
                                    ? 'text-emerald-700'
                                    : 'text-amber-800'
                            "
                        >
                            {{ formatCurrency(totals.difference) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Should be 0.00 when all posted entries are balanced.
                        </p>
                    </div>
                </div>

                <!-- Table -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600"
                    >
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-white"
                        >
                            <div>
                                <h3 class="text-sm font-semibold">
                                    Trial Balance Details
                                </h3>
                                <p class="text-xs text-white/80 mt-0.5">
                                    Totals and net balances by account (posted
                                    entries only).
                                </p>
                            </div>
                            <div class="text-xs text-white/80">
                                Debit totals:
                                <span class="font-semibold">{{
                                    formatCurrency(totals.debit_total)
                                }}</span>
                                <span class="mx-2">•</span>
                                Credit totals:
                                <span class="font-semibold">{{
                                    formatCurrency(totals.credit_total)
                                }}</span>
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
                                        Debit Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Credit Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Debit Balance
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                    >
                                        Credit Balance
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr
                                    v-for="r in rows"
                                    :key="r.id"
                                    class="hover:bg-indigo-50/30 transition-colors duration-150"
                                >
                                    <td class="px-6 py-3">
                                        <div
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ r.name }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ r.code }}
                                            <span class="mx-1">•</span>
                                            {{ r.type }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-3 text-right text-sm font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(r.debit_total) }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(r.credit_total) }}
                                    </td>

                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-emerald-700"
                                    >
                                        {{ formatCurrency(r.debit_balance) }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-indigo-700"
                                    >
                                        {{ formatCurrency(r.credit_balance) }}
                                    </td>
                                </tr>

                                <tr v-if="rows.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-10 text-center text-sm text-gray-500"
                                    >
                                        No posted activity found for the
                                        selected period.
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td
                                        class="px-6 py-3 text-sm font-semibold text-gray-700"
                                    >
                                        Totals
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-gray-900"
                                    >
                                        {{ formatCurrency(totals.debit_total) }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(totals.credit_total)
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-emerald-700"
                                    >
                                        {{
                                            formatCurrency(totals.debit_balance)
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right text-sm font-bold text-indigo-700"
                                    >
                                        {{
                                            formatCurrency(
                                                totals.credit_balance,
                                            )
                                        }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                <div class="text-xs text-gray-400">
                    <p>
                        Notes: Trial Balance is generated from posted journal
                        entries only. If the report is out of balance, review
                        unposted vouchers, unbalanced journal entries, or
                        incorrect postings.
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
