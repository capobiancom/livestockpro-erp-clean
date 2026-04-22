<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('health-events.index')"
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
                            {{ event.title }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Health Event Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('health-events.edit', event.id)"
                        class="bg-gradient-to-r from-purple-700 to-indigo-500 ml-5 hover:from-purple-600 hover:to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Health Event
                    </Link>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-green-700 font-medium">
                    {{ $page.props.flash.success }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-700 to-indigo-500 px-6 py-4"
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
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Event Title
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ event.title }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Event Type
                                </dt>
                                <dd
                                    v-if="event.event_type"
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ event.event_type.name }}
                                </dd>
                                <dd
                                    v-else
                                    class="mt-1 text-2xl font-bold text-gray-400"
                                >
                                    —
                                </dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal
                                </dt>
                                <dd
                                    v-if="event.animal"
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ event.animal.name || event.animal.tag }}
                                    ({{ event.animal.tag }})
                                </dd>
                                <dd
                                    v-else
                                    class="mt-1 text-2xl font-bold text-gray-400"
                                >
                                    —
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Occurred At
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDateTime(event.occurred_at) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Resolved At
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDateTime(event.resolved_at) || "—"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Cost Breakdown
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <div class="space-y-1">
                                        <div class="flex justify-between gap-4">
                                            <span class="text-gray-600"
                                                >Vet Fee</span
                                            >
                                            <span class="font-semibold">{{
                                                $formatCurrency(event.vet_fee)
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span class="text-gray-600"
                                                >Lab Cost</span
                                            >
                                            <span class="font-semibold">{{
                                                $formatCurrency(event.lab_cost)
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span class="text-gray-600"
                                                >Other Cost</span
                                            >
                                            <span class="font-semibold">{{
                                                $formatCurrency(
                                                    event.other_cost,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between gap-4 pt-2 border-t border-gray-200"
                                        >
                                            <span
                                                class="text-gray-800 font-semibold"
                                                >Total</span
                                            >
                                            <span class="font-bold">{{
                                                $formatCurrency(event.cost)
                                            }}</span>
                                        </div>
                                    </div>
                                </dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Treated By
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ event.treated_by?.first_name || "—" }}
                                    {{ event.treated_by?.last_name || "" }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Description Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-700 to-indigo-500 px-6 py-4"
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            Description
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
                                        event.description ||
                                        "No description available"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Status Card -->
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
                                :class="
                                    event.resolved_at
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-orange-100 text-orange-800'
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ event.resolved_at ? "Resolved" : "Active" }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Event Type</span
                            >
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize bg-purple-100 text-purple-800"
                            >
                                {{ event.event_type?.name || "N/A" }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-purple-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total Cost</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                $formatCurrency(event.cost)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-lg p-6 border border-purple-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('health-events.edit', event.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-purple-100 rounded-lg transition border border-purple-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Information</span
                            >
                        </Link>
                        <Link
                            :href="route('health-events.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-purple-100 rounded-lg transition border border-purple-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Health Events</span
                            >
                        </Link>
                        <button
                            @click="confirmDelete"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-purple-100 rounded-lg transition border border-purple-200 w-full text-left"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-red-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-red-700"
                                >Delete Health Event</span
                            >
                        </button>
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
                                formatDateTime(event.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(event.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Confirm Deletion
                    </h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete the health event
                    <span class="font-semibold">{{
                        eventToDelete?.title
                    }}</span>
                    for
                    <span class="font-semibold">{{
                        eventToDelete?.animal?.tag
                    }}</span>
                    ? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteRecord"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Event
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    event: Object,
});

const showDeleteModal = ref(false);
const eventToDelete = ref(null);

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

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const confirmDelete = () => {
    eventToDelete.value = props.event;
    showDeleteModal.value = true;
};

const deleteRecord = () => {
    Inertia.delete(route("health-events.destroy", props.event.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            eventToDelete.value = null;
        },
    });
};
</script>
