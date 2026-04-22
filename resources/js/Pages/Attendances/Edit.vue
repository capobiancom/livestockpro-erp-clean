<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Attendance
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update an existing attendance record.
                    </p>
                </div>
                <Link
                    :href="route('attendances.index')"
                    class="bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H16a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Back to Attendances
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Attendance Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Attendance Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the attendance information for the employee.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                Farm <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.farm_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.farm_id,
                                }"
                            >
                                <option value="">Select farm...</option>
                                <option
                                    v-for="farm in farms"
                                    :key="farm.id"
                                    :value="farm.id"
                                >
                                    {{ farm.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.farm_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.farm_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Date <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.date,
                                }"
                            />
                            <p
                                v-if="form.errors.date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Check In
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                id="check_in"
                                v-model="form.check_in"
                                type="time"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.check_in,
                                }"
                            />
                            <p
                                v-if="form.errors.check_in"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.check_in }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Check Out
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                id="check_out"
                                v-model="form.check_out"
                                type="time"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.check_out,
                                }"
                            />
                            <p
                                v-if="form.errors.check_out"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.check_out }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Source <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.source"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.source,
                                }"
                            >
                                <option value="">Select source...</option>
                                <option
                                    v-for="source in attendanceSources"
                                    :key="source"
                                    :value="source"
                                >
                                    {{ source }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.source"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.source }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('attendances.index')"
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
                            form.processing ? "Saving..." : "Update Attendance"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    attendance: Object,
    employees: Array,
    farms: Array,
    users: Array,
    attendanceStatuses: Array,
    attendanceSources: Array,
});

import { format, parse } from "date-fns";

const convertUtcToLocalTime = (dateTimeString) => {
    if (!dateTimeString) return "";

    try {
        const parsedDate = parse(
            dateTimeString,
            "yyyy-MM-dd HH:mm:ss",
            new Date(),
        );
        if (isNaN(parsedDate.getTime())) {
            console.error("Invalid Date encountered for:", dateTimeString);
            return "";
        }
        return format(parsedDate, "HH:mm");
    } catch (error) {
        console.error(
            "Error formatting time with date-fns for input:",
            dateTimeString,
            error,
        );
        return "";
    }
};

const form = useForm({
    employee_id: props.attendance.employee_id,
    farm_id: props.attendance.farm_id,
    date: props.attendance.date
        ? new Date(props.attendance.date).toISOString().slice(0, 10)
        : "",
    check_in: props.attendance.check_in
        ? convertUtcToLocalTime(props.attendance.check_in)
        : "",
    check_out: props.attendance.check_out
        ? convertUtcToLocalTime(props.attendance.check_out)
        : "",
    status: props.attendance.status,
    source: props.attendance.source,
});

const submit = () => {
    form.put(route("attendances.update", props.attendance.id));
};
</script>
