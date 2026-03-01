<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Animal Health Reports
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Review health events, disease treatments, and
                        vaccinations with filters and export-ready layout.
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
                        Total records
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ summary.total }}
                    </div>
                </div>

                <div
                    class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200"
                >
                    <div
                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                    >
                        Report type
                    </div>
                    <div class="mt-2 text-sm font-semibold text-gray-900">
                        {{ reportTypeLabel }}
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
                            Tip: Use a narrow date range for faster results.
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
                            >Report type</label
                        >
                        <select
                            v-model="form.event_type"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="health_events">Health Events</option>
                            <option value="disease_treatments">
                                Disease Treatments
                            </option>
                            <option value="vaccinations">Vaccinations</option>
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
                                {{
                                    a?.tag
                                        ? `${a.name || "Unnamed"} (${a?.tag})`
                                        : a.name || "Unnamed"
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-6">
                        <label class="text-xs font-medium text-gray-700"
                            >Health issue (Health Events only)</label
                        >
                        <select
                            v-model="form.health_issue_id"
                            :disabled="form.event_type !== 'health_events'"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-50 disabled:text-gray-400"
                        >
                            <option :value="null">All issues</option>
                            <option
                                v-for="h in healthIssues"
                                :key="h.id"
                                :value="h.id"
                            >
                                {{ h.name }}
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
                                    Category
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Issue / Vaccine / Disease
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Notes
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600"
                                >
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="6"
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
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-900"
                                >
                                    {{ r.animal }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="badgeClass(r.category)"
                                    >
                                        {{ r.category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ r.issue || "—" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ r.notes || "—" }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-700"
                                >
                                    {{ r.status || "—" }}
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

const props = defineProps({
    filters: { type: Object, required: true },
    animals: { type: Array, default: () => [] },
    healthIssues: { type: Array, default: () => [] },
    summary: { type: Object, required: true },
    rows: { type: Array, default: () => [] },
});

const form = reactive({
    from: props.filters.from ?? null,
    to: props.filters.to ?? null,
    animal_id: props.filters.animal_id ?? null,
    health_issue_id: props.filters.health_issue_id ?? null,
    event_type: props.filters.event_type ?? "health_events",
});

const reportTypeLabel = computed(() => {
    if (form.event_type === "disease_treatments") return "Disease Treatments";
    if (form.event_type === "vaccinations") return "Vaccinations";
    return "Health Events";
});

const generatedAt = computed(() => {
    const d = new Date();
    return d.toLocaleString();
});

function applyFilters() {
    Inertia.get(
        "/reports/animal-health",
        {
            from: form.from,
            to: form.to,
            animal_id: form.animal_id,
            health_issue_id:
                form.event_type === "health_events"
                    ? form.health_issue_id
                    : null,
            event_type: form.event_type,
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
        "/reports/animal-health",
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
}

function printReport() {
    window.print();
}

function badgeClass(category) {
    if (category === "Vaccination") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200";
    }
    if (category === "Disease Treatment") {
        return "bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200";
    }
    return "bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200";
}

function exportCsv() {
    const headers = ["Date", "Animal", "Category", "Issue", "Notes", "Status"];
    const lines = [headers.join(",")];

    props.rows.forEach((r) => {
        const row = [
            r.date,
            r.animal,
            r.category,
            r.issue ?? "",
            r.notes ?? "",
            r.status ?? "",
        ].map((v) => `"${String(v ?? "").replaceAll('"', '""')}"`);
        lines.push(row.join(","));
    });

    const blob = new Blob([lines.join("\n")], {
        type: "text/csv;charset=utf-8;",
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `animal-health-report_${form.from || "from"}_${form.to || "to"}.csv`;
    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
}
</script>

<style scoped>
@media print {
    /* Keep layout clean for printing */
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
