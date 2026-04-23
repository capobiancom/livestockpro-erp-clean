<template>
    <Head title="Edit Artificial Insemination Record" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Edit Artificial Insemination Record
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Animal Selection Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Select Animal
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Choose the animal for this artificial insemination
                        record.
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
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
                                for="event"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Reproduction Event
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="event"
                                v-model="form.event"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.event,
                                }"
                                required
                            >
                                <option value="natural">Natural</option>
                                <option value="artificial_insemination">
                                    Artificial Insemination
                                </option>
                                <option value="insemination">
                                    Insemination
                                </option>
                                <option value="mating">Mating</option>
                                <option value="pregnancy_check">
                                    Pregnancy Check
                                </option>
                                <option value="calving">Calving</option>
                                <option value="abortion">Abortion</option>
                                <option value="other">Other</option>
                            </select>
                            <p
                                v-if="form.errors.event"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.event }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AI Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Artificial Insemination Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Edit the artificial insemination information
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Semen Batch No.
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.semen_batch_no"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.semen_batch_no,
                                }"
                                placeholder="e.g., ABC-12345"
                                required
                            />
                            <p
                                v-if="form.errors.semen_batch_no"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.semen_batch_no }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Breed <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.breed_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.breed_id,
                                }"
                                required
                            >
                                <option value="">Select Breed</option>
                                <option
                                    v-for="breed in breeds"
                                    :key="breed.id"
                                    :value="breed.id"
                                >
                                    {{ breed.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.breed_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.breed_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Semen Company
                            </label>
                            <input
                                v-model="form.semen_company"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.semen_company,
                                }"
                                placeholder="e.g., ABS Global"
                            />
                            <p
                                v-if="form.errors.semen_company"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.semen_company }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Insemination Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.insemination_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors.insemination_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.insemination_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.insemination_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Vet
                            </label>
                            <select
                                v-model="form.vet_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.vet_id,
                                }"
                            >
                                <option value="">Select Vet</option>
                                <option
                                    v-for="vet in vets"
                                    :key="vet.id"
                                    :value="vet.id"
                                >
                                    {{ vet.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.vet_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.vet_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Cost
                            </label>
                            <input
                                v-model="form.cost"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.cost }"
                                placeholder="e.g., 50.00"
                            />
                            <p
                                v-if="form.errors.cost"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.cost }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Remarks Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Remarks
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Additional observations or comments
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Remarks
                            </label>
                            <textarea
                                v-model="form.remarks"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.remarks,
                                }"
                                placeholder="Any additional remarks or observations..."
                            ></textarea>
                            <p
                                v-if="form.errors.remarks"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.remarks }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="
                            route(
                                'artificial-inseminations.show',
                                artificialInsemination.id,
                            )
                        "
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                            form.processing ? "Updating..." : "Update AI Record"
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
    artificialInsemination: Object,
    vets: Array,
    animals: Array, // New prop for animals
    breeds: Array, // New prop for breeds
});

const form = useForm({
    animal_id: props.artificialInsemination.animal_id || "", // Pre-fill animal_id
    event: props.artificialInsemination.event || "insemination", // Pre-fill event
    semen_batch_no: props.artificialInsemination.semen_batch_no,
    breed_id: props.artificialInsemination.breed_id || "", // Pre-fill breed_id
    semen_company: props.artificialInsemination.semen_company || "",
    insemination_date: props.artificialInsemination.insemination_date
        ? new Date(props.artificialInsemination.insemination_date)
              .toISOString()
              .split("T")[0]
        : "",
    vet_id: props.artificialInsemination.vet_id || "",
    cost: props.artificialInsemination.cost || 0,
    remarks: props.artificialInsemination.remarks || "",
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
    form.put(
        route(
            "artificial-inseminations.update",
            props.artificialInsemination.id,
        ),
    );
}
</script>
