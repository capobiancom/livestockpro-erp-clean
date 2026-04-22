<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Add Disease Treatment
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Add animal disease treatments
                </p>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3
                    class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                >
                    <span
                        class="text-white rounded-full w-8 h-8 bg-gradient-to-r from-purple-500 to-violet-500 flex items-center justify-center text-sm"
                        >1</span
                    >
                    DiseaseTreatment Information
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Enter the disease treatment information details
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Health Issue <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.health_issue_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.health_issue_id,
                            }"
                            required
                        >
                            <option value="">Select Health Issue</option>
                            <option
                                v-for="issue in healthIssues"
                                :key="issue.id"
                                :value="issue.id"
                            >
                                {{ issue.disease_name }} ({{
                                    `${issue.animal.tag} - ${issue.animal.name}`
                                }})
                            </option>
                        </select>
                        <p
                            v-if="form.errors.health_issue_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.health_issue_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Health Event
                        </label>
                        <select
                            v-model="form.health_event_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.health_event_id,
                            }"
                        >
                            <option value="">Select Health Event</option>
                            <option
                                v-for="event in localHealthEvents"
                                :key="event.id"
                                :value="event.id"
                            >
                                {{ event.title }} ({{
                                    formatDate(event.occurred_at)
                                }})
                            </option>
                        </select>
                        <p
                            v-if="form.errors.health_event_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.health_event_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Treatment <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.treatment_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.treatment_id,
                            }"
                            required
                        >
                            <option value="">Select Treatment</option>
                            <option
                                v-for="treatment in treatments"
                                :key="treatment.id"
                                :value="treatment.id"
                            >
                                {{ treatment.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.treatment_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.treatment_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Resolved At
                        </label>
                        <input
                            v-model="form.resolved_at"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.resolved_at,
                            }"
                        />
                        <p
                            v-if="form.errors.resolved_at"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.resolved_at }}
                        </p>
                    </div>
                </div>

                <!-- Medication List Section -->
                <div
                    v-if="
                        form.treatment_id &&
                        diseaseTreatmentMedications.length > 0
                    "
                    class="mt-8 border-t border-gray-200 pt-6"
                >
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Medications for this Treatment
                    </h3>
                    <div
                        v-for="(
                            medication, index
                        ) in diseaseTreatmentMedications"
                        :key="index"
                        class="bg-gray-50 p-4 rounded-lg shadow-sm mb-4"
                    >
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-lg font-semibold text-gray-800">
                                {{ medication.name }}
                            </h4>
                            <button
                                type="button"
                                @click="removeMedication(index)"
                                class="text-red-500 hover:text-red-700 transition duration-200 p-2 rounded-full hover:bg-red-100"
                                aria-label="Delete medication"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm2 4a1 1 0 100 2h2a1 1 0 100-2H9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                        >
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
                                                `disease_treatment_medications.${index}.dose`
                                            ],
                                    }"
                                    placeholder="e.g., 10mg"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.dose`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.dose`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Frequency
                                </label>
                                <input
                                    v-model="medication.frequency"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.frequency`
                                            ],
                                    }"
                                    placeholder="e.g., Twice daily"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.frequency`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.frequency`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Duration (Days)
                                </label>
                                <input
                                    v-model="medication.duration_days"
                                    @input="updateMedicationDates(index)"
                                    type="number"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.duration_days`
                                            ],
                                    }"
                                    placeholder="e.g., 7"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.duration_days`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.duration_days`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Started At
                                </label>
                                <input
                                    v-model="medication.started_at"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.started_at`
                                            ],
                                    }"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.started_at`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.started_at`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Ended At
                                </label>
                                <input
                                    v-model="medication.ended_at"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.ended_at`
                                            ],
                                    }"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.ended_at`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.ended_at`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Quantity
                                </label>
                                <input
                                    v-model="medication.qty"
                                    @input="recalcMedicationCost(index)"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.qty`
                                            ],
                                    }"
                                    placeholder="e.g., 1"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.qty`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.qty`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Unit Cost
                                </label>
                                <input
                                    v-model="medication.unit_cost"
                                    @input="recalcMedicationCost(index)"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.unit_cost`
                                            ],
                                    }"
                                    placeholder="e.g., 25.00"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.unit_cost`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.unit_cost`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Total Cost
                                </label>
                                <input
                                    v-model="medication.total_cost"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    readonly
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 text-gray-700"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.total_cost`
                                            ],
                                    }"
                                    placeholder="0.00"
                                />
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.total_cost`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.total_cost`
                                        ]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="medication.status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors[
                                                `disease_treatment_medications.${index}.status`
                                            ],
                                    }"
                                    required
                                >
                                    <option value="planned">Planned</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="discontinued">
                                        Discontinued
                                    </option>
                                </select>
                                <p
                                    v-if="
                                        form.errors[
                                            `disease_treatment_medications.${index}.status`
                                        ]
                                    "
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{
                                        form.errors[
                                            `disease_treatment_medications.${index}.status`
                                        ]
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- This closes the div for each medication item -->
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.status }"
                            required
                        >
                            <option value="">Select Status</option>
                            <option value="planned">Planned</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="discontinued">Discontinued</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.description,
                            }"
                            placeholder="Treatment description..."
                        ></textarea>
                        <p
                            v-if="form.errors.description"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.description }}
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
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-purple-500 to-violet-500 hover:from-purple-600 hover:to-violet-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Treatment</span>
                    </button>
                    <Link
                        :href="route('disease-treatments.index')"
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
import Layout from "@/Layouts/AppLayout.vue";
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    healthIssues: Array,
    staff: Array,
    treatments: Array,
    healthEvents: Array, // Initial health events (empty for create)
    allStaff: Array, // All staff for medication line items
    resolved_at: String, // Add resolved_at to props
});

const localHealthEvents = ref(props.healthEvents || []);
const diseaseTreatmentMedications = ref([]); // New ref for dynamic medications

const form = useForm({
    health_issue_id: "",
    health_event_id: "",
    treatment_id: "",
    description: "",
    status: "",
    notes: "",
    resolved_at: props.resolved_at, // Initialize from props
    disease_treatment_medications: [], // New array for nested medications
});

// Watch for changes in health_issue_id to fetch health events
watch(
    () => form.health_issue_id,
    (newHealthIssueId) => {
        console.log("Health Issue ID changed to:", newHealthIssueId);
        if (newHealthIssueId) {
            fetchHealthEvents();
        } else {
            localHealthEvents.value = [];
            form.health_event_id = ""; // Reset health event when health issue is cleared
            form.resolved_at = ""; // Reset resolved_at
        }
    },
);

async function fetchHealthEvents() {
    if (!form.health_issue_id) {
        localHealthEvents.value = [];
        return;
    }
    try {
        console.log(
            "Fetching health events for health_issue_id:",
            form.health_issue_id,
        );
        const response = await axios.get(
            `/disease-treatments/health-events-by-issue/${form.health_issue_id}`,
        );
        console.log("Health events fetched:", response.data);
        localHealthEvents.value = response.data;
    } catch (error) {
        console.error("Error fetching health events:", error);
        localHealthEvents.value = [];
    }
}

function formatDate(dateString) {
    if (!dateString) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

import { format } from "date-fns";

function removeMedication(index) {
    diseaseTreatmentMedications.value.splice(index, 1);
}

// Watch for changes in treatment_id to fetch medications
watch(
    () => form.treatment_id,
    async (newTreatmentId) => {
        if (newTreatmentId) {
            try {
                const response = await axios.get(
                    `/disease-treatments/get-treatment-medications/${newTreatmentId}`,
                );
                diseaseTreatmentMedications.value = response.data.map(
                    (med) => ({
                        medicine_id: med.id,
                        name: med.name, // Assuming medicine has a name
                        dose: med.pivot.dose,
                        frequency: med.pivot.frequency,
                        duration_days: med.pivot.duration_days,
                        status: "planned", // Default status
                        started_at: format(new Date(), "yyyy-MM-dd"), // Current date
                        ended_at: calculateEndDate(
                            new Date(),
                            med.pivot.duration_days,
                        ),
                        qty: "",
                        unit_cost:
                            med.unit_cost !== null &&
                            med.unit_cost !== undefined
                                ? String(med.unit_cost)
                                : "",
                        total_cost: "",
                        notes: med.pivot.notes,
                    }),
                );
            } catch (error) {
                console.error("Error fetching treatment medications:", error);
                diseaseTreatmentMedications.value = [];
            }
        } else {
            diseaseTreatmentMedications.value = [];
        }
    },
);

function calculateEndDate(startDate, durationDays) {
    if (!startDate || !durationDays) return null;
    const date = new Date(startDate);
    date.setDate(date.getDate() + parseInt(durationDays));
    return format(date, "yyyy-MM-dd");
}

function updateMedicationDates(index) {
    const medication = diseaseTreatmentMedications.value[index];
    if (medication.started_at && medication.duration_days) {
        medication.ended_at = calculateEndDate(
            medication.started_at,
            medication.duration_days,
        );
    } else {
        medication.ended_at = null;
    }
}

function recalcMedicationCost(index) {
    const medication = diseaseTreatmentMedications.value[index];
    const qty = parseFloat(medication.qty || 0);
    const unitCost = parseFloat(medication.unit_cost || 0);

    if (!Number.isFinite(qty) || !Number.isFinite(unitCost)) {
        medication.total_cost = "";
        return;
    }

    medication.total_cost = (qty * unitCost).toFixed(2);
}

function submit() {
    form.disease_treatment_medications = diseaseTreatmentMedications.value;
    form.post("/disease-treatments");
}
</script>
