<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    ArrowsRightLeftIcon,
    PrinterIcon,
    ArrowPathIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    BuildingLibraryIcon,
    BriefcaseIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    from: { type: String, required: true },
    to: { type: String, required: true },
    rows: { type: Object, required: true }, // { operating: [], investing: [], financing: [] }
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
    Inertia.get(route("cash-flow.index"), form.value, {
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

const printReport = () => {
    const url = route("cash-flow.print", {
        from: form.value.from,
        to: form.value.to,
    });
    window.open(url, "_blank", "noopener,noreferrer");
};

const operatingRows = computed(() =>
    (props.rows?.operating || []).filter((r) => Number(r.movement || 0) !== 0),
);
const investingRows = computed(() =>
    (props.rows?.investing || []).filter((r) => Number(r.movement || 0) !== 0),
);
const financingRows = computed(() =>
    (props.rows?.financing || []).filter((r) => Number(r.movement || 0) !== 0),
);

const netChange = computed(() => Number(props.totals?.net_change || 0));
const isPositive = computed(() => netChange.value >= 0);

const sectionMeta = {
    operating: {
        title: "Operating Activities",
        subtitle:
            "Cash generated from core operations (indirect approximation)",
        icon: BriefcaseIcon,
        headerClass:
            "bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-600",
        rowHover: "hover:bg-indigo-50/50",
        amountClass: "text-indigo-700",
    },
    investing: {
        title: "Investing Activities",
        subtitle: "Cash used for long-term assets and investments",
        icon: BuildingLibraryIcon,
        headerClass:
            "bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600",
        rowHover: "hover:bg-emerald-50/50",
        amountClass: "text-emerald-700",
    },
    financing: {
        title: "Financing Activities",
        subtitle: "Cash from borrowings, repayments, and equity movements",
        icon: ChartBarIcon,
        headerClass: "bg-gradient-to-r from-rose-700 via-rose-600 to-pink-600",
        rowHover: "hover:bg-rose-50/50",
        amountClass: "text-rose-700",
    },
};

const signedClass = (value) => {
    const v = Number(value || 0);
    if (v > 0) return "text-emerald-700";
    if (v < 0) return "text-rose-700";
    return "text-gray-600";
};

const signedLabel = (value) => {
    const v = Number(value || 0);
    if (v > 0) return "Inflow";
    if (v < 0) return "Outflow";
    return "No change";
};
</script>

<template>
    <Layout title="Cash Flow">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cash Flow
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
                                <ArrowsRightLeftIcon
                                    class="h-6 w-6 text-white"
                                />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">Cash Flow</h1>
                                <p class="text-slate-200 text-sm mt-0.5">
                                    Statement of cash flows for
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
                                    isPositive
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : 'bg-rose-50 text-rose-700 border-rose-200'
                                "
                            >
                                <span class="inline-flex items-center gap-1.5">
                                    <ArrowTrendingUpIcon
                                        v-if="isPositive"
                                        class="h-4 w-4"
                                    />
                                    <ArrowTrendingDownIcon
                                        v-else
                                        class="h-4 w-4"
                                    />
                                    Net change in cash
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Operating
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="signedClass(totals.operating)"
                        >
                            {{ formatCurrency(totals.operating) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ signedLabel(totals.operating) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Investing
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="signedClass(totals.investing)"
                        >
                            {{ formatCurrency(totals.investing) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ signedLabel(totals.investing) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Financing
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="signedClass(totals.financing)"
                        >
                            {{ formatCurrency(totals.financing) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ signedLabel(totals.financing) }}
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                    >
                        <p
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                        >
                            Net Change
                        </p>
                        <p
                            class="text-2xl font-bold mt-1"
                            :class="signedClass(totals.net_change)"
                        >
                            {{ formatCurrency(totals.net_change) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ signedLabel(totals.net_change) }}
                        </p>
                    </div>
                </div>

                <!-- Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <!-- Operating -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100"
                            :class="sectionMeta.operating.headerClass"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div class="flex items-start gap-3">
                                    <component
                                        :is="sectionMeta.operating.icon"
                                        class="h-5 w-5 mt-0.5"
                                    />
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            {{ sectionMeta.operating.title }}
                                        </h3>
                                        <p class="text-xs text-white/80 mt-0.5">
                                            {{ sectionMeta.operating.subtitle }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">Total</p>
                                    <p class="text-lg font-bold">
                                        {{
                                            formatCurrency(
                                                totals.operating || 0,
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
                                            Cash impact
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-100"
                                >
                                    <tr
                                        v-for="r in operatingRows"
                                        :key="r.id"
                                        class="transition-colors duration-150"
                                        :class="sectionMeta.operating.rowHover"
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
                                            class="px-6 py-3 text-right text-sm font-semibold"
                                            :class="signedClass(r.movement)"
                                        >
                                            {{ formatCurrency(r.movement) }}
                                        </td>
                                    </tr>

                                    <tr v-if="operatingRows.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No operating movements found for
                                            this period.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Net cash from operating activities
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold"
                                            :class="
                                                signedClass(totals.operating)
                                            "
                                        >
                                            {{
                                                formatCurrency(
                                                    totals.operating || 0,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Investing -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100"
                            :class="sectionMeta.investing.headerClass"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div class="flex items-start gap-3">
                                    <component
                                        :is="sectionMeta.investing.icon"
                                        class="h-5 w-5 mt-0.5"
                                    />
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            {{ sectionMeta.investing.title }}
                                        </h3>
                                        <p class="text-xs text-white/80 mt-0.5">
                                            {{ sectionMeta.investing.subtitle }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">Total</p>
                                    <p class="text-lg font-bold">
                                        {{
                                            formatCurrency(
                                                totals.investing || 0,
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
                                            Cash impact
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-100"
                                >
                                    <tr
                                        v-for="r in investingRows"
                                        :key="r.id"
                                        class="transition-colors duration-150"
                                        :class="sectionMeta.investing.rowHover"
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
                                            class="px-6 py-3 text-right text-sm font-semibold"
                                            :class="signedClass(r.movement)"
                                        >
                                            {{ formatCurrency(r.movement) }}
                                        </td>
                                    </tr>

                                    <tr v-if="investingRows.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No investing movements found for
                                            this period.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Net cash from investing activities
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold"
                                            :class="
                                                signedClass(totals.investing)
                                            "
                                        >
                                            {{
                                                formatCurrency(
                                                    totals.investing || 0,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Financing -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div
                            class="px-6 py-4 border-b border-gray-100"
                            :class="sectionMeta.financing.headerClass"
                        >
                            <div
                                class="flex items-center justify-between text-white"
                            >
                                <div class="flex items-start gap-3">
                                    <component
                                        :is="sectionMeta.financing.icon"
                                        class="h-5 w-5 mt-0.5"
                                    />
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            {{ sectionMeta.financing.title }}
                                        </h3>
                                        <p class="text-xs text-white/80 mt-0.5">
                                            {{ sectionMeta.financing.subtitle }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">Total</p>
                                    <p class="text-lg font-bold">
                                        {{
                                            formatCurrency(
                                                totals.financing || 0,
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
                                            Cash impact
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-100"
                                >
                                    <tr
                                        v-for="r in financingRows"
                                        :key="r.id"
                                        class="transition-colors duration-150"
                                        :class="sectionMeta.financing.rowHover"
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
                                            class="px-6 py-3 text-right text-sm font-semibold"
                                            :class="signedClass(r.movement)"
                                        >
                                            {{ formatCurrency(r.movement) }}
                                        </td>
                                    </tr>

                                    <tr v-if="financingRows.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No financing movements found for
                                            this period.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-700"
                                        >
                                            Net cash from financing activities
                                        </td>
                                        <td
                                            class="px-6 py-3 text-right text-sm font-bold"
                                            :class="
                                                signedClass(totals.financing)
                                            "
                                        >
                                            {{
                                                formatCurrency(
                                                    totals.financing || 0,
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
                <div class="text-xs text-gray-400 space-y-1">
                    <p>
                        Notes: This Cash Flow report is generated from posted
                        journal entries within the selected period and grouped
                        by account type. It is an indirect approximation and may
                        include non-cash movements depending on your accounting
                        setup.
                    </p>
                    <p>
                        Best practice: Tag cash/bank accounts explicitly and
                        separate non-cash items (depreciation, accruals) for a
                        fully compliant cash flow statement.
                    </p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
@media print {
    aside,
    header {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>
