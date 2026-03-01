<template>
    <Head title="Edit Health Issue" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Health Issue
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update health issue information
                    </p>
                </div>
                <Link
                    :href="route('health-issues.index')"
                    class="inline-flex items-center ml-5 gap-2 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
                    style="
                        background: linear-gradient(to right, #ef4444, #ec4899);
                    "
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
                    Back to Health Issue
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Basic Information Section -->
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
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the health issue identification details
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Health Issue Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Mastitis"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
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
                                Animal <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.animal_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.animal_id,
                                }"
                                required
                            >
                                <option value="">Select Animal</option>
                                <option
                                    v-for="a in animals"
                                    :key="a.id"
                                    :value="a.id"
                                >
                                    {{ a.tag }} - {{ a.name }}
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
                                Disease Name
                            </label>
                            <select
                                v-model="form.disease_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.disease_id,
                                }"
                            >
                                <option value="">Select Disease</option>
                                <option
                                    v-for="d in diseases"
                                    :key="d.id"
                                    :value="d.id"
                                >
                                    {{ d.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.disease_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.disease_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Diagnosed At
                            </label>
                            <input
                                v-model="form.diagnosed_at"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.diagnosed_at,
                                }"
                            />
                            <p
                                v-if="form.errors.diagnosed_at"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.diagnosed_at }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Severity
                            </label>
                            <select
                                v-model="form.severity"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.severity,
                                }"
                            >
                                <option value="">Select Severity</option>
                                <option value="mild">Mild</option>
                                <option value="moderate">Moderate</option>
                                <option value="severe">Severe</option>
                            </select>
                            <p
                                v-if="form.errors.severity"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.severity }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                                required
                            >
                                <option value="active">Active</option>
                                <option value="recovered">Recovered</option>
                                <option value="chronic">Chronic</option>
                                <option value="deceased">Deceased</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Diagnosed By (Vet/Staff)
                            </label>
                            <select
                                v-model="form.diagnosed_by"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.diagnosed_by,
                                }"
                            >
                                <option value="">Select Staff Member</option>
                                <option
                                    v-for="s in staff"
                                    :key="s.id"
                                    :value="Number(s.id)"
                                >
                                    {{ s.name }} - {{ s.position }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.diagnosed_by"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.diagnosed_by }}
                            </p>
                        </div>

                        <div v-if="form.status === 'recovered'">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Recovered At
                            </label>
                            <input
                                v-model="form.recovered_at"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.recovered_at,
                                }"
                            />
                            <p
                                v-if="form.errors.recovered_at"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.recovered_at }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Clinical Information Section -->
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
                        Clinical Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update symptoms, diagnosis, and medical observations
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Symptoms
                            </label>
                            <textarea
                                v-model="form.symptoms"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.symptoms,
                                }"
                                placeholder="Describe the symptoms observed..."
                            ></textarea>
                            <p
                                v-if="form.errors.symptoms"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.symptoms }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Diagnosis
                            </label>
                            <textarea
                                v-model="form.diagnosis"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.diagnosis,
                                }"
                                placeholder="Detailed diagnosis and findings..."
                            ></textarea>
                            <p
                                v-if="form.errors.diagnosis"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.diagnosis }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.notes }"
                                placeholder="Any additional notes or observations..."
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
                        :href="route('health-issues.index')"
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
                                ? "Updating..."
                                : "Update Health Issue"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link, Head } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    healthIssue: Object,
    animals: Array,
    staff: Array,
    diseases: Array,
});

const form = useForm({
    name: props.healthIssue.name || "", // Initialize name field
    animal_id: props.healthIssue.animal_id,
    disease_id: props.healthIssue.disease_id || "",
    diagnosed_at: props.healthIssue.diagnosed_at
        ? new Date(props.healthIssue.diagnosed_at).toISOString().split("T")[0]
        : "",
    severity: props.healthIssue.severity || "",
    symptoms: props.healthIssue.symptoms || "",
    diagnosis: props.healthIssue.diagnosis || "",
    status: props.healthIssue.status || "active",
    recovered_at: props.healthIssue.recovered_at
        ? new Date(props.healthIssue.recovered_at).toISOString().split("T")[0]
        : "",
    diagnosed_by: props.healthIssue.diagnosed_by?.id || "",
    notes: props.healthIssue.notes || "",
});

function submit() {
    form.put(`/health-issues/${props.healthIssue.id}`);
}
</script>
