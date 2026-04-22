<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { computed, ref, watch } from "vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import { Inertia } from "@inertiajs/inertia";

const collectingInvoiceId = ref(null);

const confirmCollectOpen = ref(false);
const confirmCollectRow = ref(null);
const confirmCollectLoading = ref(false);

function canCollect(row) {
    return (
        row?.type === "invoice" && ["unpaid", "pending"].includes(row?.status)
    );
}

function collectInvoice(row) {
    const invoiceId = row?.invoice?.id;
    if (!invoiceId) return;

    confirmCollectRow.value = row;
    confirmCollectOpen.value = true;
}

function closeCollectConfirm() {
    confirmCollectOpen.value = false;
    confirmCollectRow.value = null;
    confirmCollectLoading.value = false;
}

function confirmCollect() {
    const invoiceId = confirmCollectRow.value?.invoice?.id;
    if (!invoiceId) return;

    confirmCollectLoading.value = true;
    collectingInvoiceId.value = invoiceId;

    Inertia.post(
        `/admin/collections/invoices/${invoiceId}/collect`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                collectingInvoiceId.value = null;
                confirmCollectLoading.value = false;
                closeCollectConfirm();
            },
        },
    );
}

const props = defineProps({
    filters: { type: Object, required: true },
    kpis: { type: Object, required: true },
    farms: { type: Array, required: true },
    invoice_wise: { type: Array, required: true },
    farm_wise: { type: Array, required: true },
});

const page = usePage();

const month = ref(props.filters?.month || "");
const farmId = ref(props.filters?.farm_id || null);

watch(
    () => props.filters,
    (f) => {
        month.value = f?.month || "";
        farmId.value = f?.farm_id || null;
    },
    { deep: true },
);

function formatMoneyFromCents(cents) {
    return (Number(cents || 0) / 100).toFixed(2);
}

const selectedFarmName = computed(() => {
    if (!farmId.value) return "All farms";
    const f = props.farms.find((x) => String(x.id) === String(farmId.value));
    return f?.name || `Farm #${farmId.value}`;
});

function applyFilters() {
    Inertia.get(
        "/admin/collections",
        {
            month: month.value || undefined,
            farm_id: farmId.value || undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

function clearFarm() {
    farmId.value = null;
    applyFilters();
}
</script>

<template>
    <Head title="Collections" />

    <AppLayout>
        <template #title>
            <div>
                <div class="text-xs text-gray-500">Administration</div>
                <div class="text-lg font-semibold text-gray-900">
                    Collections
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div
                class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
            >
                <div class="flex flex-col md:flex-row md:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600"
                            >Month</label
                        >
                        <input
                            v-model="month"
                            type="month"
                            class="mt-1 w-full md:w-64 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600"
                            >Farm</label
                        >
                        <select
                            v-model="farmId"
                            class="mt-1 w-full md:w-80 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option :value="null">All farms</option>
                            <option
                                v-for="f in farms"
                                :key="f.id"
                                :value="f.id"
                            >
                                {{ f.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-sm"
                            @click="applyFilters"
                        >
                            Apply
                        </button>
                        <button
                            type="button"
                            class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm font-medium"
                            @click="clearFarm"
                            :disabled="!farmId"
                        >
                            Clear farm
                        </button>
                    </div>
                </div>

                <div class="mt-3 text-xs text-gray-500">
                    Showing:
                    <span class="font-medium">{{ selectedFarmName }}</span>
                    <span v-if="kpis?.month_start && kpis?.month_end">
                        ({{ kpis.month_start }} to {{ kpis.month_end }})
                    </span>
                </div>
            </div>

            <!-- KPI row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Receivable (unpaid/pending)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{
                            formatMoneyFromCents(
                                kpis.this_month_receivable_cents,
                            )
                        }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Collection (succeeded payments)
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{
                            formatMoneyFromCents(
                                kpis.this_month_collection_cents,
                            )
                        }}
                    </div>
                </div>
            </div>

            <!-- Farm-wise -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-4 border-b">
                    <div class="text-sm font-semibold text-gray-900">
                        Farm-wise collection
                    </div>
                    <div class="text-xs text-gray-500">
                        Total succeeded payments grouped by farm
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr class="text-left border-b">
                                <th class="py-3 px-4 font-medium">Farm</th>
                                <th class="py-3 px-4 font-medium">Payments</th>
                                <th class="py-3 px-4 font-medium text-right">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in farm_wise"
                                :key="row.farm?.id"
                                class="border-b hover:bg-gray-50/60"
                            >
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">
                                        {{ row.farm?.name ?? "-" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID: {{ row.farm?.id ?? "-" }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700">
                                    {{ row.payments_count }}
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-semibold text-gray-900"
                                >
                                    {{ formatMoneyFromCents(row.total_cents) }}
                                </td>
                            </tr>

                            <tr v-if="farm_wise.length === 0">
                                <td
                                    colspan="3"
                                    class="py-10 text-center text-gray-500"
                                >
                                    No payments found for this filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoice-wise -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-4 border-b">
                    <div class="text-sm font-semibold text-gray-900">
                        Invoice-wise collection
                    </div>
                    <div class="text-xs text-gray-500">
                        Latest succeeded payments (max 500 rows)
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr class="text-left border-b">
                                <th class="py-3 px-4 font-medium">Paid at</th>
                                <th class="py-3 px-4 font-medium">Farm</th>
                                <th class="py-3 px-4 font-medium">Invoice</th>
                                <th class="py-3 px-4 font-medium text-right">
                                    Amount
                                </th>
                                <th class="py-3 px-4 font-medium text-right">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="p in invoice_wise"
                                :key="p.id"
                                class="border-b hover:bg-gray-50/60"
                            >
                                <td class="py-3 px-4 text-gray-700">
                                    {{ p.paid_at ?? "-" }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">
                                        {{ p.farm?.name ?? "-" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID: {{ p.farm?.id ?? "-" }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700">
                                    <div class="font-medium text-gray-900">
                                        {{ p.invoice?.invoice_number ?? "-" }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Date:
                                        {{ p.invoice?.invoice_date ?? "-" }}
                                    </div>
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-semibold text-gray-900"
                                >
                                    {{ formatMoneyFromCents(p.amount_cents) }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button
                                        v-if="canCollect(p)"
                                        type="button"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                        :disabled="
                                            collectingInvoiceId ===
                                            p.invoice?.id
                                        "
                                        @click="collectInvoice(p)"
                                    >
                                        {{
                                            collectingInvoiceId ===
                                            p.invoice?.id
                                                ? "Collecting..."
                                                : "Collect"
                                        }}
                                    </button>
                                    <span v-else class="text-xs text-gray-400"
                                        >-</span
                                    >
                                </td>
                            </tr>

                            <tr v-if="invoice_wise.length === 0">
                                <td
                                    colspan="5"
                                    class="py-10 text-center text-gray-500"
                                >
                                    No payments found for this filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <ConfirmModal
            :show="confirmCollectOpen"
            title="Confirm Collection"
            :message="`Mark invoice ${confirmCollectRow?.invoice?.invoice_number ?? ''} as collected?`"
            confirmText="Yes, Collect"
            cancelText="Cancel"
            :loading="confirmCollectLoading"
            @confirm="confirmCollect"
            @close="closeCollectConfirm"
        />
    </AppLayout>
</template>
