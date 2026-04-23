<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    salaryStructure: Object,
    employees: Array,
    farms: Array,
});

const formatDateForInput = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toISOString().slice(0, 10);
};

const form = useForm({
    employee_id: props.salaryStructure.employee_id,
    farm_id: props.salaryStructure.farm_id,
    basic_salary: props.salaryStructure.basic_salary,
    house_allowance: props.salaryStructure.house_allowance,
    medical_allowance: props.salaryStructure.medical_allowance,
    transport_allowance: props.salaryStructure.transport_allowance,
    overtime_rate: props.salaryStructure.overtime_rate,
    effective_from: formatDateForInput(props.salaryStructure.effective_from),
});

const submit = () => {
    form.put(route("salary-structures.update", props.salaryStructure.id));
};
</script>

<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Salary Structure
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update the details for this salary structure.
                    </p>
                </div>
                <Link
                    :href="route('salary-structures.index')"
                    class="bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Salary Structure Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Salary Structure Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the details for the salary structure.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="employee_id"
                                value="Employee"
                                class="text-gray-700 font-medium"
                            />
                            <select
                                id="employee_id"
                                v-model="form.employee_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.employee_id,
                                }"
                            >
                                <option value="">Select employee...</option>
                                <option
                                    v-for="employee in employees"
                                    :key="employee.id"
                                    :value="employee.id"
                                >
                                    {{ employee.first_name }}
                                    {{ employee.last_name }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.employee_id"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="basic_salary"
                                value="Basic Salary"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="basic_salary"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.basic_salary"
                                required
                                :class="{
                                    'border-red-500': form.errors.basic_salary,
                                }"
                            />
                            <InputError
                                :message="form.errors.basic_salary"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="house_allowance"
                                value="House Allowance"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="house_allowance"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.house_allowance"
                                :class="{
                                    'border-red-500':
                                        form.errors.house_allowance,
                                }"
                            />
                            <InputError
                                :message="form.errors.house_allowance"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="medical_allowance"
                                value="Medical Allowance"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="medical_allowance"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.medical_allowance"
                                :class="{
                                    'border-red-500':
                                        form.errors.medical_allowance,
                                }"
                            />
                            <InputError
                                :message="form.errors.medical_allowance"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="transport_allowance"
                                value="Transport Allowance"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="transport_allowance"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.transport_allowance"
                                :class="{
                                    'border-red-500':
                                        form.errors.transport_allowance,
                                }"
                            />
                            <InputError
                                :message="form.errors.transport_allowance"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="overtime_rate"
                                value="Overtime Rate"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="overtime_rate"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.overtime_rate"
                                :class="{
                                    'border-red-500': form.errors.overtime_rate,
                                }"
                            />
                            <InputError
                                :message="form.errors.overtime_rate"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex flex-col gap-3">
                            <InputLabel
                                for="effective_from"
                                value="Effective From"
                                class="text-gray-700 font-medium"
                            />
                            <TextInput
                                id="effective_from"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                v-model="form.effective_from"
                                required
                                :class="{
                                    'border-red-500':
                                        form.errors.effective_from,
                                }"
                            />
                            <InputError
                                :message="form.errors.effective_from"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('salary-structures.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                                ? "Updating..."
                                : "Update Salary Structure"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>
