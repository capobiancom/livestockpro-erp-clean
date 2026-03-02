<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Farm Productivity Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Monitor production, sales, feeding activity, and health
                        signals for the selected period.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="resetToLast30Days"
                    >
                        Last 30 days
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800"
                        @click="printReport"
                    >
                        Print
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900">
                            Date range
                        </h2>
                        <div class="text-xs text-gray-500">
                            Data is calculated from farm activity within the
                            selected period.
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-12">
                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >From</label
                        >
                        <input
                            v-model="form.from"
                            type="date"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >To</label
                        >
                        <input
                            v-model="form.to"
                            type="date"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="md:col-span-6 flex items-end justify-end gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                            @click="applyFilters"
                        >
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- debug output (visible on remote, helps verify data) -->
            <pre class="bg-yellow-50 text-xs text-red-600 p-2 overflow-auto">
                {{ JSON.stringify(props, null, 2) }}
            </pre>

            <!-- KPI cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <KpiCard
                    label="Milk (Total)"
                    :value="safeKpi('milk_total_liters')"
                    suffix="L"
                    tone="cyan"
                />
                <KpiCard
                    label="Milk (Avg / day)"
                    :value="safeKpi('milk_avg_daily_liters')"
                    suffix="L"
                    tone="indigo"
                />
                <KpiCard
                    label="Milk Sales Revenue"
                    :value="money(props.kpis?.milk_sales_revenue ?? 0)"
                    tone="emerald"
                />
                <KpiCard
                    label="Expenses"
                    :value="money(props.kpis?.expenses_total ?? 0)"
                    tone="rose"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <KpiCard
                    label="Feedings"
                    :value="safeKpi('feedings_count')"
                    tone="amber"
                />
                <KpiCard
                    label="Health Events"
                    :value="safeKpi('health_events_count')"
                    tone="red"
                />
                <KpiCard
                    label="Vaccinations Due (7d)"
                    :value="safeKpi('vaccinations_due_7d')"
                    tone="violet"
                />
                <KpiCard
                    label="Active Animals"
                    :value="safeKpi('active_animals')"
                    tone="slate"
                />
            </div>

            <!-- Trends -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <TrendCard
                    title="Milk Trend"
                    subtitle="Daily liters"
                    :series="safeTrend('milk')"
                    value-key="liters"
                    tone="cyan"
                />
                <TrendCard
                    title="Revenue Trend"
                    subtitle="Daily milk sales"
                    :series="safeTrend('revenue')"
                    value-key="amount"
                    tone="emerald"
                    :format="money"
                />
                <TrendCard
                    title="Feeding Trend"
                    subtitle="Daily feeding count"
                    :series="safeTrend('feedings')"
                    value-key="count"
                    tone="amber"
                />
            </div>

            <!-- Tables -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div
                        class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                    >
                        <h2 class="text-sm font-semibold text-gray-900">
                            Upcoming Vaccinations (30 days)
                        </h2>
                        <div class="text-xs text-gray-500">
                            {{ tables.upcoming_vaccinations.length }} item(s)
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Due date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Animal
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Disease
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-if="
                                        tables.upcoming_vaccinations.length ===
                                        0
                                    "
                                >
                                    <td
                                        colspan="3"
                                        class="px-4 py-10 text-center text-sm text-gray-500"
                                    >
                                        No upcoming vaccinations found.
                                    </td>
                                </tr>

                                <tr
                                    v-for="(
                                        r, idx
                                    ) in tables.upcoming_vaccinations"
                                    :key="idx"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="whitespace-nowrap px-4 py-3 text-sm text-gray-900"
                                    >
                                        {{ r.due_date || "—" }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ r.animal || "—" }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ r.disease || "—" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div
                        class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                    >
                        <h2 class="text-sm font-semibold text-gray-900">
                            Recent Health Events
                        </h2>
                        <div class="text-xs text-gray-500">
                            {{ tables.recent_health_events.length }} item(s)
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Animal
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                    >
                                        Type
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-if="
                                        tables.recent_health_events.length === 0
                                    "
                                >
                                    <td
                                        colspan="3"
                                        class="px-4 py-10 text-center text-sm text-gray-500"
                                    >
                                        No recent health events found.
                                    </td>
                                </tr>

                                <tr
                                    v-for="(
                                        r, idx
                                    ) in tables.recent_health_events"
                                    :key="idx"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="whitespace-nowrap px-4 py-3 text-sm text-gray-900"
                                    >
                                        {{ r.date || "—" }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ r.animal || "—" }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ r.type || "—" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500"
                    >
                        Generated at {{ generatedAt }}
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { computed, reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    filters: { type: Object, required: true },
    kpis: { type: Object, required: true },
    trends: { type: Object, required: true },
    tables: { type: Object, required: true },
});

// log props on mount (helps verify what the server sent)
import { onMounted, watchEffect } from "vue";

onMounted(() => {
    console.log("FarmProductivity props:", props);
});

watchEffect(() => {
    // reactive log to catch updates
    console.log("FP props changed", props);
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
});

const generatedAt = computed(() => new Date().toLocaleString());

function applyFilters() {
    Inertia.get(
        "/farm-productivity-dashboard",
        { from: form.from, to: form.to },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function resetToLast30Days() {
    const to = new Date();
    const from = new Date();
    from.setDate(to.getDate() - 30);

    form.from = from.toISOString().slice(0, 10);
    form.to = to.toISOString().slice(0, 10);
    applyFilters();
}

function printReport() {
    window.print();
}

const { money: moneyFormat } = useMoneyFormatter();

function money(v) {
    return moneyFormat(v ?? 0);
}

function number(v) {
    const n = Number(v ?? 0);
    return Number.isFinite(n) ? n.toLocaleString() : "0";
}

// safe helpers that tolerate missing props
function safeKpi(key) {
    return number(props.kpis?.[key] ?? 0);
}

function safeTrend(series, key) {
    return props.trends?.[series] || [];
}

/**
 * Local components (kept in-file for speed and consistency)
 */
const KpiCard = {
    props: {
        label: { type: String, required: true },
        value: { type: String, required: true },
        suffix: { type: String, default: "" },
        tone: { type: String, default: "slate" },
    },
    computed: {
        toneClasses() {
            const map = {
                cyan: "bg-cyan-50 text-cyan-700 ring-cyan-100",
                indigo: "bg-indigo-50 text-indigo-700 ring-indigo-100",
                emerald: "bg-emerald-50 text-emerald-700 ring-emerald-100",
                rose: "bg-rose-50 text-rose-700 ring-rose-100",
                amber: "bg-amber-50 text-amber-700 ring-amber-100",
                red: "bg-red-50 text-red-700 ring-red-100",
                violet: "bg-violet-50 text-violet-700 ring-violet-100",
                slate: "bg-slate-50 text-slate-700 ring-slate-100",
            };
            return map[this.tone] ?? map.slate;
        },
    },
    template: `
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ label }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ value }}<span v-if="suffix" class="ml-1 text-sm font-semibold text-gray-500">{{ suffix }}</span>
                    </div>
                </div>
                <div class="rounded-lg p-2 ring-1" :class="toneClasses" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6zM7 7a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1H8a1 1 0 01-1-1V7zM12 4a1 1 0 011-1h2a1 1 0 011 1v13a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
            </div>
        </div>
    `,
};

const TrendCard = {
    props: {
        title: { type: String, required: true },
        subtitle: { type: String, default: "" },
        series: { type: Array, default: () => [] },
        valueKey: { type: String, required: true },
        tone: { type: String, default: "slate" },
        format: { type: Function, default: null },
    },
    computed: {
        maxValue() {
            const vals = (this.series || []).map((s) =>
                Number(s?.[this.valueKey] ?? 0),
            );
            const max = Math.max(0, ...vals);
            return max > 0 ? max : 1;
        },
        toneBar() {
            const map = {
                cyan: "bg-cyan-500",
                emerald: "bg-emerald-500",
                amber: "bg-amber-500",
                slate: "bg-slate-500",
            };
            return map[this.tone] ?? map.slate;
        },
    },
    methods: {
        labelFor(d) {
            if (!d) return "—";
            // show MM-DD
            return String(d).slice(5);
        },
        valueFor(v) {
            const n = Number(v ?? 0);
            if (!Number.isFinite(n)) return 0;
            return n;
        },
        displayValue(v) {
            if (this.format) return this.format(v);
            const n = Number(v ?? 0);
            return Number.isFinite(n) ? n.toLocaleString() : "0";
        },
        widthPct(v) {
            const n = this.valueFor(v);
            return Math.max(2, Math.round((n / this.maxValue) * 100));
        },
    },
    template: `
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ title }}</h3>
                        <p v-if="subtitle" class="mt-0.5 text-xs text-gray-500">{{ subtitle }}</p>
                    </div>
                    <div class="text-xs text-gray-500">{{ (series || []).length }} day(s)</div>
                </div>
            </div>

            <div class="p-4 space-y-2">
                <div v-if="(series || []).length === 0" class="py-10 text-center text-sm text-gray-500">
                    No trend data for this period.
                </div>

                <div v-for="(s, idx) in series" :key="idx" class="flex items-center gap-3">
                    <div class="w-12 text-xs text-gray-500 tabular-nums">{{ labelFor(s.date) }}</div>
                    <div class="flex-1">
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-2 rounded-full" :class="toneBar" :style="{ width: widthPct(s[valueKey]) + '%' }"></div>
                        </div>
                    </div>
                    <div class="w-24 text-right text-xs font-medium text-gray-700 tabular-nums">
                        {{ displayValue(s[valueKey]) }}
                    </div>
                </div>
            </div>
        </div>
    `,
};
</script>

<style scoped>
@media print {
    :deep(.app-header),
    :deep(.app-sidebar) {
        display: none !important;
    }

    :deep(main) {
        padding: 0 !important;
        background: white !important;
    }
}
</style>
