<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('disease-treatments.index')"
                        class="text-gray-600 hover:text-gray-800"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ treatment.treatment?.name || "N/A" }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Treatment Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('disease-treatments.edit', treatment.id)"
                        class="bg-gradient-to-r from-purple-500 to-violet-500 ml-5 hover:from-purple-600 hover:to-violet-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                            />
                        </svg>
                        Edit Treatment
                    </Link>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Treatment Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Treatment Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ treatment.treatment?.name || "N/A" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-violet-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-semibold"
                                        :class="
                                            getStatusClass(treatment.status)
                                        "
                                    >
                                        {{ treatment.status }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal
                                </dt>
                                <dd
                                    v-if="treatment.health_issue?.animal"
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <span
                                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold"
                                    >
                                        {{
                                            treatment.health_issue.animal
                                                .name ||
                                            treatment.health_issue.animal.tag
                                        }}
                                        ({{
                                            treatment.health_issue.animal.tag
                                        }})
                                    </span>
                                </dd>
                                <dd v-else class="mt-1 text-lg text-gray-400">
                                    No animal specified
                                </dd>
                            </div>
                            <div class="border-l-4 border-violet-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Health Issue
                                </dt>
                                <dd
                                    v-if="treatment.health_issue"
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ treatment.health_issue.disease_name }}
                                </dd>
                                <dd v-else class="mt-1 text-lg text-gray-400">
                                    No health issue linked
                                </dd>
                            </div>

                            <div class="border-l-4 border-violet-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Health Event
                                </dt>
                                <dd
                                    v-if="treatment.health_event"
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ treatment.health_event.title }} ({{
                                        formatDateTime(
                                            treatment.health_event.occurred_at,
                                        )
                                    }})
                                </dd>
                                <dd v-else class="mt-1 text-lg text-gray-400">
                                    No associated health event
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Medications -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                            Medications
                        </h3>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="
                                treatment.disease_treatment_medications &&
                                treatment.disease_treatment_medications.length
                            "
                        >
                            <div
                                v-for="medication in treatment.disease_treatment_medications"
                                :key="medication.id"
                                class="mb-4 p-4 border border-gray-200 rounded-lg bg-white"
                            >
                                <p
                                    class="text-lg font-medium text-gray-900 mb-1"
                                >
                                    Medicine:
                                    <span class="font-semibold">{{
                                        medication.medicine?.name || "-"
                                    }}</span>
                                </p>
                                <p class="text-sm text-gray-700">
                                    Dose: {{ medication.dose || "-" }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    Frequency: {{ medication.frequency || "-" }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    Duration:
                                    {{
                                        medication.duration_days
                                            ? `${medication.duration_days} days`
                                            : "-"
                                    }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    Status: {{ medication.status || "-" }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    Started:
                                    {{ formatDateTime(medication.started_at) }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    Ended:
                                    {{ formatDateTime(medication.ended_at) }}
                                </p>

                                <p
                                    v-if="medication.notes"
                                    class="text-sm text-gray-700 mt-2"
                                >
                                    Notes: {{ medication.notes }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-lg text-gray-400">
                            No medications specified
                        </p>
                    </div>
                </div>

                <!-- Description & Notes -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 8h10M7 12h10M7 16h10M9 18h6M12 6V3m0 3v3m0-3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Additional Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-6">
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Description
                                </dt>
                                <dd
                                    class="mt-1 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        treatment.description ||
                                        "No description provided"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-violet-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Notes
                                </dt>
                                <dd
                                    class="mt-1 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        treatment.notes || "No notes available"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Status
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-purple-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Current Status</span
                            >
                            <span
                                :class="getStatusClass(treatment.status)"
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ treatment.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(treatment.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(treatment.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    treatment: Object,
});

function getStatusClass(status) {
    const classes = {
        planned: "bg-blue-100 text-blue-800",
        ongoing: "bg-yellow-100 text-yellow-800",
        completed: "bg-green-100 text-green-800",
        discontinued: "bg-red-100 text-red-800",
    };
    return classes[status] || classes.planned;
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

const formatDateTime = (datetime) => {
    if (!datetime) return "—";
    return new Date(datetime).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
