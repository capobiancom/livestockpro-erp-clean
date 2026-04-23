<template>
    <Head title="Add New Treatment" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Add New Treatment
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create a new treatment record
                    </p>
                </div>
                <Link
                    :href="route('treatments.index')"
                    class="inline-flex items-center ml-5 gap-2 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
                    style="
                        background: linear-gradient(to right, #10b981, #06b6d4);
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
                    Back to Treatments
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Section 1: Treatment Information -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #10b981,
                                    #06b6d4
                                );
                            "
                            >1</span
                        >
                        Treatment Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the treatment identification details
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Treatment Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.name }"
                                placeholder="e.g., Respiratory Treatment Protocol"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Medications -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #10b981,
                                    #06b6d4
                                );
                            "
                            >2</span
                        >
                        Medications
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add medications for this treatment
                    </p>
                    <div
                        v-for="(medication, index) in form.medications"
                        :key="index"
                        class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Medicine <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="medication.medicine_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `medications.${index}.medicine_id`
                                            ],
                                    }"
                                    required
                                >
                                    <option :value="null" disabled>
                                        Select a medicine
                                    </option>
                                    <option
                                        v-for="medicine in medicines"
                                        :key="medicine.id"
                                        :value="medicine.id"
                                    >
                                        {{ medicine.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="
                                        form.errors[
                                            `medications.${index}.medicine_id`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `medications.${index}.medicine_id`
                                        ]
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Dose <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="medication.dose"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `medications.${index}.dose`
                                            ],
                                    }"
                                    placeholder="e.g., 10mg/kg"
                                    required
                                />
                                <p
                                    v-if="
                                        form.errors[`medications.${index}.dose`]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[`medications.${index}.dose`]
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Frequency
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="medication.frequency"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `medications.${index}.frequency`
                                            ],
                                    }"
                                    placeholder="e.g., Twice daily"
                                    required
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `medications.${index}.frequency`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `medications.${index}.frequency`
                                        ]
                                    }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Duration (Days)
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="medication.duration_days"
                                    type="number"
                                    min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `medications.${index}.duration_days`
                                            ],
                                    }"
                                    placeholder="Number of days"
                                    required
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `medications.${index}.duration_days`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `medications.${index}.duration_days`
                                        ]
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button
                                type="button"
                                @click="removeMedication(index)"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                            >
                                Remove Medication
                            </button>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="addMedication"
                        class="mt-4 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition"
                    >
                        Add Medication
                    </button>
                    <p
                        v-if="form.errors.medications"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.medications }}
                    </p>
                </div>

                <!-- Section 3: Instructions -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #10b981,
                                    #06b6d4
                                );
                            "
                            >3</span
                        >
                        Instructions
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Provide detailed instructions for the treatment
                    </p>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Treatment Instructions
                        </label>
                        <textarea
                            v-model="form.instructions"
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.instructions,
                            }"
                            placeholder="Detailed instructions for administering this treatment..."
                        ></textarea>
                        <p
                            v-if="form.errors.instructions"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.instructions }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('treatments.index')"
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
                                #10b981,
                                #06b6d4
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
                        {{ form.processing ? "Saving..." : "Create Treatment" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { Link, useForm, Head } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";

const props = defineProps({
    farms: Array,
    medicines: Array,
    suppliers: Array,
});

const form = useForm({
    name: "",
    instructions: "",
    medications: [],
});

const showNewMedicineForm = ref(false);
const newMedicineForm = useForm({
    name: "",
    description: "",
    medicine_group: "",
    supplier_id: null,
    farm_id: null,
});

const addMedication = () => {
    form.medications.push({
        medicine_id: null,
        dose: "",
        frequency: "",
        duration_days: 1,
    });
};

const removeMedication = (index) => {
    form.medications.splice(index, 1);
};

const submitNewMedicine = () => {
    newMedicineForm.farm_id = props.farms[0]?.id; // Assuming a single farm for farm owner
    newMedicineForm.post(route("medicines.store"), {
        onSuccess: () => {
            // Optionally refresh medicines list or add new medicine to props.medicines
            // For now, we'll just hide the form and clear it.
            showNewMedicineForm.value = false;
            newMedicineForm.reset();
            // A full page reload might be necessary to get the updated medicines list
            // Inertia.reload({ only: ['medicines'] });
        },
    });
};

const submit = () => {
    form.post(route("treatments.store"));
};
</script>
