<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import SupplierPaymentForm from "./Partials/SupplierPaymentForm.vue";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    supplierPayment: Object,
    suppliers: Array,
    payables: Array,
    paymentMethods: Array,
});

const form = useForm({
    _method: "PUT",
    supplier_id: props.supplierPayment.supplier_id,
    payable_id: props.supplierPayment.payable_id,
    payable_type: props.supplierPayment.payable_type,
    payment_date: props.supplierPayment.payment_date,
    amount: props.supplierPayment.amount,
    payment_method: props.supplierPayment.payment_method,
    reference_number: props.supplierPayment.reference_number,
    notes: props.supplierPayment.notes,
});

const updateSupplierPayment = () => {
    form.post(route("supplier-payments.update", props.supplierPayment.id));
};
</script>

<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Supplier Payment
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update supplier payment information
                    </p>
                </div>
                <Link
                    :href="route('supplier-payments.index')"
                    class="text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <form @submit.prevent="updateSupplierPayment" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <SupplierPaymentForm
                    :form="form"
                    :suppliers="suppliers"
                    :paymentMethods="paymentMethods"
                    :payables="payables"
                    :supplierPayment="supplierPayment"
                />

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 mt-8"
                >
                    <Link
                        :href="route('supplier-payments.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        {{ form.processing ? "Updating..." : "Update Payment" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>
