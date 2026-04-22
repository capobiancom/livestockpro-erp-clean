<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Add New Transaction
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Record a new cash inflow or outflow
                    </p>
                </div>
                <Link
                    :href="route('cash-transactions.index')"
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
                    Back to Transactions
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Transaction Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Transaction Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the transaction's primary information
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Cash Account <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.cash_account_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.cash_account_id,
                                }"
                                required
                            >
                                <option value="">Select account...</option>
                                <option v-for="account in cashAccounts" :key="account.id" :value="account.id">
                                    {{ account.name }} ({{ account.type }}) - Balance: {{ formatCurrency(account.current_balance) }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.cash_account_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.cash_account_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Transaction Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.transaction_date"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.transaction_date }"
                                required
                            />
                            <p
                                v-if="form.errors.transaction_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.transaction_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Direction <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.direction"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.direction,
                                }"
                                required
                            >
                                <option value="">Select direction...</option>
                                <option v-for="direction in directions" :key="direction.value" :value="direction.value">
                                    {{ direction.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.direction"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.direction }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.amount }"
                                required
                            />
                            <p
                                v-if="form.errors.amount"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.amount }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Payment Method <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input
                                v-model="form.payment_method"
                                type="text"
                                placeholder="e.g., Cash, Check, Bank Transfer"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.payment_method }"
                            />
                            <p
                                v-if="form.errors.payment_method"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.payment_method }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Reference Type <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input
                                v-model="form.reference_type"
                                type="text"
                                placeholder="e.g., Invoice, Receipt, Order"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.reference_type }"
                            />
                            <p
                                v-if="form.errors.reference_type"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.reference_type }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Description <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Enter description or notes about this transaction..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('cash-transactions.index')"
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
                        {{ form.processing ? "Recording..." : "Record Transaction" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    directions: Array,
    cashAccounts: Array,
    preselectedAccountId: [String, Number],
});

const today = new Date().toISOString().split("T")[0];

const form = useForm({
    cash_account_id: props.preselectedAccountId || "",
    transaction_date: today,
    amount: "",
    direction: "",
    description: "",
    payment_method: "",
    reference_type: "",
    reference_id: "",
});

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return "0.00";
    return parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const submit = () => {
    form.post(route("cash-transactions.store"));
};
</script>
