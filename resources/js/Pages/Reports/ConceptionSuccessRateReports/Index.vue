<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Conception Success Rate
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 flex flex-col gap-2">
                        <span>
                            Track the percentage of breeding attempts that
                            result in confirmed pregnancy.
                        </span>
                        <span class="text-gray-500">
                            (Confirmed Pregnancies ÷ Total Breeding Attempts) ×
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
                        Total attempts
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.total_attempts }}
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
                        Conception success rate
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-white">
                        {{ summary.conception_success_rate }}%
                    </div>
                    <div class="mt-1 text-xs text-blue-100">
                        Based on breeding attempts within the selected range.
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
                            Tip: Group by month to spot trends.
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
                            >Group by</label
                        >
                        <select
                            v-model="form.group_by"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="service_type">Service type</option>
                            <option value="month">Month</option>
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
                                {{ `${a.tag}(${a.name})` }}
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
                                    Attempts
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="4"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No breeding attempts found for the selected
                                    filters.
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
                                    {{ r.attempts }}
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

            <!-- Notes -->
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">
                    Definition & formula
                </h3>
                <p class="mt-1 text-sm text-gray-700">
                    <span class="font-medium">Conception Success Rate (%)</span>
                    =
                    <span class="font-medium"
                        >(Number of Confirmed Pregnancies ÷ Total Breeding
                        Attempts) × 100</span
                    >
                </p>
                <ul class="mt-2 list-disc pl-5 text-sm text-gray-700">
                    <li>
                        A breeding attempt includes: Artificial Insemination
                        (AI), Natural mating, and Embryo transfer.
                    </li>
                    <li>Each service attempt is counted as 1 record.</li>
                    <li>
                        “Confirmed pregnancy” is counted when a pregnancy record
                        exists for the attempt and has a confirmed date.
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
    service_type: props.filters.service_type ?? "all",
    group_by: props.filters.group_by ?? "service_type",
});

const groupHeader = computed(() => {
    return form.group_by === "month" ? "Month" : "Service type";
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/conception-success-rate",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
            service_type: form.service_type,
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
        "/reports/conception-success-rate",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const params = new URLSearchParams();

    if (form.from) params.set("from", form.from);
    if (form.to) params.set("to", form.to);
    if (form.animal_id) params.set("animal_id", String(form.animal_id));
    if (form.service_type) params.set("service_type", form.service_type);
    if (form.group_by) params.set("group_by", form.group_by);

    const url = `/reports/conception-success-rate/print?${params.toString()}`;

    window.open(url, "_blank", "noopener,noreferrer");
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
        "Attempts",
        "Confirmed",
        "Success rate",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [r.group, r.attempts, r.confirmed, `${r.rate}%`].map(
            (v) => `"${String(v ?? "").replaceAll('"', '""')}"`,
        );
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `conception-success-rate_${form.from || "from"}_${form.to || "to"}.csv`;
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
