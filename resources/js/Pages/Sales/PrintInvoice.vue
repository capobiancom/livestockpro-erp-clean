<template>
    <div class="print-invoice p-8 bg-white font-sans text-gray-800">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">INVOICE</h1>
                    <p class="text-lg text-gray-600">
                        #{{ sale.invoice_number || sale.id }}
                    </p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        {{ sale.farm?.name || "Farm Name" }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        Date: {{ formatDate(sale.invoice_date) }}
                    </p>
                </div>
            </div>

            <hr class="border-gray-300 mb-8" />

            <!-- Billing Information -->
            <div class="grid grid-cols-2 gap-8 mb-12">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Customer Details:
                    </h3>
                    <p class="font-medium">
                        {{ sale.customer?.name || "N/A" }}
                    </p>
                    <p
                        v-if="sale.customer?.contact_person"
                        class="text-sm text-gray-600"
                    >
                        Contact: {{ sale.customer.contact_person }}
                    </p>
                    <p
                        v-if="sale.customer?.phone"
                        class="text-sm text-gray-600"
                    >
                        Phone: {{ sale.customer.phone }}
                    </p>
                    <p
                        v-if="sale.customer?.email"
                        class="text-sm text-gray-600"
                    >
                        Email: {{ sale.customer.email }}
                    </p>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Invoice From:
                    </h3>
                    <p class="font-medium">
                        {{ sale.farm?.name || "N/A" }}
                    </p>
                    <p v-if="sale.farm?.address" class="text-sm text-gray-600">
                        {{ sale.farm.address }}
                    </p>
                    <p v-if="sale.farm?.phone" class="text-sm text-gray-600">
                        {{ sale.farm.phone }}
                    </p>
                    <p v-if="sale.farm?.email" class="text-sm text-gray-600">
                        {{ sale.farm.email }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-12">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300">
                            <th
                                class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase"
                            >
                                Item
                            </th>
                            <th
                                class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase"
                            >
                                Quantity
                            </th>
                            <th
                                class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase"
                            >
                                Unit Price
                            </th>
                            <th
                                class="text-right py-3 px-4 text-sm font-semibold text-gray-700 uppercase"
                            >
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in sale.sales_items"
                            :key="item.id"
                            class="border-b border-gray-200"
                        >
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ item.item?.name || "N/A" }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ formatNumber(item.quantity) }}
                                {{ item.item?.unit || "" }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ formatCurrency(item.unit_price) }}
                            </td>
                            <td
                                class="text-right py-3 px-4 text-sm text-gray-700"
                            >
                                {{ formatCurrency(getItemSubtotal(item)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="flex justify-end mb-12">
                <div class="w-full md:w-1/2">
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            >Total Amount:</span
                        >
                        <span class="text-sm font-medium text-gray-900">{{
                            formatCurrency(sale.total_amount)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            >Paid Amount:</span
                        >
                        <span class="text-sm font-medium text-green-600">{{
                            formatCurrency(sale.paid_amount)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            >Due Amount:</span
                        >
                        <span
                            class="text-sm font-medium"
                            :class="
                                sale.due_amount > 0
                                    ? 'text-red-600'
                                    : 'text-gray-900'
                            "
                            >{{ formatCurrency(sale.due_amount) }}</span
                        >
                    </div>
                    <div
                        class="flex justify-between py-3 font-bold text-lg bg-gray-50 rounded-b-lg"
                    >
                        <span class="px-4 text-gray-800">Status:</span>
                        <span
                            class="px-4 capitalize"
                            :class="{
                                'text-green-600': sale.status === 'paid',
                                'text-yellow-600': sale.status === 'partial',
                                'text-red-600': sale.status === 'unpaid',
                            }"
                            >{{ sale.status }}</span
                        >
                    </div>
                </div>
            </div>

            <!-- Amount in Words -->
            <div class="mb-8">
                <div
                    class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center"
                >
                    <p class="text-sm font-semibold text-gray-700 mb-1">
                        Amount in Words:
                    </p>
                    <p
                        class="text-base font-bold text-gray-900 uppercase italic"
                    >
                        {{ totalAmountInWords }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-xs mt-12">
                <p>Thank you for your business!</p>
                <p>
                    {{ sale.farm?.address || "" }}
                    <span v-if="sale.farm?.address && sale.farm?.phone">|</span>
                    {{ sale.farm?.phone || "" }}
                    <span
                        v-if="
                            (sale.farm?.address || sale.farm?.phone) &&
                            sale.farm?.email
                        "
                        >|</span
                    >
                    {{ sale.farm?.email || "" }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import convertNumberToWords from "@/Utils/numberToWords";

const props = defineProps({
    sale: Object,
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

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    const numValue = parseFloat(value);
    if (isNaN(numValue)) return "0.00";
    return numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getItemSubtotal = (item) => {
    // Use total_price if available, otherwise calculate from quantity * unit_price
    if (item.total_price !== null && item.total_price !== undefined) {
        return item.total_price;
    }
    const quantity = parseFloat(item.quantity) || 0;
    const unitPrice = parseFloat(item.unit_price) || 0;
    return quantity * unitPrice;
};

const totalAmountInWords = computed(() => {
    if (
        props.sale &&
        props.sale.total_amount !== undefined &&
        props.sale.total_amount !== null
    ) {
        const amount = parseFloat(props.sale.total_amount);
        if (!isNaN(amount)) {
            return convertNumberToWords(amount);
        }
    }
    return "zero only";
});

// Automatically trigger print when the component is mounted
onMounted(() => {
    window.print();
});
</script>

<style>
/* Basic print styles to hide unnecessary elements and ensure good layout */
@media print {
    body {
        margin: 0;
        padding: 0;
        background-color: #fff;
    }
    .print-invoice {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
    /* Hide elements not needed in print */
    .no-print {
        display: none !important;
    }
}
</style>
