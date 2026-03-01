<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import CustomerPaymentForm from "./Partials/CustomerPaymentForm.vue";

const props = defineProps({
    customerPayment: Object,
    customers: Array,
    payables: Array,
    paymentMethods: Array,
});

const form = useForm({
    _method: "PUT",
    customer_id: props.customerPayment.customer_id,
    payable_id: props.customerPayment.payable_id,
    payable_type: props.customerPayment.payable_type,
    transaction_date: props.customerPayment.transaction_date,
    amount: props.customerPayment.amount,
    payment_method: props.customerPayment.payment_method,
    reference_number: props.customerPayment.reference_number,
    notes: props.customerPayment.notes,
});

const updateCustomerPayment = () => {
    form.post(route("customer-payments.update", props.customerPayment.id));
};
</script>

<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Customer Payment
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update customer payment information
                    </p>
                </div>
                <Link
                    :href="route('customer-payments.index')"
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
                    Back to Payments
                </Link>
            </div>
        </template>

        <form @submit.prevent="updateCustomerPayment" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <CustomerPaymentForm
                    :form="form"
                    :customers="customers"
                    :payables="payables"
                    :paymentMethods="paymentMethods"
                />

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 mt-8"
                >
                    <Link
                        :href="route('customer-payments.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
