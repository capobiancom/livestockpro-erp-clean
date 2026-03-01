<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Record New Purchase
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Supplier Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Supplier Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Select the supplier for this purchase
                    </p>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Supplier
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.supplier_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                            >
                                <option value="">Select Supplier</option>
                                <option
                                    v-for="supplier in suppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.supplier_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Invoice Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Purchase date and auto-generated invoice number
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Invoice Number
                            </label>
                            <input
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                value="Auto-generated on save"
                                readonly
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Purchase Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.purchased_at"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="form.errors.purchased_at"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.purchased_at }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Purchase Items Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Purchase Items
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add inventory or medicine items to this purchase
                    </p>

                    <div
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200"
                    >
                        <div
                            class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end"
                        >
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                    >Item Type</label
                                >
                                <select
                                    v-model="item.item_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                >
                                    <option value="inventory_item">
                                        Inventory Item
                                    </option>
                                    <option value="medicine_item">
                                        Medicine Item
                                    </option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                    >Select Item</label
                                >
                                <select
                                    v-model="item.item_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                >
                                    <option value="">Select Item</option>
                                    <option
                                        v-if="
                                            item.item_type === 'inventory_item'
                                        "
                                        v-for="invItem in inventoryItems"
                                        :key="invItem.id"
                                        :value="invItem.id"
                                    >
                                        {{ invItem.display_name || invItem.name }}
                                    </option>
                                    <option
                                        v-if="
                                            item.item_type === 'medicine_item'
                                        "
                                        v-for="medItem in medicineItems"
                                        :key="medItem.id"
                                        :value="medItem.id"
                                    >
                                        {{ medItem.display_name || medItem.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors[`items.${index}.item_id`]"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors[`items.${index}.item_id`] }}
                                </p>
                            </div>
                            <template v-if="item.item_type === 'medicine_item'">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                        >Batch No.</label
                                    >
                                    <input
                                        v-model="item.batch_no"
                                        type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                        placeholder="Enter batch number"
                                    />
                                    <p
                                        v-if="
                                            form.errors[
                                                `items.${index}.batch_no`
                                            ]
                                        "
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{
                                            form.errors[
                                                `items.${index}.batch_no`
                                            ]
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                        >Expiry Date</label
                                    >
                                    <input
                                        v-model="item.expiry_date"
                                        type="date"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    />
                                    <p
                                        v-if="
                                            form.errors[
                                                `items.${index}.expiry_date`
                                            ]
                                        "
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{
                                            form.errors[
                                                `items.${index}.expiry_date`
                                            ]
                                        }}
                                    </p>
                                </div>
                            </template>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                    >Quantity</label
                                >
                                <input
                                    v-model="item.quantity"
                                    @input="calculateSubTotal(item)"
                                    type="number"
                                    step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    placeholder="0.00"
                                />
                                <p
                                    v-if="
                                        form.errors[`items.${index}.quantity`]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors[`items.${index}.quantity`] }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                    >Unit Price</label
                                >
                                <input
                                    v-model="item.unit_price"
                                    @input="calculateSubTotal(item)"
                                    type="number"
                                    step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    placeholder="0.00"
                                />
                                <p
                                    v-if="
                                        form.errors[`items.${index}.unit_price`]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[`items.${index}.unit_price`]
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                    >Sub Total</label
                                >
                                <input
                                    v-model="item.sub_total"
                                    type="number"
                                    step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                            </div>
                            <div class="md:col-span-5 flex justify-end">
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition"
                                >
                                    Remove Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="addItem"
                        class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition"
                    >
                        Add Item
                    </button>
                    <p
                        v-if="form.errors.items"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.items }}
                    </p>
                </div>

                <!-- Totals Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >4</span
                        >
                        Totals
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Review and adjust total amounts
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Discount Type</label
                            >
                            <select
                                v-model="form.discount_type"
                                @change="calculateTotals"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                            >
                                <option value="Fixed">Fixed</option>
                                <option value="Percent">Percent (%)</option>
                            </select>
                            <p
                                v-if="form.errors.discount_type"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.discount_type }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Discount</label
                            >
                            <input
                                v-model="form.discount"
                                @input="calculateTotals"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                placeholder="0.00"
                            />
                            <p
                                v-if="form.errors.discount"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.discount }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Tax Percentage (%)</label
                            >
                            <input
                                v-model="form.tax_percentage"
                                @input="calculateTotals"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                placeholder="0.00"
                            />
                            <p
                                v-if="form.errors.tax_percentage"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.tax_percentage }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Calculated Tax</label
                            >
                            <input
                                v-model="form.tax"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                readonly
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Total Amount</label
                            >
                            <input
                                v-model="form.total_amount"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                readonly
                            />
                            <p
                                v-if="form.errors.total_amount"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.total_amount }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >5</span
                        >
                        Additional Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add any additional notes or details
                    </p>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Notes
                            <span class="text-gray-400 text-xs"
                                >(Optional)</span
                            >
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                            placeholder="Additional notes about this purchase..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('purchases.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        {{
                            form.processing ? "Recording..." : "Record Purchase"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

import { ref, computed } from "vue"; // Import ref and computed
import { watch } from "vue";

const props = defineProps({
    suppliers: Array,
    inventoryItems: Array,
    medicineItems: Array,
});

const form = useForm({
    supplier_id: "",
    purchased_at: "",
    notes: "",
    total_amount: 0,
    discount: 0,
    discount_type: "Fixed", // Default to Fixed
    tax: 0,
    tax_percentage: 0, // Default to 0
    items: [],
});

const addItem = () => {
    form.items.push({
        item_type: "inventory_item", // Default to inventory item
        item_id: "",
        batch_no: "",
        expiry_date: "",
        quantity: 1,
        unit_price: 0,
        sub_total: 0,
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calculateSubTotal = (item) => {
    item.sub_total = item.quantity * item.unit_price;
    calculateTotals();
};

const calculateTotals = () => {
    let subTotalSum = form.items.reduce((sum, item) => sum + item.sub_total, 0);
    let calculatedDiscount = 0;
    if (form.discount_type === "Percent") {
        calculatedDiscount = subTotalSum * (form.discount / 100);
    } else {
        calculatedDiscount = form.discount;
    }

    let calculatedTax = subTotalSum * (form.tax_percentage / 100);

    form.tax = calculatedTax; // Update the tax field with the calculated tax
    form.total_amount = subTotalSum - calculatedDiscount + calculatedTax;
};

// Watch for changes in items to update totals
watch(
    () => form.items,
    () => {
        calculateTotals();
    },
    { deep: true },
);

// Watch for changes in discount and tax_percentage to update totals
watch(
    () => [form.discount, form.tax_percentage, form.discount_type],
    () => {
        calculateTotals();
    },
    { deep: true },
);

function submit() {
    form.post("/purchases");
}

// Initial add item
addItem();
</script>
