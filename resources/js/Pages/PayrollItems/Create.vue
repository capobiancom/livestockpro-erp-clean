<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Create New Payroll Item
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Add a new payroll item for an employee
                    </p>
                </div>
                <Link
                    :href="
                        payrollRun
                            ? `/payroll-runs/${payrollRun.id}`
                            : '/payroll-items'
                    "
                    class="bg-gradient-to-r ml-5 from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Back to Payroll Items
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Payroll Item Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Payroll Item Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the details for the new payroll item
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Payroll Run <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.payroll_run_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.payroll_run_id,
                                }"
                                :disabled="!!payrollRun"
                            >
                                <option value="">Select Payroll Run...</option>
                                <option
                                    v-for="pr in payrollRuns"
                                    :key="pr.id"
                                    :value="pr.id"
                                >
                                    {{ pr.month }} {{ pr.year }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.payroll_run_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.payroll_run_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Employee <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.employee_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.employee_id,
                                }"
                            >
                                <option value="">Select Employee...</option>
                                <option
                                    v-for="employee in employees"
                                    :key="employee.id"
                                    :value="employee.id"
                                >
                                    {{ employee.first_name }}
                                    {{ employee.last_name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.employee_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.employee_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Basic Salary
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.basic_salary"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 1000.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.basic_salary,
                                }"
                            />
                            <p
                                v-if="form.errors.basic_salary"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.basic_salary }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                House Allowance
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.house_allowance"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 200.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.house_allowance,
                                }"
                            />
                            <p
                                v-if="form.errors.house_allowance"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.house_allowance }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Medical Allowance
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.medical_allowance"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 50.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.medical_allowance,
                                }"
                            />
                            <p
                                v-if="form.errors.medical_allowance"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.medical_allowance }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Transport Allowance
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.transport_allowance"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 30.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.transport_allowance,
                                }"
                            />
                            <p
                                v-if="form.errors.transport_allowance"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.transport_allowance }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Overtime Hours
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.overtime_hours"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 10.5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.overtime_hours,
                                }"
                            />
                            <p
                                v-if="form.errors.overtime_hours"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.overtime_hours }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Overtime Rate
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.overtime_rate"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 15.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.overtime_rate,
                                }"
                            />
                            <p
                                v-if="form.errors.overtime_rate"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.overtime_rate }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Overtime Amount
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.overtime_amount"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 157.50"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.overtime_amount,
                                }"
                            />
                            <p
                                v-if="form.errors.overtime_amount"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.overtime_amount }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Gross Salary
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.gross_salary"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 1437.50"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.gross_salary,
                                }"
                            />
                            <p
                                v-if="form.errors.gross_salary"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.gross_salary }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Deductions <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.deductions"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 50.00"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.deductions,
                                }"
                            />
                            <p
                                v-if="form.errors.deductions"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.deductions }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Net Salary
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.net_salary"
                                type="number"
                                step="0.01"
                                placeholder="e.g., 1387.50"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.net_salary,
                                }"
                            />
                            <p
                                v-if="form.errors.net_salary"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.net_salary }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="
                            payrollRun
                                ? `/payroll-runs/${payrollRun.id}`
                                : '/payroll-items'
                        "
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                                ? "Saving..."
                                : "Create Payroll Item"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { computed } from "vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/Layout/AppLayout.vue";

const props = defineProps({
    payrollRun: Object, // Optional, if creating from a specific payroll run
    employees: Array,
    payrollRuns: Array, // For selecting a payroll run if not provided
});

const form = useForm({
    payroll_run_id: props.payrollRun?.id || "",
    employee_id: "",
    basic_salary: 0,
    house_allowance: 0,
    medical_allowance: 0,
    transport_allowance: 0,
    overtime_hours: 0,
    overtime_rate: 0,
    overtime_amount: 0,
    gross_salary: 0,
    deductions: 0,
    net_salary: 0,
});

const submit = () => {
    form.post("/payroll-items");
};
</script>
