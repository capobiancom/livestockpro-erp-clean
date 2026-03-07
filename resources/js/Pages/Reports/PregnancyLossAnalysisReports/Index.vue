<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Pregnancy Loss Analysis
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 flex flex-col gap-2">
                        <span>
                            Measure the rate and timing of pregnancies that were
                            confirmed but later lost (abortion, embryonic death,
                            miscarriage).
                        </span>
                        <span class="text-gray-500">
                            (Pregnancy Losses ÷ Total Confirmed Pregnancies) ×
                            100
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
                        Confirmed pregnancies
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.total_confirmed_pregnancies }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Pregnancy losses
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.pregnancy_losses }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Abortion: {{ summary.loss_type_counts?.abortion ?? 0 }},
                        Embryonic death:
                        {{ summary.loss_type_counts?.embryonic_death ?? 0 }},
                        Miscarriage:
                        {{ summary.loss_type_counts?.miscarriage ?? 0 }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-gradient-to-br from-rose-600 to-rose-500 p-4 shadow-sm ring-1 ring-rose-300"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-rose-100"
                    >
                        Pregnancy loss rate
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-white">
                        {{ summary.pregnancy_loss_rate }}%
                    </div>
                    <div class="mt-1 text-xs text-rose-100">
                        Benchmark: 8–15% total loss (dairy cattle).
                    </div>
                </div>
            </div>

            <!-- Benchmarks -->
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Industry benchmarks (Dairy cattle)
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Use these ranges to interpret your results. If total
                            pregnancy loss is above 15%, it usually indicates a
                            management issue.
                        </p>
                    </div>
                    <div
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                        :class="
                            benchmarkBadgeClass(summary.pregnancy_loss_rate)
                        "
                    >
                        {{ benchmarkLabel(summary.pregnancy_loss_rate) }}
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Loss type
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Normal range
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    Early embryonic loss (before 42 days)
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    5–15%
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    Mid-gestation loss
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    2–5%
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    Late abortion
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    < 2%
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    Total pregnancy loss
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    8–15%
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                            Tip: Group by month to see trends.
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
                            >Group by</label
                        >
                        <select
                            v-model="form.group_by"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="loss_timing">Loss timing</option>
                            <option value="month">Month</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
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
                        Showing {{ rows.length }} group(s)
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    {{ groupHeader }}
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Confirmed
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Losses
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Loss rate
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="4"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No confirmed pregnancies found for the
                                    selected filters.
                                </td>
                            </tr>

                            <tr
                                v-for="(r, idx) in normalizedRows"
                                :key="idx"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ r.group }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.confirmed }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.losses }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="rateBadgeClass(r.rate)"
                                    >
                                        {{ r.rate }}%
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

            <!-- Details -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                >
                    <h2 class="text-sm font-semibold text-gray-900">
                        Pregnancy details
                    </h2>
                    <div class="text-xs text-gray-500">
                        Showing {{ details.length }} record(s)
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Pregnancy ID
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Animal ID
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Confirmed date
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Loss type
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Loss date
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Days from confirm
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Timing bucket
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Calving date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="details.length === 0">
                                <td
                                    colspan="8"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No records to display.
                                </td>
                            </tr>

                            <tr
                                v-for="d in details"
                                :key="d.pregnancy_id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ d.pregnancy_id }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium">
                                            {{ d.animal_tag ?? d.animal_id }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ID: {{ d.animal_id }}
                                            <span v-if="d.animal_name">
                                                · {{ d.animal_name }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ d.confirmed_date ?? "-" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="lossTypeBadgeClass(d.loss_type)"
                                    >
                                        {{ lossTypeLabel(d.loss_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ d.loss_date ?? "-" }}
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ d.days_from_confirm ?? "-" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ timingLabel(d.loss_timing) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ d.calving_date ?? "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">
                    Definition & formula
                </h3>
                <p class="mt-1 text-sm text-gray-700">
                    <span class="font-medium">Pregnancy Loss Rate (%)</span>
                    =
                    <span class="font-medium"
                        >(Number of Pregnancy Losses ÷ Total Confirmed
                        Pregnancies) × 100</span
                    >
                </p>

                <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Why it matters
                        </div>
                        <ul class="mt-2 list-disc pl-5 text-sm text-gray-700">
                            <li>Poor nutrition</li>
                            <li>Disease (Brucellosis, Leptospirosis, etc.)</li>
                            <li>Heat stress</li>
                            <li>Genetic issues</li>
                            <li>Poor herd management</li>
                            <li>Hormonal problems</li>
                        </ul>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                        <div class="text-xs font-semibold text-gray-700">
                            Business impact
                        </div>
                        <ul class="mt-2 list-disc pl-5 text-sm text-gray-700">
                            <li>Higher cost per calf</li>
                            <li>Lower farm profitability</li>
                            <li>More open days and re-breeding costs</li>
                        </ul>
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

const props = defineProps({
    filters: { type: Object, required: true },
    animals: { type: Array, default: () => [] },
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
    details: { type: Array, default: () => [] },
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    animal_id: props.filters.animal_id ?? null,
    group_by: props.filters.group_by ?? "loss_timing",
});

const groupHeader = computed(() => {
    return form.group_by === "month" ? "Month (confirmed)" : "Loss timing";
});

const normalizedRows = computed(() => {
    // Backend returns different shapes depending on group_by.
    // Normalize to a consistent table: group, confirmed, losses, rate.
    if (form.group_by === "month") {
        return props.rows.map((r) => ({
            group: r.group,
            confirmed: r.confirmed ?? 0,
            losses: r.losses ?? 0,
            rate: r.rate ?? 0,
        }));
    }

    // loss_timing: backend returns only losses per bucket.
    // We compute rate against total confirmed pregnancies for context.
    const totalConfirmed = props.summary.total_confirmed_pregnancies ?? 0;

    return props.rows.map((r) => {
        const losses =
            r.losses ?? r.losses_count ?? r.lossesTotal ?? r.losses ?? r.losses;
        const l = typeof losses === "number" ? losses : (r.losses ?? 0);
        const rate =
            totalConfirmed > 0 ? round2((l / totalConfirmed) * 100) : 0;

        return {
            group: r.group,
            confirmed: totalConfirmed,
            losses: l,
            rate,
        };
    });
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/pregnancy-loss-analysis",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
            group_by: form.group_by,
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
        "/reports/pregnancy-loss-analysis",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const qs = new URLSearchParams();

    if (form.from) qs.set("from", form.from);
    if (form.to) qs.set("to", form.to);
    if (form.animal_id) qs.set("animal_id", String(form.animal_id));
    if (form.group_by) qs.set("group_by", form.group_by);

    const url =
        "/reports/pregnancy-loss-analysis/print" +
        (qs.toString() ? `?${qs.toString()}` : "");

    window.open(url, "_blank", "noopener,noreferrer");
}

function rateBadgeClass(rate) {
    if (rate <= 15) {
        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }
    if (rate <= 20) {
        return "bg-amber-50 text-amber-700 ring-amber-200";
    }
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function benchmarkLabel(rate) {
    if (rate <= 15) return "Within benchmark (≤ 15%)";
    return "Above benchmark (> 15%)";
}

function benchmarkBadgeClass(rate) {
    if (rate <= 15) return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function exportCsv() {
    const headers = [
        "Pregnancy ID",
        "Animal",
        "Confirmed date",
        "Loss type",
        "Loss date",
        "Days from confirm",
        "Timing bucket",
        "Calving date",
    ];
    const lines = [headers.join(",")];

    props.details.forEach((d) => {
        const row = [
            d.pregnancy_id,
            [d.animal_tag, d.animal_name, `ID:${d.animal_id}`]
                .filter(Boolean)
                .join(" - "),
            d.confirmed_date,
            lossTypeLabel(d.loss_type),
            d.loss_date,
            d.days_from_confirm,
            timingLabel(d.loss_timing),
            d.calving_date,
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `pregnancy-loss-analysis_${form.from || "from"}_${form.to || "to"}.csv`;
    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
}

function lossTypeLabel(type) {
    return (
        {
            abortion: "Abortion",
            embryonic_death: "Embryonic death",
            miscarriage: "Miscarriage",
            unknown: "Unknown",
        }[type] ?? "Unknown"
    );
}

function lossTypeBadgeClass(type) {
    return (
        {
            abortion: "bg-rose-50 text-rose-700 ring-rose-200",
            embryonic_death: "bg-amber-50 text-amber-700 ring-amber-200",
            miscarriage: "bg-orange-50 text-orange-700 ring-orange-200",
            unknown: "bg-gray-50 text-gray-700 ring-gray-200",
        }[type] ?? "bg-gray-50 text-gray-700 ring-gray-200"
    );
}

function timingLabel(bucket) {
    return (
        {
            early_embryonic: "Early embryonic (< 42 days)",
            mid_gestation: "Mid-gestation (42–179 days)",
            late_abortion: "Late abortion (≥ 180 days)",
        }[bucket] ?? "-"
    );
}

function round2(n) {
    return Math.round((Number(n) + Number.EPSILON) * 100) / 100;
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
