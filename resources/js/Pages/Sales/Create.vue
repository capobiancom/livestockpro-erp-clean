<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";
import SaleForm from "./Partials/SaleForm.vue";

const props = defineProps({
    customers: Array,
    farms: Array,
    inventoryItems: Array,
    animals: Array,
    saleStatuses: Array,
});

const form = useForm({
    invoice_number: "",
    invoice_date: "",
    customer_id: "",
    total_amount: 0,
    paid_amount: 0,
    status: "unpaid",
    notes: "",
    sales_items: [],
});

function submit() {
    form.post(route("sales.store"));
}
</script>

<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Create New Sale</h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <SaleForm
                    :form="form"
                    :customers="customers"
                    :farms="farms"
                    :inventoryItems="inventoryItems"
                    :animals="animals"
                    :saleStatuses="saleStatuses"
                />

                <div
                    v-if="form.errors.error"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4"
                    role="alert"
                >
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ form.errors.error }}</span>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 mt-8"
                >
                    <Link
                        :href="route('sales.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        {{ form.processing ? "Saving..." : "Save Sale" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>
