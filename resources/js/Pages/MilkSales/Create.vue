<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Record New Milk Sale
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Sale Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Sale Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Basic sale details and reference
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Invoice Number
                                <span class="text-gray-400 text-xs"
                                    >(Auto-generated if empty)</span
                                >
                            </label>
                            <input
                                v-model="form.invoice_number"
                                type="text"
                                readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                                :class="{
                                    'border-red-500':
                                        form.errors.invoice_number,
                                }"
                            />
                            <p
                                v-if="form.errors.invoice_number"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.invoice_number }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Sale Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.sale_date"
                                type="date"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="form.errors.sale_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.sale_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                            >
                                <option value="">Select Status</option>
                                <option
                                    v-for="statusOption in saleStatuses"
                                    :key="statusOption.value"
                                    :value="statusOption.value"
                                >
                                    {{ statusOption.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Customer and quantity information
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.customer_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                            >
                                <option value="">Select Customer</option>
                                <option
                                    v-for="customer in customers"
                                    :key="customer.id"
                                    :value="customer.id"
                                >
                                    {{ customer.name
                                    }}{{
                                        customer.contact_person
                                            ? " - " + customer.contact_person
                                            : ""
                                    }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.customer_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.customer_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Pricing
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Quantity and pricing information
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.quantity"
                                @input="calculateTotal"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                                placeholder="0.00"
                            />
                            <p
                                v-if="form.errors.quantity"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.quantity }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Unit Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >{{
                                        $page.props.appSettings.currency_symbol
                                    }}</span
                                >
                                <input
                                    v-model="form.unit_price"
                                    @input="calculateTotal"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                                    placeholder="0.00"
                                />
                            </div>
                            <p
                                v-if="form.errors.unit_price"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.unit_price }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Total Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >{{
                                        $page.props.appSettings.currency_symbol
                                    }}</span
                                >
                                <input
                                    v-model="form.total_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    readonly
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                                    placeholder="0.00"
                                />
                            </div>
                            <p
                                v-if="form.errors.total_price"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.total_price }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment and Notes Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >4</span
                        >
                        Payment & Notes
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Payment details and additional notes for the sale
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Paid Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="paid_amount"
                                v-model.number="form.paid_amount"
                                @input="calculateTotal"
                                type="number"
                                step="0.01"
                                min="0"
                                :max="form.total_price"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.paid_amount,
                                }"
                            />
                            <p
                                v-if="form.errors.paid_amount"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.paid_amount }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Due Amount
                            </label>
                            <input
                                id="due_amount"
                                :value="
                                    (
                                        (form.total_price || 0) -
                                        (form.paid_amount || 0)
                                    ).toFixed(2)
                                "
                                type="text"
                                readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Notes
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.notes }"
                                placeholder="Additional notes about this sale..."
                            ></textarea>
                            <p
                                v-if="form.errors.notes"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('milk-sales.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                            form.processing
                                ? "Recording..."
                                : "Record Milk Sale"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    customers: Array,
    saleStatuses: Array,
});

const form = useForm({
    invoice_number: "",
    customer_id: "",
    sale_date: "",
    quantity: "",
    unit: "liters",
    unit_price: "",
    total_price: 0,
    paid_amount: 0,
    status: "unpaid",
    notes: "",
});

function calculateTotal() {
    const quantity = parseFloat(form.quantity) || 0;
    const unitPrice = parseFloat(form.unit_price) || 0;
    form.total_price = (quantity * unitPrice).toFixed(2);

    // Update paid_amount and status based on total_price
    if (form.total_price == 0) {
        form.paid_amount = 0;
        form.status = "unpaid";
    } else if (form.paid_amount >= form.total_price) {
        form.paid_amount = form.total_price;
        form.status = "paid";
    } else if (form.paid_amount > 0 && form.paid_amount < form.total_price) {
        form.status = "partial";
    } else {
        form.paid_amount = 0;
        form.status = "unpaid";
    }
}

function submit() {
    form.post("/milk-sales");
}
</script>
