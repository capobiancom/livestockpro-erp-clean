<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Edit Disease Treatment
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Edit animal disease treatments
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
                    Edit DiseaseTreatment Information
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Edit the disease treatment information details
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
                            @change="fetchHealthEvents"
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
                                {{ issue.name }} - {{ issue.animal?.tag }} (
                                {{ issue.animal?.name }}
                                )
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
                            :disabled="
                                !!props.treatment.health_event?.resolved_at
                            "
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
                                v-for="treatmentOption in treatments"
                                :key="treatmentOption.id"
                                :value="treatmentOption.id"
                            >
                                {{ treatmentOption.name }}
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
                            :disabled="
                                !!props.treatment.health_event?.resolved_at
                            "
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
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update Treatment</span>
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
        <ToastNotification ref="toast" />
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";
import { ref, watch } from "vue";
import axios from "axios";
import { format } from "date-fns"; // Import date-fns
import ToastNotification from "../../Components/ToastNotification.vue";

const props = defineProps({
    treatment: Object,
    healthIssues: Array,
    staff: Array,
    treatments: Array, // Add treatments to props
    healthEvents: Array, // Initial health events for the selected health issue
});

const localHealthEvents = ref(props.healthEvents || []);
const diseaseTreatmentMedications = ref(
    props.treatment.disease_treatment_medications.map((med) => ({
        ...med,
        unit_cost:
            med.unit_cost !== null && med.unit_cost !== undefined
                ? String(med.unit_cost)
                : "",
        started_at: med.started_at
            ? format(new Date(med.started_at), "yyyy-MM-dd")
            : null,
        ended_at: med.ended_at
            ? format(new Date(med.ended_at), "yyyy-MM-dd")
            : null,
    })),
);

const toast = ref(null);

const form = useForm({
    health_issue_id: props.treatment.health_issue_id || "",
    health_event_id: props.treatment.health_event_id || "", // Initialize health_event_id
    treatment_id: props.treatment.treatment_id || "", // Change to treatment_id
    description: props.treatment.description || "",
    status: props.treatment.status || "",
    notes: props.treatment.notes || "",
    resolved_at: props.treatment.health_event?.resolved_at || "", // Initialize from health_event
    disease_treatment_medications: [], // Initialize as empty, will be populated from diseaseTreatmentMedications ref
});

// Watch for changes in health_issue_id to fetch health events
watch(
    () => form.health_issue_id,
    (newHealthIssueId) => {
        if (newHealthIssueId) {
            fetchHealthEvents();
        } else {
            localHealthEvents.value = [];
            form.health_event_id = ""; // Reset health event when health issue is cleared
            form.resolved_at = ""; // Reset resolved_at
        }
    },
);

function removeMedication(index) {
    diseaseTreatmentMedications.value.splice(index, 1);
}

async function fetchHealthEvents() {
    if (!form.health_issue_id) {
        localHealthEvents.value = [];
        return;
    }
    try {
        const response = await axios.get(
            `/disease-treatments/health-events-by-issue/${form.health_issue_id}`,
        );
        localHealthEvents.value = response.data.filter(
            (event) => event.resolved_at === null,
        ); // Filter resolved events
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
    form.put(`/disease-treatments/${props.treatment.id}`, {
        onSuccess: () => {
            toast.value.showToast("Treatment updated successfully!", "success");
        },
        onError: (errors) => {
            if (errors && Object.keys(errors).length > 0) {
                const firstErrorKey = Object.keys(errors)[0];
                toast.value.showToast(errors[firstErrorKey], "error");
            } else {
                toast.value.showToast("An unexpected error occurred.", "error");
            }
        },
    });
}
</script>
