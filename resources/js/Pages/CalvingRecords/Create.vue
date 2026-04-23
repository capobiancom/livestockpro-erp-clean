<template>
    <Head title="Record Calving" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800"> {{ $t('record_calving') }} </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Calving Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >1</span
                        >
                        Calving Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the calving event information
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                for="pregnancy_id"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Pregnancy (Animal)
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="pregnancy_id"
                                v-model="form.pregnancy_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.pregnancy_id,
                                }"
                                required
                            >
                                <option value="">Select a pregnancy</option>
                                <option
                                    v-for="pregnancy in pregnancies"
                                    :key="pregnancy.id"
                                    :value="pregnancy.id"
                                >
                                    {{ pregnancy.display_name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.pregnancy_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.pregnancy_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="calving_date"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Calving Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="calving_date"
                                v-model="form.calving_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.calving_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.calving_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.calving_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="calving_type"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Calving Type
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="calving_type"
                                v-model="form.calving_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.calving_type,
                                }"
                                required
                            >
                                <option value="">Select calving type</option>
                                <option
                                    v-for="type in calvingTypes"
                                    :key="type"
                                    :value="type"
                                >
                                    {{ type }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.calving_type"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.calving_type }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="calves_count"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Number of Calves
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="calves_count"
                                v-model="form.calves_count"
                                type="number"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.calves_count,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.calves_count"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.calves_count }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="calf_gender"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Calf Gender
                            </label>
                            <select
                                id="calf_gender"
                                v-model="form.calf_gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.calf_gender,
                                }"
                            >
                                <option value="">Select gender</option>
                                <option
                                    v-for="gender in calfGenders"
                                    :key="gender"
                                    :value="gender"
                                >
                                    {{ gender }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.calf_gender"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.calf_gender }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="calving_outcome"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Calving Outcome
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="calving_outcome"
                                v-model="form.calving_outcome"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500':
                                        form.errors.calving_outcome,
                                }"
                                required
                            >
                                <option value="">Select outcome</option>
                                <option
                                    v-for="outcome in calvingOutcomes"
                                    :key="outcome"
                                    :value="outcome"
                                >
                                    {{ outcome }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.calving_outcome"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.calving_outcome }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >2</span
                        >
                        Notes
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Any additional notes or comments
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                for="notes"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Notes
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.notes,
                                }"
                                placeholder="Any additional notes..."
                            ></textarea>
                            <p
                                v-if="form.errors.notes"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('calving-records.index')"
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
                                #ec4899,
                                #f43f5e
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
                                ? "Saving..."
                                : "Save Calving Record"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
// Head, Link, and Layout are globally available via Inertia setup in app.js

const props = defineProps({
    pregnancies: Array,
    calvingTypes: Array,
    calfGenders: Array,
    calvingOutcomes: Array,
});

const form = useForm({
    pregnancy_id: "",
    calving_date: new Date().toISOString().split("T")[0],
    calving_type: "",
    calves_count: 1,
    calf_gender: "",
    calving_outcome: "",
    notes: "",
});

function submit() {
    form.post(route("calving-records.store"));
}
</script>
