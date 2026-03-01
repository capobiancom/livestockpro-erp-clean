<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Record Milk Production
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Section 1: Basic Information -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full flex items-center justify-center font-bold"
                        >
                            1
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">
                            Basic Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Animal *
                            </label>
                            <select
                                v-model="form.animal_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.animal_id,
                                }"
                                required
                            >
                                <option value="">Select Animal</option>
                                <option
                                    v-for="animal in animals"
                                    :key="animal.id"
                                    :value="animal.id"
                                >
                                    {{ animal.tag }} - {{ animal.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.animal_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.animal_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Date *
                            </label>
                            <input
                                v-model="form.date"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.date }"
                                required
                            />
                            <p
                                v-if="form.errors.date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.date }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Production Details -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full flex items-center justify-center font-bold"
                        >
                            2
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">
                            Production Details
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Quantity (Liters) *
                            </label>
                            <input
                                v-model="form.quantity_liters"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.quantity_liters,
                                }"
                                placeholder="Enter quantity in liters"
                                required
                            />
                            <p
                                v-if="form.errors.quantity_liters"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.quantity_liters }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Staff Member
                            </label>
                            <select
                                v-model="form.staff_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.staff_id,
                                }"
                            >
                                <option value="">Select Staff Member</option>
                                <option
                                    v-for="staffMember in staff"
                                    :key="staffMember.id"
                                    :value="staffMember.id"
                                >
                                    {{ staffMember.first_name }}
                                    {{ staffMember.last_name }}
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

                <!-- Section 3: Additional Information -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full flex items-center justify-center font-bold"
                        >
                            3
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">
                            Additional Information
                        </h3>
                    </div>

                    <div class="ml-11">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes about this milk production record..."
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
                <div class="flex gap-3 mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Milk Record</span>
                    </button>
                    <Link
                        :href="route('milk-records.index')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200"
                    >
                        Cancel
                    </Link>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    animals: Array,
    staff: Array,
});

const form = useForm({
    animal_id: "",
    date: "",
    quantity_liters: "",
    staff_id: "",
    notes: "",
});

function submit() {
    form.post("/milk-records");
}
</script>
