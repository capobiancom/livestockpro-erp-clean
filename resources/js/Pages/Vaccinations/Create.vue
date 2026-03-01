<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Record Vaccination
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Register a Vaccination administered to an animal in your
                    farm.
                </p>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <!-- Error Message -->
            <div
                v-if="$page.props.flash?.error"
                class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg"
            >
                <div class="flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-red-500 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <p class="text-red-700 font-medium">
                        {{ $page.props.flash.error }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3
                    class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                >
                    <span
                        class="text-white rounded-full w-8 h-8 flex items-center bg-gradient-to-r from-purple-500 to-violet-500 justify-center text-sm"
                        >1</span
                    >
                    Vaccination Information
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Enter the vaccination details
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Animal *
                        </label>
                        <select
                            v-model="form.animal_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.animal_id }"
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
                            Disease
                        </label>
                        <select
                            v-model="form.disease_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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
                            Administered At *
                        </label>
                        <input
                            v-model="form.administered_at"
                            type="datetime-local"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.administered_at,
                            }"
                            required
                        />
                        <p
                            v-if="form.errors.administered_at"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.administered_at }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Next Due At
                        </label>
                        <input
                            v-model="form.next_due_at"
                            type="datetime-local"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.next_due_at,
                            }"
                        />
                        <p
                            v-if="form.errors.next_due_at"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.next_due_at }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Administered By (Vet/Staff)
                        </label>
                        <select
                            v-model="form.staff_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.staff_id }"
                        >
                            <option value="">Select Staff Member</option>
                            <option
                                v-for="s in staff"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.name }} - {{ s.position }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.staff_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.staff_id }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes about this vaccination..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center bg-gradient-to-r from-purple-500 to-violet-500 justify-center text-sm"
                            >2</span
                        >
                        Medication Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add one or more medications used in this vaccination.
                    </p>

                    <div
                        v-for="(medication, index) in form.medications"
                        :key="index"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 border border-gray-200 rounded-lg"
                    >
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Medicine *
                            </label>
                            <select
                                v-model="medication.medicine_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors[
                                            `medications.${index}.medicine_id`
                                        ],
                                }"
                                required
                            >
                                <option value="">Select Medicine</option>
                                <option
                                    v-for="m in medicines"
                                    :key="m.id"
                                    :value="m.id"
                                >
                                    {{ m.name }} ({{ m.unit }})
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
                                Quantity *
                            </label>
                            <input
                                v-model="medication.quantity"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors[
                                            `medications.${index}.quantity`
                                        ],
                                }"
                                placeholder="Enter quantity"
                                required
                            />
                            <p
                                v-if="
                                    form.errors[`medications.${index}.quantity`]
                                "
                                class="text-red-500 text-sm mt-1"
                            >
                                {{
                                    form.errors[`medications.${index}.quantity`]
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Dose
                            </label>
                            <input
                                v-model="medication.dose"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors[
                                            `medications.${index}.dose`
                                        ],
                                }"
                                placeholder="e.g., 5ml"
                            />
                            <p
                                v-if="form.errors[`medications.${index}.dose`]"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors[`medications.${index}.dose`] }}
                            </p>
                        </div>
                        <div class="flex items-end">
                            <button
                                type="button"
                                @click="removeMedication(index)"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg transition duration-200"
                            >
                                Remove
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="addMedication"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg transition duration-200 mt-4"
                    >
                        Add Medication
                    </button>
                </div>

                <div class="flex gap-3 mt-6 justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-purple-500 to-violet-500 hover:from-purple-600 hover:to-violet-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Vaccination Record</span>
                    </button>
                    <Link
                        :href="route('vaccinations.index')"
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
    diseases: Array,
    medicines: Array,
});

const form = useForm({
    animal_id: "",
    disease_id: "",
    administered_at: "",
    medications: [],
    next_due_at: "",
    staff_id: "",
    notes: "",
});

function addMedication() {
    form.medications.push({
        medicine_id: "",
        quantity: 0,
        dose: "",
    });
}

function removeMedication(index) {
    form.medications.splice(index, 1);
}

function submit() {
    form.post("/vaccinations");
}
</script>
