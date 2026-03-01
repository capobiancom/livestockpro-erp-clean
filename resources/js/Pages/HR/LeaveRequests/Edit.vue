<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Leave Request
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update leave request for
                        {{ leaveRequest.employee.first_name }}
                        {{ leaveRequest.employee.last_name }}
                    </p>
                </div>
                <Link
                    :href="route('leave-requests.show', leaveRequest.id)"
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
                    Back to Details
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Leave Request Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Leave Request Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the details for the leave request.
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
                                Leave Type <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.leave_type_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.leave_type_id,
                                }"
                            >
                                <option value="">Select leave type...</option>
                                <option
                                    v-for="leaveType in leaveTypes"
                                    :key="leaveType.id"
                                    :value="leaveType.id"
                                >
                                    {{ leaveType.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.leave_type_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.leave_type_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Start Date <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.start_date,
                                }"
                            />
                            <p
                                v-if="form.errors.start_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.start_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                End Date <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                :min="form.start_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.end_date,
                                }"
                            />
                            <p
                                v-if="form.errors.end_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.end_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Total Days <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="total_days"
                                v-model="form.total_days"
                                type="number"
                                min="1"
                                readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition bg-gray-100 cursor-not-allowed"
                                :class="{
                                    'border-red-500': form.errors.total_days,
                                }"
                            />
                            <p
                                v-if="form.errors.total_days"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.total_days }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                            >
                                <option value="">Select status...</option>
                                <option
                                    v-for="statusOption in statuses"
                                    :key="statusOption"
                                    :value="statusOption"
                                >
                                    {{ statusOption }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Reason
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <textarea
                                id="reason"
                                v-model="form.reason"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.reason,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.reason"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.reason }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Approved By
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.approved_by"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.approved_by,
                                }"
                            >
                                <option value="">Select approver...</option>
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
                                v-if="form.errors.approved_by"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.approved_by }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('leave-requests.index')"
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
                                ? "Updating..."
                                : "Update Leave Request"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { watch, onMounted } from "vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    leaveRequest: Object,
    employees: Array,
    leaveTypes: Array,
    farms: Array,
    users: Array,
    statuses: Array,
});

const formatDateForInput = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toISOString().slice(0, 10);
};

const form = useForm({
    employee_id: props.leaveRequest.employee_id || "",
    leave_type_id: props.leaveRequest.leave_type_id || "",
    farm_id: props.leaveRequest.farm_id || "",
    user_id: props.leaveRequest.user_id || "",
    start_date: formatDateForInput(props.leaveRequest.start_date),
    end_date: formatDateForInput(props.leaveRequest.end_date),
    total_days: props.leaveRequest.total_days || 1,
    reason: props.leaveRequest.reason || "",
    status: props.leaveRequest.status || "pending",
    approved_by: props.leaveRequest.approved_by?.id || null,
});

onMounted(() => {
    calculateTotalDays();
});

const calculateTotalDays = () => {
    if (form.start_date && form.end_date) {
        const startDate = new Date(form.start_date);
        const endDate = new Date(form.end_date);

        // Calculate the difference in milliseconds
        const timeDifference = endDate.getTime() - startDate.getTime();

        // Convert to days, adding 1 to include both start and end dates
        const dayDifference =
            Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

        form.total_days = Math.max(1, dayDifference); // Ensure total_days is at least 1
    } else {
        form.total_days = 1;
    }
};

watch([() => form.start_date, () => form.end_date], calculateTotalDays);

const submit = () => {
    form.put(route("leave-requests.update", props.leaveRequest.id));
};
</script>
