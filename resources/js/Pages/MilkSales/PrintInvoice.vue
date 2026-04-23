<template>
    <div class="print-invoice p-8 bg-white font-sans text-gray-800">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900"> {{ $t('invoice') }} </h1>
                    <p class="text-lg text-gray-600">
                        #{{ milkSale.invoice_number || milkSale.id }}
                    </p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        {{ milkSale.farm?.name || "Farm Name" }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        Date: {{ formatDate(milkSale.sale_date) }}
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
                        {{ milkSale.customer?.name || "N/A" }}
                    </p>
                    <p
                        v-if="milkSale.customer?.contact_person"
                        class="text-sm text-gray-600"
                    >
                        Contact: {{ milkSale.customer.contact_person }}
                    </p>
                    <p
                        v-if="milkSale.customer?.phone"
                        class="text-sm text-gray-600"
                    >
                        Phone: {{ milkSale.customer.phone }}
                    </p>
                    <p
                        v-if="milkSale.customer?.email"
                        class="text-sm text-gray-600"
                    >
                        Email: {{ milkSale.customer.email }}
                    </p>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Invoice From:
                    </h3>
                    <p class="font-medium">
                        {{ milkSale.farm?.name || "N/A" }}
                    </p>
                    <p
                        v-if="milkSale.farm?.address"
                        class="text-sm text-gray-600"
                    >
                        {{ milkSale.farm.address }}
                    </p>
                    <p
                        v-if="milkSale.farm?.phone"
                        class="text-sm text-gray-600"
                    >
                        {{ milkSale.farm.phone }}
                    </p>
                    <p
                        v-if="milkSale.farm?.email"
                        class="text-sm text-gray-600"
                    >
                        {{ milkSale.farm.email }}
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
                                Description
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
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-3 px-4 text-sm text-gray-700">
                                Milk Sale ({{ milkSale.unit || "Liters" }})
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ formatNumber(milkSale.quantity) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ formatCurrency(milkSale.unit_price) }}
                            </td>
                            <td
                                class="text-right py-3 px-4 text-sm text-gray-700"
                            >
                                {{ formatCurrency(milkSale.total_price) }}
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
                            > {{ $t('total_amount') }} </span
                        >
                        <span class="text-sm font-medium text-gray-900">{{
                            formatCurrency(milkSale.total_price)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            > {{ $t('paid_amount') }} </span
                        >
                        <span class="text-sm font-medium text-green-600">{{
                            formatCurrency(milkSale.paid_amount)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            > {{ $t('due_amount') }} </span
                        >
                        <span
                            class="text-sm font-medium"
                            :class="
                                dueAmount > 0 ? 'text-red-600' : 'text-gray-900'
                            "
                            >{{ formatCurrency(dueAmount) }}</span
                        >
                    </div>
                    <div
                        class="flex justify-between py-3 font-bold text-lg bg-gray-50 rounded-b-lg"
                    >
                        <span class="px-4 text-gray-800"> {{ $t('status') }} </span>
                        <span
                            class="px-4 capitalize"
                            :class="{
                                'text-green-600': milkSale.status === 'paid',
                                'text-yellow-600':
                                    milkSale.status === 'partial',
                                'text-red-600': milkSale.status === 'unpaid',
                            }"
                            >{{ milkSale.status }}</span
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

            <!-- Notes -->
            <div v-if="milkSale.notes" class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"> {{ $t('notes') }} </h3>
                <p class="text-sm text-gray-600 whitespace-pre-wrap">
                    {{ milkSale.notes }}
                </p>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-xs mt-12">
                <p> {{ $t('thank_you_for_your_business') }} </p>
                <p>
                    {{ milkSale.farm?.address || "" }}
                    <span v-if="milkSale.farm?.address && milkSale.farm?.phone"
                        >|</span
                    >
                    {{ milkSale.farm?.phone || "" }}
                    <span
                        v-if="
                            (milkSale.farm?.address || milkSale.farm?.phone) &&
                            milkSale.farm?.email
                        "
                        >|</span
                    >
                    {{ milkSale.farm?.email || "" }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import convertNumberToWords from "@/Utils/numberToWords";

const props = defineProps({
    milkSale: Object,
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

const dueAmount = computed(() => {
    return (
        parseFloat(props.milkSale.total_price || 0) -
        parseFloat(props.milkSale.paid_amount || 0)
    );
});

const totalAmountInWords = computed(() => {
    if (
        props.milkSale &&
        props.milkSale.total_price !== undefined &&
        props.milkSale.total_price !== null
    ) {
        const amount = parseFloat(props.milkSale.total_price);
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
