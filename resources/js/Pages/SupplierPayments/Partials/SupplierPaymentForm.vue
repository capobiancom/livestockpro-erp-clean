<script setup>
import { watch, ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    form: Object,
    suppliers: Array,
    payables: {
        type: Array,
        default: () => [],
    },
    paymentMethods: Array,
    supplierPayment: Object,
});

const page = usePage();

const filteredPayables = ref([]);

// Watch for changes in supplier_id to filter payables
watch(
    () => props.form.supplier_id,
    (newSupplierId) => {
        if (newSupplierId) {
            filteredPayables.value = props.payables.filter(
                (payable) => payable.supplier_id === newSupplierId,
            );
        } else {
            filteredPayables.value = [];
        }
        // Reset payable_id and payable_type if the selected supplier no longer has the previously selected payable
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
    { immediate: true },
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
                props.form.amount = payable.total_amount - (payable.paid_amount || 0);
                props.form.payable_type = `App\\Models\\${payable.type}`;
            }
        } else {
            props.form.amount = 0;
            props.form.payable_type = null;
        }
    },
    { immediate: true },
);

// Set initial reference_number if in edit mode
onMounted(() => {
    if (props.supplierPayment && props.supplierPayment.reference_number) {
        props.form.reference_number = props.supplierPayment.reference_number;
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
                class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >1</span
            >
            Payment Information
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Basic payment details and supplier information
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Supplier <span class="text-red-500">*</span>
                </label>
                <select
                    id="supplier_id"
                    v-model="form.supplier_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.supplier_id }"
                    required
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

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Payable Invoice <span class="text-red-500">*</span>
                </label>
                <select
                    id="payable_id"
                    v-model="form.payable_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.payable_id }"
                    required
                >
                    <option value="">Select Payable</option>
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
                            selectedPayable.paid_amount || 0
                        }}</span>
                    </p>
                    <p>
                        Remaining Amount:
                        <span class="font-semibold">{{
                            selectedPayable.total_amount -
                            (selectedPayable.paid_amount || 0)
                        }}</span>
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Date <span class="text-red-500">*</span>
                </label>
                <input
                    id="payment_date"
                    v-model="form.payment_date"
                    type="date"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.payment_date }"
                />
                <p
                    v-if="form.errors.payment_date"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.payment_date }}
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
                    min="0"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.payment_method }"
                    required
                >
                    <option value="">Select Method</option>
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
                    <span class="text-gray-400 text-xs">(Auto-generated)</span>
                </label>
                <input
                    id="reference_number"
                    v-model="form.reference_number"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-gray-100 cursor-not-allowed"
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
                    Notes <span class="text-gray-400 text-xs">(Optional)</span>
                </label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
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
