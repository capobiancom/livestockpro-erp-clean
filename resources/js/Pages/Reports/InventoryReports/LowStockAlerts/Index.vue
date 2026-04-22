<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Low-stock Alerts
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Items below minimum stock level, calculated from stock
                        movements. Use this to prioritize replenishment.
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Alerts
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ number(summary.total_alerts) }}
                            </div>
                        </div>
                        <div
                            class="rounded-lg bg-red-50 p-2 text-red-700 ring-1 ring-red-100"
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
                                    d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.59c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.743-2.99l6.52-11.59zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z"
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
                                Total shortage
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ number(summary.total_shortage) }}
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
                                Shortage value
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ money(summary.total_shortage_value) }}
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
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1v-1h1a1 1 0 100-2h-1V7z"
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
                            Default shows only items below minimum.
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <label class="text-xs font-medium text-gray-700"
                            >Search</label
                        >
                        <input
                            v-model="form.q"
                            type="text"
                            placeholder="Search by name or SKU…"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            @keydown.enter.prevent="applyFilters"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Item type</label
                        >
                        <select
                            v-model="form.item_type"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All</option>
                            <option value="inventory">Inventory items</option>
                            <option value="medicine">Medicines</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Sort</label
                        >
                        <div class="mt-1 grid grid-cols-2 gap-2">
                            <select
                                v-model="form.sort"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="shortage">Shortage</option>
                                <option value="value">Shortage value</option>
                                <option value="name">Name</option>
                                <option value="stock">Current stock</option>
                                <option value="min">Min stock</option>
                            </select>
                            <select
                                v-model="form.direction"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="desc">Desc</option>
                                <option value="asc">Asc</option>
                            </select>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-700"
                            >Options</label
                        >
                        <label
                            class="mt-2 flex items-center gap-2 text-sm text-gray-700"
                        >
                            <input
                                v-model="form.only_below_min"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            />
                            Only below min
                        </label>
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
                    <h2 class="text-sm font-semibold text-gray-900">Alerts</h2>
                    <div class="text-xs text-gray-500">
                        Showing {{ rows.length }} item(s)
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Item
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Min
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Current
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Shortage
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Avg unit cost
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Shortage value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="7"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No low-stock alerts for the selected
                                    filters.
                                </td>
                            </tr>

                            <tr
                                v-for="r in rows"
                                :key="`${r.item_type}:${r.item_id}`"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">
                                        {{ r.name || "—" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ r.sku ? `SKU: ${r.sku}` : "—" }}
                                        <span v-if="r.unit">
                                            • Unit: {{ r.unit }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-700"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-200"
                                    >
                                        {{ typeLabel(r.item_type) }}
                                    </span>
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700"
                                >
                                    {{ number(r.min_quantity) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold"
                                    :class="
                                        r.current_stock > 0
                                            ? 'text-emerald-700'
                                            : r.current_stock < 0
                                              ? 'text-red-700'
                                              : 'text-gray-900'
                                    "
                                >
                                    {{ number(r.current_stock) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-red-700"
                                >
                                    {{ number(r.shortage) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700"
                                >
                                    {{ money(r.avg_unit_cost) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-gray-900"
                                >
                                    {{ money(r.shortage_value) }}
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
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
});

const form = reactive({
    q: props.filters.q ?? null,
    item_type: props.filters.item_type ?? "all",
    only_below_min: props.filters.only_below_min ?? true,
    sort: props.filters.sort ?? "shortage",
    direction: props.filters.direction ?? "desc",
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/inventory/low-stock-alerts",
        {
            q: form.q,
            item_type: form.item_type,
            only_below_min: form.only_below_min ? 1 : 0,
            sort: form.sort,
            direction: form.direction,
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
        "/reports/inventory/low-stock-alerts",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    const params = new URLSearchParams();

    if (form.q) params.set("q", form.q);
    if (form.item_type) params.set("item_type", form.item_type);
    params.set("only_below_min", form.only_below_min ? "1" : "0");
    if (form.sort) params.set("sort", form.sort);
    if (form.direction) params.set("direction", form.direction);

    const url = `/reports/inventory/low-stock-alerts/print?${params.toString()}`;
    window.open(url, "_blank", "noopener,noreferrer");
}

function exportCsv() {
    const headers = [
        "Item",
        "SKU",
        "Type",
        "Unit",
        "Min Quantity",
        "Current Stock",
        "Shortage",
        "Avg Unit Cost",
        "Shortage Value",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.name ?? "",
            r.sku ?? "",
            typeLabel(r.item_type),
            r.unit ?? "",
            r.min_quantity ?? "",
            r.current_stock ?? "",
            r.shortage ?? "",
            r.avg_unit_cost ?? "",
            r.shortage_value ?? "",
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `low-stock-alerts.csv`;
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

function typeLabel(itemType) {
    if (itemType === "App\\\\Models\\\\InventoryItem") return "Inventory";
    if (itemType === "App\\\\Models\\\\Medicine") return "Medicine";
    return "Item";
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
