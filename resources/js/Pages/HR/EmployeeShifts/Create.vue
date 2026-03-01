<template>
    <Layout title="Assign Employee Shift">
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Assign Employee Shift
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Assign a shift to an employee
                    </p>
                </div>
                <Link
                    :href="route('employee-shifts.index')"
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
                    Back to Employee Shifts
                </Link>
            </div>
        </template>

        <form @submit.prevent="createEmployeeShift" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Employee Shift Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Assignment Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Select an employee and a shift, and define the effective
                        dates.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Employee <span class="text-blue-500">*</span>
                            </label>
                            <select
                                v-model="form.employee_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
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
                                Shift <span class="text-blue-500">*</span>
                            </label>
                            <select
                                v-model="form.shift_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.shift_id,
                                }"
                            >
                                <option value="">Select shift...</option>
                                <option
                                    v-for="shift in shifts"
                                    :key="shift.id"
                                    :value="shift.id"
                                >
                                    {{ shift.name }} ({{ shift.start_time }} -
                                    {{ shift.end_time }})
                                </option>
                            </select>
                            <p
                                v-if="form.errors.shift_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.shift_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Effective From
                                <span class="text-blue-500">*</span>
                            </label>
                            <input
                                id="effective_from"
                                v-model="form.effective_from"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.effective_from,
                                }"
                            />
                            <p
                                v-if="form.errors.effective_from"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.effective_from }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Effective To
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                id="effective_to"
                                v-model="form.effective_to"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.effective_to,
                                }"
                            />
                            <p
                                v-if="form.errors.effective_to"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.effective_to }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('employee-shifts.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                        {{ form.processing ? "Saving..." : "Assign Shift" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    employees: Array,
    shifts: Array,
});

const form = useForm({
    employee_id: "",
    shift_id: "",
    effective_from: "",
    effective_to: "",
});

const createEmployeeShift = () => {
    form.post(route("employee-shifts.store"), {
        onSuccess: () => form.reset(),
        onError: () => {
            // Handle error
        },
    });
};
</script>
