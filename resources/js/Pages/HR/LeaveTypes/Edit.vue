<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Leave Type: {{ leaveType.name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update the details of the leave type.
                    </p>
                </div>
                <Link
                    :href="route('leave-types.index')"
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
                    Back to Leave Types
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Leave Type Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Leave Type Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Fill in the leave type information.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Leave Type Name
                                <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="name"
                                v-model="form.name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.name,
                                }"
                            >
                                <option value="">Select leave type...</option>
                                <option value="Casual Leave">
                                    Casual Leave
                                </option>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Annual Leave">
                                    Annual Leave
                                </option>
                                <option value="Unpaid Leave">
                                    Unpaid Leave
                                </option>
                                <option value="Maternity Leave">
                                    Maternity Leave
                                </option>
                                <option value="Comp Off">Comp Off</option>
                            </select>
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
                                Max Days Per Year
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="max_days_per_year"
                                v-model="form.max_days_per_year"
                                type="number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.max_days_per_year,
                                }"
                            />
                            <p
                                v-if="form.errors.max_days_per_year"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.max_days_per_year }}
                            </p>
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="form.paid"
                                type="checkbox"
                                id="paid"
                                class="h-5 w-5 text-rose-600 focus:ring-rose-500 border-gray-300 rounded"
                            />
                            <label
                                for="paid"
                                class="ml-2 block text-sm font-semibold text-gray-700"
                            >
                                Paid Leave
                            </label>
                            <p
                                v-if="form.errors.paid"
                                class="mt-1 text-sm text-red-600 ml-2"
                            >
                                {{ form.errors.paid }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('leave-types.index')"
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
                        {{ form.processing ? "Saving..." : "Add Leave Type" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    leaveType: Object,
    farms: Array,
    users: Array,
    leaveTypeOptions: Array, // Add leaveTypeOptions prop
});

const form = useForm({
    name: props.leaveType.name,
    paid: !!props.leaveType.paid, // Explicitly cast to boolean
    max_days_per_year: props.leaveType.max_days_per_year,
});

const submit = () => {
    form.put(route("leave-types.update", props.leaveType.id));
};
</script>
