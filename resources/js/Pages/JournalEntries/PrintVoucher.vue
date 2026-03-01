<template>
    <div class="print-voucher bg-white font-sans text-gray-800 min-h-screen">
        <!-- No-print action bar (hidden when printing) -->
        <div
            class="no-print fixed top-0 left-0 right-0 z-50 bg-gray-800 text-white px-6 py-3 flex items-center justify-between shadow-lg"
        >
            <div class="flex items-center gap-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-indigo-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                    />
                </svg>
                <span class="text-sm font-semibold">
                    Journal Voucher — #{{
                        String(journalEntry.id).padStart(4, "0")
                    }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all duration-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                    Print Voucher
                </button>
                <button
                    @click="window.close()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 hover:bg-gray-500 text-white text-sm rounded-lg transition-all duration-200"
                >
                    Close
                </button>
            </div>
        </div>

        <!-- Voucher Content -->
        <div class="max-w-3xl mx-auto p-10 pt-20 print-content">
            <!-- Voucher Top Border Accent -->
            <div
                class="h-2 rounded-t-lg bg-gradient-to-r from-indigo-600 to-blue-500 mb-0"
            ></div>

            <!-- Main Voucher Box -->
            <div class="border border-gray-300 rounded-b-lg shadow-sm">
                <!-- Header Section -->
                <div
                    class="px-8 py-6 border-b-2 border-gray-200 flex items-start justify-between"
                >
                    <div>
                        <h2
                            class="text-xl font-black text-gray-900 uppercase tracking-tight"
                        >
                            {{ journalEntry.farm?.name || "Farm Name" }}
                        </h2>
                        <p
                            v-if="journalEntry.farm?.address"
                            class="text-sm text-gray-500 mt-1"
                        >
                            {{ journalEntry.farm.address }}
                        </p>
                        <p
                            v-if="journalEntry.farm?.phone"
                            class="text-sm text-gray-500"
                        >
                            Tel: {{ journalEntry.farm.phone }}
                        </p>
                        <p
                            v-if="journalEntry.farm?.email"
                            class="text-sm text-gray-500"
                        >
                            {{ journalEntry.farm.email }}
                        </p>
                    </div>
                    <div class="text-right">
                        <h1
                            class="text-3xl font-black uppercase tracking-widest text-indigo-700"
                        >
                            Journal Voucher
                        </h1>
                        <p
                            class="mt-1 text-sm font-bold uppercase text-gray-500 tracking-wide"
                        >
                            {{
                                referenceTypeLabel(journalEntry.reference_type)
                            }}
                        </p>
                    </div>
                </div>

                <!-- Voucher Meta Grid -->
                <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        <!-- Left column -->
                        <div class="space-y-2">
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Voucher No.</span
                                >
                                <span class="text-sm font-black text-indigo-700"
                                    >#{{
                                        String(journalEntry.id).padStart(4, "0")
                                    }}</span
                                >
                            </div>
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Voucher Type</span
                                >
                                <span
                                    class="text-sm font-semibold text-indigo-600"
                                    >{{
                                        referenceTypeLabel(
                                            journalEntry.reference_type,
                                        )
                                    }}</span
                                >
                            </div>
                            <div
                                v-if="journalEntry.reference_id"
                                class="flex items-baseline gap-3"
                            >
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Reference ID</span
                                >
                                <span class="text-sm text-gray-700">{{
                                    journalEntry.reference_id
                                }}</span>
                            </div>
                        </div>
                        <!-- Right column -->
                        <div class="space-y-2">
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Entry Date</span
                                >
                                <span
                                    class="text-sm font-semibold text-gray-900"
                                    >{{
                                        formatDate(journalEntry.entry_date)
                                    }}</span
                                >
                            </div>
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Status</span
                                >
                                <span
                                    class="text-sm font-bold capitalize"
                                    :class="statusColor(journalEntry.status)"
                                    >{{ journalEntry.status }}</span
                                >
                            </div>
                            <div
                                v-if="journalEntry.createdBy"
                                class="flex items-baseline gap-3"
                            >
                                <span
                                    class="w-32 text-xs font-bold text-gray-500 uppercase tracking-wide flex-shrink-0"
                                    >Prepared By</span
                                >
                                <span class="text-sm text-gray-700">{{
                                    journalEntry.createdBy?.name
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div
                    v-if="journalEntry.description"
                    class="px-8 py-3 border-b border-gray-200 bg-white"
                >
                    <span
                        class="text-xs font-bold text-gray-500 uppercase tracking-wide"
                        >Description:
                    </span>
                    <span class="text-sm text-gray-700">{{
                        journalEntry.description
                    }}</span>
                </div>

                <!-- Journal Lines Table -->
                <div class="px-8 py-6">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-white">
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide border border-gray-700 w-8"
                                >
                                    #
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide border border-gray-700"
                                >
                                    Account
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide border border-gray-700"
                                >
                                    Narration
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide border border-gray-700 w-36"
                                >
                                    Debit
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide border border-gray-700 w-36"
                                >
                                    Credit
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(line, index) in journalEntry.lines"
                                :key="line.id"
                                :class="
                                    index % 2 === 0 ? 'bg-white' : 'bg-gray-50'
                                "
                            >
                                <td
                                    class="border border-gray-200 px-4 py-2.5 text-sm text-gray-500 text-center"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-900"
                                >
                                    {{ line.account?.name || "—" }}
                                </td>
                                <td
                                    class="border border-gray-200 px-4 py-2.5 text-sm text-gray-600"
                                >
                                    {{ line.narration || "—" }}
                                </td>
                                <td
                                    class="border border-gray-200 px-4 py-2.5 text-sm font-medium text-right"
                                    :class="
                                        parseFloat(line.debit_amount) > 0
                                            ? 'text-gray-900'
                                            : 'text-gray-300'
                                    "
                                >
                                    {{
                                        parseFloat(line.debit_amount) > 0
                                            ? formatCurrency(line.debit_amount)
                                            : "—"
                                    }}
                                </td>
                                <td
                                    class="border border-gray-200 px-4 py-2.5 text-sm font-medium text-right"
                                    :class="
                                        parseFloat(line.credit_amount) > 0
                                            ? 'text-gray-900'
                                            : 'text-gray-300'
                                    "
                                >
                                    {{
                                        parseFloat(line.credit_amount) > 0
                                            ? formatCurrency(line.credit_amount)
                                            : "—"
                                    }}
                                </td>
                            </tr>

                            <!-- Empty lines placeholder -->
                            <tr v-if="!journalEntry.lines?.length">
                                <td
                                    colspan="5"
                                    class="border border-gray-200 px-4 py-4 text-sm text-gray-400 text-center"
                                >
                                    No journal lines found
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-800 text-white">
                                <td
                                    colspan="3"
                                    class="border border-gray-700 px-4 py-3 text-sm font-black uppercase tracking-wide text-right"
                                >
                                    Total
                                </td>
                                <td
                                    class="border border-gray-700 px-4 py-3 text-sm font-black text-right text-emerald-300"
                                >
                                    {{ formatCurrency(totalDebit) }}
                                </td>
                                <td
                                    class="border border-gray-700 px-4 py-3 text-sm font-black text-right text-blue-300"
                                >
                                    {{ formatCurrency(totalCredit) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Amount in Words -->
                <div class="px-8 pb-4">
                    <div
                        class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-center"
                    >
                        <p
                            class="text-xs font-bold text-indigo-500 uppercase tracking-wide mb-1"
                        >
                            Total Amount in Words
                        </p>
                        <p
                            class="text-sm font-bold text-indigo-900 uppercase italic"
                        >
                            {{ totalAmountInWords }}
                        </p>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="px-8 pb-8">
                    <div class="grid grid-cols-3 gap-8 mt-10">
                        <div class="text-center">
                            <div class="border-t-2 border-gray-400 pt-2 mt-10">
                                <p
                                    class="text-xs font-bold text-gray-600 uppercase tracking-wide"
                                >
                                    Prepared By
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{
                                        journalEntry.createdBy?.name ||
                                        "__________"
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="border-t-2 border-gray-400 pt-2 mt-10">
                                <p
                                    class="text-xs font-bold text-gray-600 uppercase tracking-wide"
                                >
                                    Checked By
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    __________
                                </p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="border-t-2 border-gray-400 pt-2 mt-10">
                                <p
                                    class="text-xs font-bold text-gray-600 uppercase tracking-wide"
                                >
                                    Approved By
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    __________
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-between"
                >
                    <p class="text-xs text-gray-400">
                        {{ journalEntry.farm?.name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Printed: {{ printDate }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import convertNumberToWords from "@/Utils/numberToWords";

const props = defineProps({
    journalEntry: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const currencySymbol = computed(
    () => page.props.value.app_currency_symbol || "BDT",
);

const formatCurrency = (value) => {
    if (value === null || value === undefined)
        return `${currencySymbol.value} 0.00`;
    const numValue = parseFloat(value);
    if (isNaN(numValue)) return `${currencySymbol.value} 0.00`;
    return `${currencySymbol.value} ${numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const referenceTypeLabel = (type) => {
    const labels = {
        sale: "Sale Voucher",
        purchase: "Purchase Voucher",
        payroll: "Payroll Voucher",
        treatment: "Treatment Voucher",
        milk_sale: "Milk Sale Voucher",
        expense: "Expense Voucher",
        cash: "Cash Voucher",
    };
    return (
        labels[type] ||
        type?.replace(/_/g, " ")?.replace(/\b\w/g, (l) => l.toUpperCase()) ||
        "—"
    );
};

const statusColor = (status) => {
    const colors = {
        draft: "text-blue-600",
        posted: "text-green-600",
        reversed: "text-red-600",
    };
    return colors[status] || "text-gray-600";
};

const totalDebit = computed(
    () =>
        props.journalEntry.lines?.reduce(
            (sum, line) => sum + parseFloat(line.debit_amount || 0),
            0,
        ) || 0,
);

const totalCredit = computed(
    () =>
        props.journalEntry.lines?.reduce(
            (sum, line) => sum + parseFloat(line.credit_amount || 0),
            0,
        ) || 0,
);

const totalAmountInWords = computed(() => {
    const amount = parseFloat(totalDebit.value);
    if (!isNaN(amount)) {
        return convertNumberToWords(amount);
    }
    return "zero only";
});

const printDate = computed(() =>
    new Date().toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }),
);

onMounted(() => {
    window.print();
});
</script>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
        background-color: #fff;
    }
    .no-print {
        display: none !important;
    }
    .print-content {
        padding-top: 0 !important;
    }
    .print-voucher {
        box-shadow: none;
    }
}
</style>
