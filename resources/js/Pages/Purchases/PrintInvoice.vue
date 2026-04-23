<template>
    <div class="print-invoice p-8 bg-white font-sans text-gray-800">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900"> {{ $t('invoice') }} </h1>
                    <p class="text-lg text-gray-600">
                        #{{ purchase.invoice_number || purchase.id }}
                    </p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        {{ purchase.farm?.name || "Farm Name" }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        Date: {{ formatDate(purchase.purchased_at) }}
                    </p>
                </div>
            </div>

            <hr class="border-gray-300 mb-8" />

            <!-- Billing Information -->
            <div class="grid grid-cols-2 gap-8 mb-12">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Supplier Details:
                    </h3>
                    <p class="font-medium">
                        {{ purchase.supplier?.name || "N/A" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.supplier?.address || "" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.supplier?.phone || "" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.supplier?.email || "" }}
                    </p>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Invoice To:
                    </h3>
                    <p class="font-medium">
                        {{ purchase.farm?.name || "N/A" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.farm?.address || "" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.farm?.phone || "" }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ purchase.farm?.email || "" }}
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
                            v-for="item in purchase.purchase_items"
                            :key="item.id"
                            class="border-b border-gray-200"
                        >
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ item.item?.name || "N/A" }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ item.quantity }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ money(item.unit_price) }}
                            </td>
                            <td
                                class="text-right py-3 px-4 text-sm text-gray-700"
                            >
                                {{ money(item.sub_total) }}
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
                            > {{ $t('subtotal') }} </span
                        >
                        <span class="text-sm font-medium text-gray-900">{{
                            money(subTotalSum)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            > {{ $t('discount') }} </span
                        >
                        <span class="text-sm font-medium text-gray-900">
                            <span v-if="purchase.discount_type === 'Percent'">
                                {{ purchase.discount }}%
                                <span v-if="calculatedDiscountAmount > 0">
                                    ({{ money(calculatedDiscountAmount) }})
                                </span>
                            </span>
                            <span v-else>
                                {{ money(purchase.discount) }}
                            </span>
                        </span>
                    </div>
                    <div
                        class="flex justify-between py-2 border-b border-gray-200"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            > {{ $t('tax') }} </span
                        >
                        <span class="text-sm font-medium text-gray-900">
                            <span v-if="purchase.tax_percentage > 0">
                                {{ purchase.tax_percentage }}%
                                <span v-if="purchase.tax > 0">
                                    ({{ money(purchase.tax) }})
                                </span>
                            </span>
                            <span v-else>
                                {{ money(purchase.tax) }}
                            </span>
                        </span>
                    </div>
                    <div
                        class="flex justify-between py-3 font-bold text-lg bg-gray-50 rounded-b-lg"
                    >
                        <span class="px-4 text-gray-800"> {{ $t('total') }} </span>
                        <span class="px-4 text-gray-900">{{
                            money(purchase.total_amount)
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="purchase.notes" class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"> {{ $t('notes') }} </h3>
                <p class="text-sm text-gray-600 whitespace-pre-wrap">
                    {{ purchase.notes }}
                </p>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-xs mt-12">
                <p> {{ $t('thank_you_for_your_business') }} </p>
                <p>
                    {{ purchase.farm?.address || "" }} |
                    {{ purchase.farm?.phone || "" }} |
                    {{ purchase.farm?.email || "" }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    purchase: Object,
});

const { money } = useMoneyFormatter();

// Removed formatNumber as money will be used
// const formatNumber = (value) => {
//     if (value === null || value === undefined) return "0.00";
//     return parseFloat(value).toLocaleString("en-US", {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2,
//     });
// };

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const subTotalSum = computed(() => {
    return props.purchase.purchase_items.reduce(
        (sum, item) => sum + item.sub_total,
        0,
    );
});

const calculatedDiscountAmount = computed(() => {
    if (props.purchase.discount_type === "Percent") {
        return subTotalSum.value * (props.purchase.discount / 100);
    }
    return props.purchase.discount;
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
