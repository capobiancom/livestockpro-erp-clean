<script setup>
import { watch, ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    form: Object,
    customers: Array,
    payables: {
        type: Array,
        default: () => [],
    },
    paymentMethods: Array,
    customerPayment: Object, // Added for edit mode to display existing reference_number
});

const page = usePage();

const filteredPayables = ref([]);

// Watch for changes in customer_id to filter payables
watch(
    () => props.form.customer_id,
    (newCustomerId) => {
        if (newCustomerId) {
            filteredPayables.value = props.payables.filter(
                (payable) => payable.customer_id === newCustomerId,
            );
        } else {
            filteredPayables.value = [];
        }
        // Reset payable_id and payable_type if the selected customer no longer has the previously selected payable
        if (
            props.form.payable_id &&
            !filteredPayables.value.some(
                (payable) => payable.id === props.form.payable_id,
            )
        ) {
            props.form.payable_id = null;
            props.form.payable_type = null;
        }
    },
    { immediate: true }, // Run immediately on component mount
);

const selectedPayable = computed(() => {
    return props.payables.find(
        (payable) => payable.id === props.form.payable_id,
    );
});

// Watch for changes in payable_id to update the amount field and payable_type
watch(
    () => props.form.payable_id,
    (newPayableId) => {
        if (newPayableId) {
            const payable = props.payables.find((p) => p.id === newPayableId);
            if (payable) {
                props.form.amount = payable.total_amount - payable.paid_amount; // Set amount to remaining due
                props.form.payable_type = `App\\Models\\${payable.type}`;
            }
        } else {
            props.form.amount = 0; // Reset amount if no payable is selected
            props.form.payable_type = null;
        }
    },
    { immediate: true }, // Run immediately on component mount
);

// Set initial reference_number if in edit mode
onMounted(() => {
    if (props.customerPayment && props.customerPayment.reference_number) {
        props.form.reference_number = props.customerPayment.reference_number;
    }
});
</script>

<template>
    <!-- Payment Information Section -->
    <div class="mb-8">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >1</span
            >
            Payment Information
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Basic payment details and customer information
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Customer <span class="text-red-500">*</span>
                </label>
                <select
                    id="customer_id"
                    v-model="form.customer_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.customer_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                    required
                >
                    <option value=""> {{ $t('select_customer') }} </option>
                    <option
                        v-for="customer in customers"
                        :key="customer.id"
                        :value="customer.id"
                    >
                        {{ customer.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.customer_id"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.customer_id }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Payable Invoice <span class="text-red-500">*</span>
                </label>
                <select
                    id="payable_id"
                    v-model="form.payable_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.payable_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                    required
                >
                    <option value=""> {{ $t('select_payable') }} </option>
                    <option
                        v-for="payable in filteredPayables"
                        :key="payable.id"
                        :value="payable.id"
                    >
                        {{ payable.display_name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.payable_id"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.payable_id }}
                </p>
                <div v-if="selectedPayable" class="mt-2 text-sm text-gray-600">
                    <p>
                        Invoice Type:
                        <span class="font-semibold">{{
                            selectedPayable.type
                        }}</span>
                    </p>
                    <p>
                        Invoice Number:
                        <span class="font-semibold">{{
                            selectedPayable.invoice_number
                        }}</span>
                    </p>
                    <p>
                        Total Amount:
                        <span class="font-semibold">{{
                            selectedPayable.total_amount
                        }}</span>
                    </p>
                    <p>
                        Paid Amount:
                        <span class="font-semibold">{{
                            selectedPayable.paid_amount
                        }}</span>
                    </p>
                    <p>
                        Remaining Amount:
                        <span class="font-semibold">{{
                            selectedPayable.total_amount -
                            selectedPayable.paid_amount
                        }}</span>
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Transaction Date <span class="text-red-500">*</span>
                </label>
                <input
                    id="transaction_date"
                    v-model="form.transaction_date"
                    type="date"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.transaction_date }"
                />
                <p
                    v-if="form.errors.transaction_date"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.transaction_date }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Amount <span class="text-red-500">*</span>
                </label>
                <input
                    id="amount"
                    v-model.number="form.amount"
                    type="number"
                    step="0.01"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.amount }"
                />
                <p v-if="form.errors.amount" class="text-red-500 text-sm mt-1">
                    {{ form.errors.amount }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select
                    id="payment_method"
                    v-model="form.payment_method"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.payment_method }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                    required
                >
                    <option value=""> {{ $t('select_method') }} </option>
                    <option
                        v-for="method in paymentMethods"
                        :key="method"
                        :value="method"
                    >
                        {{ method }}
                    </option>
                </select>
                <p
                    v-if="form.errors.payment_method"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.payment_method }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Reference Number
                    <span class="text-gray-400 text-xs"> {{ $t('auto_generated') }} </span>
                </label>
                <input
                    id="reference_number"
                    v-model="form.reference_number"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-gray-100 cursor-not-allowed"
                    :class="{ 'border-red-500': form.errors.reference_number }"
                    placeholder="Auto-generated"
                    readonly
                    disabled
                />
                <p
                    v-if="form.errors.reference_number"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.reference_number }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Notes <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
                </label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.notes }"
                    placeholder="Additional notes about this payment..."
                ></textarea>
                <p v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                    {{ form.errors.notes }}
                </p>
            </div>
        </div>
    </div>
</template>
