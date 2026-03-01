<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Medicine Used per Disease
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Total medicine quantity and cost used to treat each
                        disease over a selected period.
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
                                Diseases
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ number(summary.total_diseases) }}
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
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm3 3a1 1 0 000 2h8a1 1 0 100-2H6zm0 4a1 1 0 000 2h8a1 1 0 100-2H6zm0 4a1 1 0 000 2h5a1 1 0 100-2H6z"
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
                                Medicines
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ number(summary.total_medicines) }}
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
                                <path
                                    d="M9 2a1 1 0 00-1 1v1H6a2 2 0 00-2 2v2h12V6a2 2 0 00-2-2h-2V3a1 1 0 00-1-1H9z"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M4 10v6a2 2 0 002 2h8a2 2 0 002-2v-6H4zm3 2a1 1 0 012 0v3a1 1 0 11-2 0v-3zm4 0a1 1 0 112 0v3a1 1 0 11-2 0v-3z"
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
                                {{ number(summary.total_quantity) }}
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
                                Total cost
                            </div>
                            <div
                                class="mt-2 text-2xl font-semibold text-gray-900"
                            >
                                {{ money(summary.total_cost) }}
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
                            Default shows current month.
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
                            >Disease</label
                        >
                        <select
                            v-model="form.disease_id"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option :value="null">All diseases</option>
                            <option
                                v-for="d in diseases"
                                :key="d.id"
                                :value="d.id"
                            >
                                {{ d.name }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >Search</label
                        >
                        <input
                            v-model="form.q"
                            type="text"
                            placeholder="Search disease or medicine…"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            @keydown.enter.prevent="applyFilters"
                        />
                    </div>

                    <div class="md:col-span-6">
                        <label class="text-xs font-medium text-gray-700"
                            >Sort</label
                        >
                        <div class="mt-1 grid grid-cols-2 gap-2">
                            <select
                                v-model="form.sort"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="cost">Total cost</option>
                                <option value="quantity">Total quantity</option>
                                <option value="disease">Disease</option>
                                <option value="medicine">Medicine</option>
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

            <!-- Table -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-4 py-3"
                >
                    <h2 class="text-sm font-semibold text-gray-900">Usage</h2>
                    <div class="text-xs text-gray-500">
                        Showing {{ rows.length }} row(s)
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Disease
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Medicine
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Total quantity
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
                                    colspan="4"
                                    class="px-4 py-10 text-center text-sm text-gray-500"
                                >
                                    No data found for the selected filters.
                                </td>
                            </tr>

                            <tr
                                v-for="r in rows"
                                :key="`${r.disease_id}:${r.medicine_id ?? 'null'}`"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">
                                        {{ r.disease_name || "—" }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">
                                        {{ r.medicine_name || "—" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ r.unit ? `Unit: ${r.unit}` : "—" }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900"
                                >
                                    {{ number(r.total_quantity) }}
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
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { computed, reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
    diseases: { type: Array, default: () => [] },
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    disease_id: props.filters.disease_id ?? null,
    q: props.filters.q ?? null,
    sort: props.filters.sort ?? "cost",
    direction: props.filters.direction ?? "desc",
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/inventory/medicine-used-per-disease",
        {
            from: form.from,
            to: form.to,
            disease_id: form.disease_id,
            q: form.q,
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
        "/reports/inventory/medicine-used-per-disease",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    window.print();
}

function exportCsv() {
    const headers = [
        "Disease",
        "Medicine",
        "Unit",
        "Total Quantity",
        "Total Cost",
    ];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.disease_name ?? "",
            r.medicine_name ?? "",
            r.unit ?? "",
            r.total_quantity ?? "",
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
    a.download = `medicine-used-per-disease.csv`;
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
