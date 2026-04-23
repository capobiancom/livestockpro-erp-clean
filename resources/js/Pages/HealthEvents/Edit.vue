<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Health Event
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update health event information
                    </p>
                </div>
                <Link
                    :href="route('health-events.show', event.id)"
                    class="bg-gradient-to-r from-purple-500 ml-5 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Back to Details
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Event Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the health event details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Health Issue
                            </label>
                            <select
                                v-model="form.health_issue_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500':
                                        form.errors.health_issue_id,
                                }"
                            >
                                <option value="">Select health issue...</option>
                                <option
                                    v-for="healthIssue in healthIssues"
                                    :key="healthIssue.id"
                                    :value="healthIssue.id"
                                >
                                    {{ healthIssue.name }} -
                                    {{ healthIssue.animal?.tag }} -
                                    {{ healthIssue.animal?.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.health_issue_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.health_issue_id }}
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.animal_id,
                                }"
                            >
                                <option value="">Select animal...</option>
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
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.animal_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Event Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.event_type_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.event_type_id,
                                }"
                            >
                                <option value="">Select event type...</option>
                                <option
                                    v-for="eventType in eventTypes"
                                    :key="eventType.id"
                                    :value="eventType.id"
                                >
                                    {{ eventType.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.event_type_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.event_type_id }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g., Annual Checkup"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Enter detailed description..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Occurred At <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.occurred_at"
                                type="datetime-local"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.occurred_at,
                                }"
                            />
                            <p
                                v-if="form.errors.occurred_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.occurred_at }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Resolved At</label
                            >
                            <input
                                v-model="form.resolved_at"
                                type="datetime-local"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.resolved_at,
                                }"
                            />
                            <p
                                v-if="form.errors.resolved_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.resolved_at }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Cost Breakdown
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1"
                                        >Vet Fee</label
                                    >
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                            >{{
                                                $page.props.appSettings
                                                    .currency_symbol || "$"
                                            }}</span
                                        >
                                        <input
                                            v-model="form.vet_fee"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.vet_fee,
                                            }"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.vet_fee"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.vet_fee }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1"
                                        >Lab Cost</label
                                    >
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                            >{{
                                                $page.props.appSettings
                                                    .currency_symbol || "$"
                                            }}</span
                                        >
                                        <input
                                            v-model="form.lab_cost"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.lab_cost,
                                            }"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.lab_cost"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.lab_cost }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1"
                                        >Other Cost</label
                                    >
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                            >{{
                                                $page.props.appSettings
                                                    .currency_symbol || "$"
                                            }}</span
                                        >
                                        <input
                                            v-model="form.other_cost"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.other_cost,
                                            }"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.other_cost"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.other_cost }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 text-sm text-gray-700">
                                <span class="font-semibold">Total:</span>
                                {{ totalCost.toFixed(2) }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Treated By</label
                            >
                            <select
                                v-model="form.treated_by"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500': form.errors.treated_by,
                                }"
                            >
                                <option value="">Select staff...</option>
                                <option
                                    v-for="member in staff"
                                    :key="member.id"
                                    :value="member.id"
                                >
                                    {{ member.first_name }}
                                    {{ member.last_name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.treated_by"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.treated_by }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('health-events.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                                : "Update Health Event"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";
import { watch, computed } from "vue";

const props = defineProps({
    event: Object,
    animals: Array,
    staff: Array,
    eventTypes: Array,
    healthIssues: Array, // Add healthIssues to props
});

const form = useForm({
    animal_id: props.event.animal_id || "",
    event_type_id: props.event.event_type_id || "",
    health_issue_id: props.event.health_issue_id || "", // Initialize health_issue_id
    title: props.event.title || "",
    description: props.event.description || "",
    occurred_at: props.event.occurred_at
        ? props.event.occurred_at.slice(0, 16)
        : "",
    resolved_at: props.event.resolved_at
        ? props.event.resolved_at.slice(0, 16)
        : "",
    vet_fee: props.event.vet_fee ?? "",
    lab_cost: props.event.lab_cost ?? "",
    other_cost: props.event.other_cost ?? "",
    treated_by: props.event.treated_by || "",
});

const totalCost = computed(() => {
    return (
        (Number(form.vet_fee) || 0) +
        (Number(form.lab_cost) || 0) +
        (Number(form.other_cost) || 0)
    );
});

watch(
    () => form.health_issue_id,
    (newHealthIssueId) => {
        if (newHealthIssueId) {
            const selectedHealthIssue = props.healthIssues.find(
                (issue) => issue.id === newHealthIssueId,
            );
            if (selectedHealthIssue && selectedHealthIssue.animal_id) {
                form.animal_id = selectedHealthIssue.animal_id;
            }
        } else {
            form.animal_id = ""; // Clear animal_id if no health issue is selected
        }
    },
);

const submit = () => {
    form.put(`/health-events/${props.event.id}`);
};
</script>
