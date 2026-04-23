<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Feeding Cost Analysis
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Analyze feeding costs by date range and item. Use the
                        summary KPIs and export-ready table for reporting.
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
            <!-- Summary cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Date range
                            </div>
                            <div
                                class="mt-2 text-sm font-semibold text-gray-900"
                            >
                                {{ summary.from }} → {{ summary.to }}
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-blue-50 p-2 text-blue-700 ring-1 ring-blue-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM18 9H2v7a2 2 0 002 2h12a2 2 0 002-2V9z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Total cost
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ money(summary.total_cost) }}
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-emerald-50 p-2 text-emerald-700 ring-1 ring-emerald-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16z" />
                                <path
                                    fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v1.07a3.001 3.001 0 012 2.83 1 1 0 11-2 0 1 1 0 00-2 0c0 .552.448 1 1 1h.5a2.5 2.5 0 010 5H11V16a1 1 0 11-2 0v-.07a3.001 3.001 0 01-2-2.83 1 1 0 112 0 1 1 0 002 0c0-.552-.448-1-1-1H9.5a2.5 2.5 0 010-5H9V6a1 1 0 011-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Total quantity
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ number(summary.total_qty) }}
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-violet-50 p-2 text-violet-700 ring-1 ring-violet-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6zM7 7a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1H8a1 1 0 01-1-1V7zM12 4a1 1 0 011-1h2a1 1 0 011 1v13a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Avg unit cost
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ money(summary.avg_unit_cost) }}
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-amber-50 p-2 text-amber-700 ring-1 ring-amber-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3a1 1 0 00.293.707l1 1a1 1 0 001.414-1.414L11 9.586V7z"
                                    clip-rule="evenodd"
                                />
                            </svg>
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
                            Tip: Export CSV for Excel analysis.
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

                    <div class="md:col-span-4">
                        <label class="text-xs font-medium text-gray-700"
                            >Feed item</label
                        >
                        <select
                            v-model="form.inventory_item_id"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option :value="null">All items</option>
                            <option
                                v-for="i in items"
                                :key="i.id"
                                :value="i.id"
                            >
                                {{ i.name }}
                                {{ i.unit ? `(${i.unit})` : "" }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-700"
                            >Group by</label
                        >
                        <select
                            v-model="form.group_by"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="item">Item</option>
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

            <!-- Table -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                >
                    <h2 class="text-sm font-semibold text-gray-900">Results</h2>
                    <div class="text-xs text-gray-500">
                        Showing {{ rows.length }} record(s)
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
                                    Item
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Unit
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Unit cost
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Total cost
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="7"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No records found for the selected filters.
                                </td>
                            </tr>

                            <tr
                                v-for="(r, idx) in rows"
                                :key="idx"
                                class="hover:bg-gray-50"
                            >
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-900"
                                >
                                    {{ r.date }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ r.animal || "—" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ r.item || "—" }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900"
                                >
                                    {{ number(r.qty) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-700"
                                >
                                    {{ r.unit || "—" }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700"
                                >
                                    {{ money(r.unit_cost) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-gray-900"
                                >
                                    {{ money(r.total_cost) }}
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
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    filters: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    inventory_item_id: props.filters.inventory_item_id ?? null,
    group_by: props.filters.group_by ?? "day",
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/feeding-cost-analysis",
        {
            from: form.from,
            to: form.to,
            inventory_item_id: form.inventory_item_id,
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
        "/reports/feeding-cost-analysis",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const params = new URLSearchParams();

    if (form.from) params.set("from", form.from);
    if (form.to) params.set("to", form.to);
    if (form.inventory_item_id)
        params.set("inventory_item_id", String(form.inventory_item_id));
    if (form.group_by) params.set("group_by", form.group_by);

    const url = `/reports/feeding-cost-analysis/print?${params.toString()}`;
    window.open(url, "_blank", "noopener,noreferrer");
}

function exportCsv() {
    const headers = [
        "Date",
        "Animal",
        "Item",
        "Qty",
        "Unit",
        "Unit Cost",
        "Total Cost",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.date,
            r.animal ?? "",
            r.item ?? "",
            r.qty ?? "",
            r.unit ?? "",
            r.unit_cost ?? "",
            r.total_cost ?? "",
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `feeding-cost-analysis_${form.from || "from"}_${form.to || "to"}.csv`;
    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
}

const { money: moneyFormat } = useMoneyFormatter();

function money(v) {
    return moneyFormat(v ?? 0);
}

function number(v) {
    const n = Number(v ?? 0);
    return Number.isFinite(n) ? n.toLocaleString() : "0";
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
