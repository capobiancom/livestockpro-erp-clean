<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Calving Interval Report
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Calving Interval (CI) is the number of days between two
                        consecutive calvings of the same cow.
                        <span class="text-gray-500">
                            (Current Calving Date − Previous Calving Date)
                        </span>
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
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
                        Total intervals
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.total_intervals }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Average CI (days)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.average_ci_days }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Min {{ summary.min_ci_days }} • Max
                        {{ summary.max_ci_days }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-500 p-4 shadow-sm ring-1 ring-blue-300"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-blue-100"
                    >
                        Performance mix
                    </div>
                    <div class="mt-2 text-sm font-semibold text-white">
                        Excellent: {{ summary.excellent_count }} • Good:
                        {{ summary.good_count }} • Poor:
                        {{ summary.poor_count }}
                    </div>
                    <div class="mt-1 text-xs text-blue-100">
                        Target: 12 months (365 days)
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
                            >Performance</label
                        >
                        <select
                            v-model="form.performance"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option value="all">All</option>
                            <option value="excellent">Excellent (≤ 365)</option>
                            <option value="good">Good (366–420)</option>
                            <option value="poor">Poor (> 420)</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Animal</label
                        >
                        <select
                            v-model="form.animal_id"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option :value="null">All animals</option>
                            <option
                                v-for="a in animals"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.tag_number ?? a.tag }}
                                {{ a.name ? ` - ${a.name}` : "" }}
                            </option>
                        </select>
                    </div>

                    <div
                        class="md:col-span-12 flex items-end justify-end gap-2"
                    >
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
                        Showing {{ rows.length }} interval(s)
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
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Previous calving
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Current calving
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    CI (days)
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Performance
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No calving intervals found for the selected
                                    filters. A cow needs at least two calving
                                    records to calculate CI.
                                </td>
                            </tr>

                            <tr
                                v-for="(r, idx) in rows"
                                :key="idx"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">
                                        {{ r.tag_number }}
                                    </div>
                                    <div
                                        v-if="r.animal_name"
                                        class="text-xs text-gray-500"
                                    >
                                        {{ r.animal_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ r.previous_calving_date }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ r.current_calving_date }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900"
                                >
                                    {{ r.calving_interval_days }}
                                </td>
                                <td class="px-4 py-3">
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
                    Definition, targets & why it matters
                </h3>

                <p class="mt-1 text-sm text-gray-700">
                    <span class="font-medium">Calving Interval (days)</span>
                    =
                    <span class="font-medium"
                        >Date of Current Calving − Date of Previous
                        Calving</span
                    >
                </p>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div
                        class="rounded-lg bg-emerald-50 p-3 ring-1 ring-emerald-200"
                    >
                        <div class="text-xs font-semibold text-emerald-800">
                            Excellent
                        </div>
                        <div class="mt-1 text-sm text-emerald-800">
                            365 days
                        </div>
                    </div>
                    <div
                        class="rounded-lg bg-amber-50 p-3 ring-1 ring-amber-200"
                    >
                        <div class="text-xs font-semibold text-amber-800">
                            Good
                        </div>
                        <div class="mt-1 text-sm text-amber-800">
                            365–400 days
                        </div>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-3 ring-1 ring-rose-200">
                        <div class="text-xs font-semibold text-rose-800">
                            Poor
                        </div>
                        <div class="mt-1 text-sm text-rose-800">> 420 days</div>
                    </div>
                </div>

                <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
                    <li>
                        Shorter CI generally means more lactations, more milk,
                        more calves, and higher lifetime profit.
                    </li>
                    <li>
                        Longer CI increases non-productive days and feed cost
                        without milk, reducing profitability.
                    </li>
                    <li class="text-gray-600">
                        Related KPI: CI is influenced by Days Open.
                        <span class="font-medium"
                            >CI ≈ Gestation Length (~280 days) + Days Open</span
                        >.
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
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
    performance: props.filters.performance ?? "all",
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/calving-interval",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
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
        "/reports/calving-interval",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const url = new URL(
        "/reports/calving-interval/print",
        window.location.origin,
    );

    if (form.from) url.searchParams.set("from", form.from);
    if (form.to) url.searchParams.set("to", form.to);
    if (form.animal_id)
        url.searchParams.set("animal_id", String(form.animal_id));
    if (form.performance) url.searchParams.set("performance", form.performance);

    window.open(url.toString(), "_blank", "noopener,noreferrer");
}

function performanceBadgeClass(bucket) {
    if (bucket === "excellent") {
        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }
    if (bucket === "good") {
        return "bg-amber-50 text-amber-700 ring-amber-200";
    }
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function exportCsv() {
    const headers = [
        "Cow Tag",
        "Cow Name",
        "Previous Calving Date",
        "Current Calving Date",
        "Calving Interval (days)",
        "Performance",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.tag_number,
            r.animal_name ?? "",
            r.previous_calving_date,
            r.current_calving_date,
            r.calving_interval_days,
            r.performance_label,
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `calving-interval_${form.from || "from"}_${form.to || "to"}.csv`;
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
