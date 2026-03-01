<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        AI vs Natural Breeding Success
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Compare conception performance between
                        <span class="font-medium text-gray-800"
                            >Artificial Insemination (AI)</span
                        >
                        and
                        <span class="font-medium text-gray-800"
                            >Natural Mating</span
                        >.
                        <span class="text-gray-500">
                            (Confirmed Pregnancies ÷ Total Services) × 100
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
                        Total services
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.total_services }}
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
                        {{ summary.confirmed_pregnancies }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-500 p-4 shadow-sm ring-1 ring-blue-300"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-blue-100"
                    >
                        Success rate
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-white">
                        {{ summary.success_rate }}%
                    </div>
                    <div class="mt-1 text-xs text-blue-100">
                        Services per conception:
                        <span class="font-medium">{{
                            summary.services_per_conception ?? "—"
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Benchmarks + First service -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 lg:col-span-2"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">
                                Industry benchmarks (Dairy)
                            </h2>
                            <p class="mt-1 text-xs text-gray-500">
                                Benchmarks are reference ranges; your farm may
                                differ by breed, nutrition, heat detection, and
                                management.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-3"
                        >
                            <div class="text-xs font-medium text-gray-600">
                                Artificial Insemination (AI)
                            </div>
                            <div
                                class="mt-1 text-lg font-semibold text-gray-900"
                            >
                                {{ summary.benchmarks.ai.min }}–{{
                                    summary.benchmarks.ai.max
                                }}%
                            </div>
                            <div class="mt-2 h-2 w-full rounded bg-gray-200">
                                <div
                                    class="h-2 rounded bg-blue-600"
                                    :style="{
                                        width: `${summary.benchmarks.ai.max}%`,
                                    }"
                                />
                            </div>
                        </div>

                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-3"
                        >
                            <div class="text-xs font-medium text-gray-600">
                                Natural Mating
                            </div>
                            <div
                                class="mt-1 text-lg font-semibold text-gray-900"
                            >
                                {{ summary.benchmarks.natural_mating.min }}–{{
                                    summary.benchmarks.natural_mating.max
                                }}%
                            </div>
                            <div class="mt-2 h-2 w-full rounded bg-gray-200">
                                <div
                                    class="h-2 rounded bg-emerald-600"
                                    :style="{
                                        width: `${summary.benchmarks.natural_mating.max}%`,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <h2 class="text-sm font-semibold text-gray-900">
                        First-service conception
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Earliest service per animal within the selected range.
                    </p>

                    <div class="mt-4">
                        <div class="text-xs text-gray-500">
                            First-service success rate
                        </div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">
                            {{
                                summary.first_service
                                    .first_service_conception_rate
                            }}%
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ summary.first_service.confirmed_first_services }}
                            confirmed out of
                            {{ summary.first_service.total_first_services }}
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <div
                            v-for="m in summary.first_service.by_method"
                            :key="m.method"
                            class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2"
                        >
                            <div class="text-xs font-medium text-gray-700">
                                {{ m.method_label }}
                            </div>
                            <div class="text-xs text-gray-600">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="rateBadgeClass(m.rate)"
                                >
                                    {{ m.rate }}%
                                </span>
                            </div>
                        </div>
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
                            Tip: Group by technician (AI) or bull (Natural) to
                            spot performance differences.
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
                            >Method</label
                        >
                        <select
                            v-model="form.method"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All methods</option>
                            <option value="ai">
                                Artificial Insemination (AI)
                            </option>
                            <option value="natural_mating">
                                Natural Mating
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Group by</label
                        >
                        <select
                            v-model="form.group_by"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="method">Method</option>
                            <option value="month">Month</option>
                            <option value="technician">
                                Technician (AI only)
                            </option>
                            <option value="bull">Bull (Natural only)</option>
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
                                {{ a.tag_number }}
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
                                    Services
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Confirmed
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Success rate
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    SPC
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No services found for the selected filters.
                                </td>
                            </tr>

                            <tr
                                v-for="(r, idx) in rows"
                                :key="idx"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ r.group }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.services }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.confirmed }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="rateBadgeClass(r.success_rate)"
                                    >
                                        {{ r.success_rate }}%
                                    </span>
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ r.services_per_conception ?? "—" }}
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
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <h3 class="text-sm font-semibold text-gray-900">
                        Methods overview
                    </h3>

                    <div class="mt-3 space-y-3 text-sm text-gray-700">
                        <div>
                            <div class="font-medium text-gray-900">
                                Artificial Insemination (AI)
                            </div>
                            <div class="mt-1 text-gray-700">
                                Semen is manually inserted into the cow by a
                                technician.
                            </div>
                            <ul class="mt-2 list-disc pl-5 text-gray-700">
                                <li>
                                    <span class="font-medium">Advantages:</span>
                                    Better genetics, no need to keep a bull,
                                    disease control, controlled breeding
                                    program.
                                </li>
                                <li>
                                    <span class="font-medium">Risks:</span> Heat
                                    detection must be accurate; depends on
                                    technician skill.
                                </li>
                            </ul>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="font-medium text-gray-900">
                                Natural Breeding
                            </div>
                            <div class="mt-1 text-gray-700">
                                Natural mating with a live bull.
                            </div>
                            <ul class="mt-2 list-disc pl-5 text-gray-700">
                                <li>
                                    <span class="font-medium">Advantages:</span>
                                    Simple process, less human intervention,
                                    better for small farms.
                                </li>
                                <li>
                                    <span class="font-medium">Risks:</span>
                                    Disease spread, limited genetic improvement,
                                    bull maintenance cost.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <h3 class="text-sm font-semibold text-gray-900">
                        KPIs & financial impact
                    </h3>

                    <div class="mt-3 text-sm text-gray-700">
                        <p>
                            <span class="font-medium">Success Rate (%)</span> =
                            <span class="font-medium"
                                >(Confirmed Pregnancies ÷ Total Services) ×
                                100</span
                            >
                        </p>

                        <p class="mt-3">
                            <span class="font-medium"
                                >Services Per Conception (SPC)</span
                            >
                            =
                            <span class="font-medium"
                                >Total Services ÷ Total Pregnancies</span
                            >
                            <span class="text-gray-500">(lower is better)</span>
                        </p>

                        <div
                            class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3"
                        >
                            <div
                                class="text-xs font-semibold uppercase tracking-wide text-amber-800"
                            >
                                Why this matters
                            </div>
                            <ul
                                class="mt-2 list-disc pl-5 text-sm text-amber-900"
                            >
                                <li>
                                    AI failure increases semen, hormone, and
                                    labor costs.
                                </li>
                                <li>
                                    Lower conception delays calving and milk
                                    income.
                                </li>
                                <li>
                                    This KPI directly impacts profitability and
                                    reproduction efficiency.
                                </li>
                            </ul>
                        </div>
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
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    animal_id: props.filters.animal_id ?? null,
    method: props.filters.method ?? "all",
    group_by: props.filters.group_by ?? "method",
});

const groupHeader = computed(() => {
    if (form.group_by === "month") return "Month";
    if (form.group_by === "technician") return "Technician";
    if (form.group_by === "bull") return "Bull";
    return "Method";
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/ai-vs-natural-breeding-success",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
            method: form.method,
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
        "/reports/ai-vs-natural-breeding-success",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    window.print();
}

function rateBadgeClass(rate) {
    if (rate >= 70) {
        return "bg-emerald-50 text-emerald-700 ring-emerald-200";
    }
    if (rate >= 40) {
        return "bg-amber-50 text-amber-700 ring-amber-200";
    }
    return "bg-rose-50 text-rose-700 ring-rose-200";
}

function exportCsv() {
    const headers = [
        groupHeader.value,
        "Services",
        "Confirmed",
        "Success rate",
        "SPC",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.group,
            r.services,
            r.confirmed,
            `${r.success_rate}%`,
            r.services_per_conception ?? "",
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `ai-vs-natural-breeding-success_${form.from || "from"}_${form.to || "to"}.csv`;
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
