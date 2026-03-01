<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Edit Vaccine Type</h2>
            <p class="text-sm text-gray-500 mt-1">Update vaccine type information</p>
        </template>

        <div class="max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="bg-white rounded-lg shadow-md p-6">
                <!-- Section 1: Vaccine Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        1. Vaccine Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.name }"
                                placeholder="e.g., Rabies Vaccine, FMD Vaccine"
                                required
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Manufacturer
                            </label>
                            <input
                                v-model="form.manufacturer"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.manufacturer }"
                                placeholder="e.g., Zoetis, Merck Animal Health"
                            />
                            <p v-if="form.errors.manufacturer" class="text-red-500 text-xs mt-1">{{ form.errors.manufacturer }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Route of Administration
                            </label>
                            <input
                                v-model="form.route"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.route }"
                                placeholder="e.g., Intramuscular, Subcutaneous"
                            />
                            <p v-if="form.errors.route" class="text-red-500 text-xs mt-1">{{ form.errors.route }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Dosage Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        2. Dosage Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dose
                            </label>
                            <input
                                v-model="form.dose"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.dose }"
                                placeholder="e.g., 2 ml, 5 ml"
                            />
                            <p v-if="form.errors.dose" class="text-red-500 text-xs mt-1">{{ form.errors.dose }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Doses per Animal
                            </label>
                            <input
                                v-model="form.doses_per_animal"
                                type="number"
                                min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.doses_per_animal }"
                                placeholder="e.g., 1, 2, 3"
                            />
                            <p v-if="form.errors.doses_per_animal" class="text-red-500 text-xs mt-1">{{ form.errors.doses_per_animal }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Additional Notes -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        3. Additional Notes
                    </h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes, storage instructions, or special considerations..."
                        ></textarea>
                        <p v-if="form.errors.notes" class="text-red-500 text-xs mt-1">{{ form.errors.notes }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <Link
                        :href="route('vaccine-types.index')"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-600 hover:to-purple-600 text-white px-6 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Updating...' : 'Update Vaccine Type' }}
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    vaccineType: Object,
});

const form = useForm({
    name: props.vaccineType.name || "",
    manufacturer: props.vaccineType.manufacturer || "",
    dose: props.vaccineType.dose || "",
    doses_per_animal: props.vaccineType.doses_per_animal || null,
    route: props.vaccineType.route || "",
    notes: props.vaccineType.notes || "",
});

const submit = () => {
    form.put(route('vaccine-types.update', props.vaccineType.id));
};
</script>
