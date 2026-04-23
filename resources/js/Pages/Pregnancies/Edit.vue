<template>
    <Head title="Edit Pregnancy Record" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Edit Pregnancy Record
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Pregnancy Details Section -->
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
                        Pregnancy Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Edit the pregnancy information
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                for="animal_id"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Animal <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="animal_id"
                                v-model="form.animal_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.animal_id,
                                }"
                                required
                            >
                                <option value="">Select an animal</option>
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
                                for="reproduction_record_id"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Breeding Record
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="reproduction_record_id"
                                v-model="form.reproduction_record_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500':
                                        form.errors.reproduction_record_id,
                                }"
                                required
                            >
                                <option value="">
                                    Select a breeding record
                                </option>
                                <option
                                    v-for="record in reproductionRecords"
                                    :key="record.id"
                                    :value="record.id"
                                >
                                    {{ record.display_name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.reproduction_record_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.reproduction_record_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="pregnancy_confirmed_date"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Pregnancy Confirmed Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="pregnancy_confirmed_date"
                                v-model="form.pregnancy_confirmed_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.pregnancy_confirmed_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.pregnancy_confirmed_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.pregnancy_confirmed_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="expected_gestation_days"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Expected Gestation Days
                            </label>
                            <input
                                id="expected_gestation_days"
                                v-model="form.expected_gestation_days"
                                type="number"
                                min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.expected_gestation_days,
                                }"
                                placeholder="e.g., 283"
                            />
                            <p
                                v-if="form.errors.expected_gestation_days"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.expected_gestation_days }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="pregnancy_status"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Pregnancy Status
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="pregnancy_status"
                                v-model="form.pregnancy_status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500':
                                        form.errors.pregnancy_status,
                                }"
                                required
                            >
                                <option value="ongoing">Ongoing</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="aborted">Aborted</option>
                                <option value="completed">Completed</option>
                            </select>
                            <p
                                v-if="form.errors.pregnancy_status"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.pregnancy_status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Health Notes Section -->
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
                        Health Notes
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Any health-related observations or comments
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                for="health_notes"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Health Notes
                            </label>
                            <textarea
                                id="health_notes"
                                v-model="form.health_notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.health_notes,
                                }"
                                placeholder="Any additional health notes..."
                            ></textarea>
                            <p
                                v-if="form.errors.health_notes"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.health_notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('pregnancies.show', pregnancy.id)"
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
                                ? "Updating..."
                                : "Update Pregnancy Record"
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
    pregnancy: Object,
    animals: Array,
    reproductionRecords: Array,
});

const form = useForm({
    animal_id: props.pregnancy.animal_id,
    reproduction_record_id: props.pregnancy.reproduction_record_id,
    pregnancy_confirmed_date: props.pregnancy.pregnancy_confirmed_date
        ? new Date(props.pregnancy.pregnancy_confirmed_date)
              .toISOString()
              .split("T")[0]
        : "",
    expected_gestation_days: props.pregnancy.expected_gestation_days,
    pregnancy_status: props.pregnancy.pregnancy_status,
    health_notes: props.pregnancy.health_notes || "",
});

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};

function submit() {
    form.put(route("pregnancies.update", props.pregnancy.id));
}
</script>
