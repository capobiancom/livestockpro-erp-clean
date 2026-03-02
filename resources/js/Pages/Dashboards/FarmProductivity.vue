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

            <!-- KPI cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <!-- Milk Total -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Milk (Total)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("milk_total_liters")
                        }}<span class="ml-1 text-sm font-semibold text-gray-500"
                            >L</span
                        >
                    </div>
                </div>
                <!-- Milk Avg Daily -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Milk (Avg / day)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("milk_avg_daily_liters")
                        }}<span class="ml-1 text-sm font-semibold text-gray-500"
                            >L</span
                        >
                    </div>
                </div>
                <!-- Milk Sales Revenue -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Milk Sales Revenue
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ money(props.kpis?.milk_sales_revenue ?? 0) }}
                    </div>
                </div>
                <!-- Expenses -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Expenses
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ money(props.kpis?.expenses_total ?? 0) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <!-- Feedings -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Feedings
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("feedings_count") }}
                    </div>
                </div>
                <!-- Health Events -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Health Events
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("health_events_count") }}
                    </div>
                </div>
                <!-- Vaccinations Due -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Vaccinations Due (7d)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("vaccinations_due_7d") }}
                    </div>
                </div>
                <!-- Active Animals -->
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Active Animals
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ safeKpi("active_animals") }}
                    </div>
                </div>
            </div>

            <!-- Trends -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Milk Trend Card -->
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="border-b border-gray-200 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Milk Trend
                                </h3>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Daily liters
                                </p>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ (safeTrend("milk") || []).length }} day(s)
                            </div>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <div
                            v-if="(safeTrend('milk') || []).length === 0"
                            class="py-10 text-center text-sm text-gray-500"
                        >
                            No trend data for this period.
                        </div>
                        <div
                            v-for="(s, idx) in safeTrend('milk')"
                            :key="idx"
                            class="flex items-center gap-3"
                        >
                            <div
                                class="w-12 text-xs text-gray-500 tabular-nums"
                            >
                                {{ String(s?.date || "").slice(5) }}
                            </div>
                            <div class="flex-1">
                                <div
                                    class="h-2 rounded-full bg-gray-100 overflow-hidden"
                                >
                                    <div
                                        class="h-2 rounded-full bg-cyan-500"
                                        :style="{
                                            width: trendWidth(
                                                s?.liters,
                                                'milk',
                                                'liters',
                                            ),
                                        }"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="w-24 text-right text-xs font-medium text-gray-700 tabular-nums"
                            >
                                {{
                                    Number.isFinite(Number(s?.liters ?? 0))
                                        ? Number(
                                              s?.liters ?? 0,
                                          ).toLocaleString()
                                        : "0"
                                }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Trend Card -->
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="border-b border-gray-200 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Revenue Trend
                                </h3>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Daily milk sales
                                </p>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ (safeTrend("revenue") || []).length }}
                                day(s)
                            </div>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <div
                            v-if="(safeTrend('revenue') || []).length === 0"
                            class="py-10 text-center text-sm text-gray-500"
                        >
                            No trend data for this period.
                        </div>
                        <div
                            v-for="(s, idx) in safeTrend('revenue')"
                            :key="idx"
                            class="flex items-center gap-3"
                        >
                            <div
                                class="w-12 text-xs text-gray-500 tabular-nums"
                            >
                                {{ String(s?.date || "").slice(5) }}
                            </div>
                            <div class="flex-1">
                                <div
                                    class="h-2 rounded-full bg-gray-100 overflow-hidden"
                                >
                                    <div
                                        class="h-2 rounded-full bg-emerald-500"
                                        :style="{
                                            width: trendWidth(
                                                s?.amount,
                                                'revenue',
                                                'amount',
                                            ),
                                        }"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="w-24 text-right text-xs font-medium text-gray-700 tabular-nums"
                            >
                                {{ money(s?.amount ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feeding Trend Card -->
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="border-b border-gray-200 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Feeding Trend
                                </h3>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Daily feeding count
                                </p>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ (safeTrend("feedings") || []).length }}
                                day(s)
                            </div>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <div
                            v-if="(safeTrend('feedings') || []).length === 0"
                            class="py-10 text-center text-sm text-gray-500"
                        >
                            No trend data for this period.
                        </div>
                        <div
                            v-for="(s, idx) in safeTrend('feedings')"
                            :key="idx"
                            class="flex items-center gap-3"
                        >
                            <div
                                class="w-12 text-xs text-gray-500 tabular-nums"
                            >
                                {{ String(s?.date || "").slice(5) }}
                            </div>
                            <div class="flex-1">
                                <div
                                    class="h-2 rounded-full bg-gray-100 overflow-hidden"
                                >
                                    <div
                                        class="h-2 rounded-full bg-amber-500"
                                        :style="{
                                            width: trendWidth(
                                                s?.count,
                                                'feedings',
                                                'count',
                                            ),
                                        }"
                                    ></div>
                                </div>
                            </div>
                            <div
                                class="w-24 text-right text-xs font-medium text-gray-700 tabular-nums"
                            >
                                {{
                                    Number.isFinite(Number(s?.count ?? 0))
                                        ? Number(s?.count ?? 0).toLocaleString()
                                        : "0"
                                }}
                            </div>
                        </div>
                    </div>
                </div>
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

function trendMaxValue(series, key) {
    const data = safeTrend(series) || [];
    if (!data.length) return 1;
    const max = Math.max(0, ...data.map((s) => Number(s?.[key] ?? 0)));
    return max > 0 ? max : 1;
}

function trendWidth(value, series, key) {
    const max = trendMaxValue(series, key);
    const n = Number(value ?? 0);
    return Math.max(2, Math.round((n / max) * 100)) + "%";
}

// Component definitions removed - using direct template rendering instead for better compatibility
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
