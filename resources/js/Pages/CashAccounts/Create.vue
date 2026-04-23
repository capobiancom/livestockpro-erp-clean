<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Add New Cash Account
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create a new cash, bank or mobile money account
                    </p>
                </div>
                <Link
                    :href="route('cash-accounts.index')"
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
                    Back to Accounts
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the account's primary details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Main Cash Account"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Account Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.type,
                                }"
                                required
                            >
                                <option value="">Select type...</option>
                                <option
                                    v-for="type in accountTypes"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.type"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.type }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Opening Balance
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model.number="form.opening_balance"
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.opening_balance,
                                }"
                            />
                            <p
                                v-if="form.errors.opening_balance"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.opening_balance }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Chart of Account
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.account_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.account_id,
                                }"
                            >
                                <option value="">
                                    Select chart of account...
                                </option>
                                <option
                                    v-for="account in chartOfAccounts"
                                    :key="account.id"
                                    :value="account.id"
                                >
                                    {{ account.code }} - {{ account.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.account_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.account_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bank Details Section -->
                <div
                    class="mb-8"
                    v-if="form.type === 'bank' || form.type === 'mobile'"
                >
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        {{ form.type === "bank" ? "Bank" : "Mobile Provider" }}
                        Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter
                        {{ form.type === "bank" ? "bank" : "mobile provider" }}
                        specific information
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                {{ form.type === "bank" ? "Bank" : "Provider" }}
                                Name
                            </label>
                            <input
                                v-model="form.bank_name"
                                type="text"
                                :placeholder="
                                    form.type === 'bank'
                                        ? 'e.g., Bank of America'
                                        : 'e.g., M-Pesa'
                                "
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.bank_name,
                                }"
                            />
                            <p
                                v-if="form.errors.bank_name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.bank_name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Account Number
                            </label>
                            <input
                                v-model="form.account_number"
                                type="text"
                                placeholder="Enter account number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.account_number,
                                }"
                            />
                            <p
                                v-if="form.errors.account_number"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.account_number }}
                            </p>
                        </div>

                        <div v-if="form.type === 'bank'">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Branch Name
                            </label>
                            <input
                                v-model="form.branch_name"
                                type="text"
                                placeholder="Enter branch name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.branch_name,
                                }"
                            />
                            <p
                                v-if="form.errors.branch_name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.branch_name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >{{
                                form.type === "bank" || form.type === "mobile"
                                    ? "3"
                                    : "2"
                            }}</span
                        >
                        Additional Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Optional details and settings
                    </p>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Enter description or notes about this account..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-2 focus:ring-emerald-500"
                                />
                                <span
                                    class="text-sm font-semibold text-gray-700"
                                >
                                    Account is Active
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 ml-8 mt-1">
                                Only active accounts can be used for
                                transactions
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 mt-8"
                >
                    <Link
                        :href="route('cash-accounts.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        {{ form.processing ? "Creating..." : "Create Account" }}
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
    accountTypes: Array,
    chartOfAccounts: Array,
});

const form = useForm({
    name: "",
    type: "",
    account_id: "",
    account_number: "",
    bank_name: "",
    branch_name: "",
    opening_balance: 0,
    is_active: true,
    description: "",
});

const submit = () => {
    form.post(route("cash-accounts.store"));
};
</script>
