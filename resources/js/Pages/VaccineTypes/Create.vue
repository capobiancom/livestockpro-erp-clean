<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Add New Vaccine Type
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Create a new vaccine type for your livestock management
            </p>
        </template>

        <div class="max-w-4xl mx-auto">
            <form
                @submit.prevent="submit"
                class="bg-white rounded-lg shadow-md p-6"
            >
                <!-- Section 1: Vaccine Information -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ef4444,
                                    #ec4899
                                );
                            "
                            >1</span
                        >
                        Vaccine Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the vaccine identification details
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
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
                            <p
                                v-if="form.errors.name"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Manufacturer
                            </label>
                            <input
                                v-model="form.manufacturer"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.manufacturer,
                                }"
                                placeholder="e.g., Zoetis, Merck Animal Health"
                            />
                            <p
                                v-if="form.errors.manufacturer"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.manufacturer }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Route of Administration
                            </label>
                            <input
                                v-model="form.route"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.route }"
                                placeholder="e.g., Intramuscular, Subcutaneous"
                            />
                            <p
                                v-if="form.errors.route"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.route }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Dosage Details -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ef4444,
                                    #ec4899
                                );
                            "
                            >2</span
                        >
                        Dosage Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Provide details about the vaccine dosage
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Dose
                            </label>
                            <input
                                v-model="form.dose"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.dose }"
                                placeholder="e.g., 2 ml, 5 ml"
                            />
                            <p
                                v-if="form.errors.dose"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.dose }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Doses per Animal
                            </label>
                            <input
                                v-model="form.doses_per_animal"
                                type="number"
                                min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.doses_per_animal,
                                }"
                                placeholder="e.g., 1, 2, 3"
                            />
                            <p
                                v-if="form.errors.doses_per_animal"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.doses_per_animal }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Additional Notes -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ef4444,
                                    #ec4899
                                );
                            "
                            >3</span
                        >
                        Additional Notes
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Any additional notes, storage instructions, or special
                        considerations
                    </p>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes, storage instructions, or special considerations..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-xs mt-1"
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
                        :href="route('vaccine-types.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        style="
                            background: linear-gradient(
                                to right,
                                #ef4444,
                                #ec4899
                            );
                        "
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
                                ? "Creating..."
                                : "Create Vaccine Type"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const form = useForm({
    name: "",
    manufacturer: "",
    dose: "",
    doses_per_animal: null,
    route: "",
    notes: "",
});

const submit = () => {
    form.post(route("vaccine-types.store"));
};
</script>
