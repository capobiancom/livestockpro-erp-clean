<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Fertility Performance per Cow
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Evaluate reproductive efficiency per cow using key KPIs:
                        Conception Rate, Services per Conception (SPC), Days
                        Open, Calving Interval, and Pregnancy Loss.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="resetFilters"
                    >
                        Reset
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
            <!-- KPI cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Date range
                    </div>
                    <div class="mt-2 text-sm font-semibold text-gray-900">
                        {{ summary.from }} → {{ summary.to }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Cows
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.cows }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Avg conception rate
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.avg_conception_rate }}%
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Avg SPC
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ formatNullable(summary.avg_spc) }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Lower is better (ideal: 1.5–2.0)
                    </div>
                </div>

                <div
                    class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-500 p-4 shadow-sm ring-1 ring-blue-300"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-blue-100"
                    >
                        Avg days open
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-white">
                        {{ formatNullable(summary.avg_days_open) }}
                    </div>
                    <div class="mt-1 text-xs text-blue-100">
                        Target: 85–115 days
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900">
                            Filters
                        </h2>
                        <div class="text-xs text-gray-500">
                            Tip: Filter by performance to focus on problem cows.
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

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Service type</label
                        >
                        <select
                            v-model="form.service_type"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All services</option>
                            <option value="ai">
                                Artificial Insemination (AI)
                            </option>
                            <option value="natural_mating">
                                Natural Mating
                            </option>
                            <option value="embryo_transfer">
                                Embryo Transfer
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Performance</label
                        >
                        <select
                            v-model="form.performance"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All</option>
                            <option value="excellent">Excellent</option>
                            <option value="moderate">Moderate</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>

                    <div class="md:col-span-6">
                        <label class="text-xs font-medium text-gray-700"
                            >Animal</label
                        >
                        <select
                            v-model="form.animal_id"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option :value="null">All animals</option>
                            <option
                                v-for="a in animals"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.tag }}
                                {{ a.name ? `- ${a.name}` : "" }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-6 flex items-end justify-end gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                            @click="applyFilters"
                        >
                            Apply
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                            @click="exportCsv"
                        >
                            Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                >
                    <h2 class="text-sm font-semibold text-gray-900">Results</h2>
                    <div class="text-xs text-gray-500">
                        Showing {{ rows.length }} cow(s)
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Cow
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Services
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Pregnancies
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Conception rate
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    SPC
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Days open
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Calving interval
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Loss rate
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Score
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Performance
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="10"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No cows found for the selected filters.
                                </td>
                            </tr>

                            <tr
                                v-for="r in rows"
                                :key="r.animal_id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">
                                        {{ r.tag_number || "—" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ r.animal_name || "Unnamed" }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.total_services }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.confirmed_pregnancies }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="
                                            rateBadgeClass(r.conception_rate)
                                        "
                                    >
                                        {{ r.conception_rate }}%
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ formatNullable(r.spc) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ formatNullable(r.avg_days_open) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{
                                        formatNullable(
                                            r.avg_calving_interval_days,
                                        )
                                    }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.pregnancy_loss_rate }}%
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900"
                                >
                                    {{ r.fertility_score }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="
                                            performanceBadgeClass(r.performance)
                                        "
                                    >
                                        {{ r.performance_label }}
                                    </span>
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

            <!-- Notes -->
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">
                    How this report is calculated
                </h3>

                <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Conception Rate (%)
                        </div>
                        <div class="mt-1 text-sm text-gray-700">
                            (Confirmed Pregnancies ÷ Total Services) × 100
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Services per Conception (SPC)
                        </div>
                        <div class="mt-1 text-sm text-gray-700">
                            Total Services ÷ Confirmed Pregnancies
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Days Open
                        </div>
                        <div class="mt-1 text-sm text-gray-700">
                            Days between Calving → Next Confirmed Conception
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Calving Interval
                        </div>
                        <div class="mt-1 text-sm text-gray-700">
                            Days between consecutive calvings
                        </div>
                    </div>
                </div>

                <p class="mt-3 text-sm text-gray-700">
                    <span class="font-medium">Fertility Score</span> is a
                    weighted index:
                    <span class="font-medium"
                        >(Conception Rate × 0.4) + (SPC Score × 0.2) + (Days
                        Open Score × 0.2) + (Pregnancy Loss Score × 0.2)</span
                    >.
                </p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { computed, reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    filters: { type: Object, required: true },
    animals: { type: Array, default: () => [] },
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    animal_id: props.filters.animal_id ?? null,
    service_type: props.filters.service_type ?? "all",
    performance: props.filters.performance ?? "all",
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/fertility-performance-per-cow",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
            service_type: form.service_type,
            performance: form.performance,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function resetFilters() {
    Inertia.get(
        "/reports/fertility-performance-per-cow",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const url = new URL(
        "/reports/fertility-performance-per-cow/print",
        window.location.origin,
    );

    // Pass current filters to print page
    if (form.from) url.searchParams.set("from", form.from);
    if (form.to) url.searchParams.set("to", form.to);
    if (form.animal_id)
        url.searchParams.set("animal_id", String(form.animal_id));
    if (form.service_type)
        url.searchParams.set("service_type", form.service_type);
    if (form.performance) url.searchParams.set("performance", form.performance);

    window.open(url.toString(), "_blank", "noopener,noreferrer");
}

function formatNullable(v) {
    if (v === null || v === undefined || v === "") {
        return "—";
    }
    return v;
}

function rateBadgeClass(rate) {
    if (rate >= 60) {
        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }
    if (rate >= 45) {
        return "bg-amber-50 text-amber-700 ring-amber-200";
    }
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function performanceBadgeClass(bucket) {
    if (bucket === "excellent") {
        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }
    if (bucket === "moderate") {
        return "bg-amber-50 text-amber-700 ring-amber-200";
    }
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function exportCsv() {
    const headers = [
        "Cow Tag",
        "Cow Name",
        "Services",
        "Confirmed Pregnancies",
        "Conception Rate",
        "SPC",
        "Avg Days Open",
        "Avg Calving Interval (days)",
        "Pregnancy Loss Rate",
        "Fertility Score",
        "Performance",
    ];

    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.tag_number ?? "",
            r.animal_name ?? "",
            r.total_services ?? "",
            r.confirmed_pregnancies ?? "",
            `${r.conception_rate ?? 0}%`,
            r.spc ?? "",
            r.avg_days_open ?? "",
            r.avg_calving_interval_days ?? "",
            `${r.pregnancy_loss_rate ?? 0}%`,
            r.fertility_score ?? "",
            r.performance_label ?? "",
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);

        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `fertility-performance-per-cow_${form.from || "from"}_${form.to || "to"}.csv`;
    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
}
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

    table {
        font-size: 12px;
    }
}
</style>
