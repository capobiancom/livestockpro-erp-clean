<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Record New Expense</h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the expense's primary details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Expense Title
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                placeholder="Enter expense title"
                                required
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold"
                                    >{{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}</span
                                >
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                    placeholder="0.00"
                                    required
                                />
                            </div>
                            <p
                                v-if="form.errors.amount"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.amount }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Incurred On <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.incurred_on"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                required
                            />
                            <p
                                v-if="form.errors.incurred_on"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.incurred_on }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Association Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Association Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Link this expense to a farm or staff member
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Farm
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.farm_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                            >
                                <option value="">Select Farm</option>
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
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.farm_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Staff Member
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.staff_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                            >
                                <option value="">Select Staff</option>
                                <option
                                    v-for="member in staff"
                                    :key="member.id"
                                    :value="member.id"
                                >
                                    {{ member.first_name }}
                                    {{ member.last_name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.staff_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.staff_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Additional Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add any additional notes or details
                    </p>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Notes
                            <span class="text-gray-400 text-xs"
                                >(Optional)</span
                            >
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                            placeholder="Additional notes about this expense..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('expenses.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                            form.processing ? "Recording..." : "Record Expense"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    farms: Array,
    staff: Array,
});

const form = useForm({
    title: "",
    amount: "",
    incurred_on: "",
    farm_id: "",
    staff_id: "",
    notes: "",
});

function submit() {
    form.post("/expenses");
}
</script>
