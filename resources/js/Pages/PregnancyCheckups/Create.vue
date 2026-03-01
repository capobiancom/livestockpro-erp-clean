<template>
    <Head title="Record Pregnancy Checkup" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Record Pregnancy Checkup
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Checkup Details Section -->
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
                        Checkup Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the pregnancy checkup information
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
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
                                for="checkup_date"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Checkup Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="checkup_date"
                                v-model="form.checkup_date"
                                type="datetime-local"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.checkup_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.checkup_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.checkup_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="checkup_result"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Checkup Result
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="checkup_result"
                                v-model="form.checkup_result"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.checkup_result,
                                }"
                                required
                            >
                                <option value="">Select a result</option>
                                <option
                                    v-for="result in checkupResults"
                                    :key="result"
                                    :value="result"
                                >
                                    {{ result }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.checkup_result"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.checkup_result }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="checked_by"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Checked By (Vet)
                            </label>
                            <select
                                id="checked_by"
                                v-model="form.checked_by"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.checked_by,
                                }"
                            >
                                <option value="">Select a vet</option>
                                <option
                                    v-for="vet in vets"
                                    :key="vet.id"
                                    :value="vet.id"
                                >
                                    {{ vet.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.checked_by"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.checked_by }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observations Section -->
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
                        Observations
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Any additional observations or comments
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                for="observations"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Observations
                            </label>
                            <textarea
                                id="observations"
                                v-model="form.observations"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.observations,
                                }"
                                placeholder="Any additional observations..."
                            ></textarea>
                            <p
                                v-if="form.errors.observations"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.observations }}
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
                            form.pregnancy_id
                                ? route('pregnancies.show', form.pregnancy_id)
                                : route('pregnancy-checkups.index')
                        "
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
                                : "Save Checkup Record"
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
    vets: Array,
    checkupResults: Array,
});

const form = useForm({
    pregnancy_id: "",
    checkup_date: new Date()
        .toLocaleString("en-CA", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
        })
        .replace(/,/, ""), // Set default to current date and time
    checkup_result: "",
    observations: "",
    checked_by: "",
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
    form.post(route("pregnancy-checkups.store"));
}
</script>
