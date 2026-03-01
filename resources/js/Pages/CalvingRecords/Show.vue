<template>
    <Head title="Calving Record Details" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('calving-records.index')"
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
                            Calving Record Details
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed view of a calving event
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('calving-records.edit', calvingRecord.id)"
                        class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Record
                    </Link>

                    <button
                        @click="confirmDelete(calvingRecord)"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Delete Record
                    </button>
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
            <!-- Main -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Calving Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4"
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
                            Calving Information
                        </h3>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Pregnancy (Animal)
                                </dt>
                                <dd class="mt-1">
                                    <Link
                                        v-if="calvingRecord.pregnancy?.animal"
                                        :href="
                                            route(
                                                'pregnancies.show',
                                                calvingRecord.pregnancy.id,
                                            )
                                        "
                                        class="text-lg font-semibold text-pink-600 hover:text-pink-900"
                                    >
                                        {{ calvingRecord.pregnancy.animal.tag }}
                                        -
                                        {{
                                            calvingRecord.pregnancy.animal.name
                                        }}
                                    </Link>
                                    <span
                                        v-else
                                        class="text-lg font-medium text-gray-900"
                                        >—</span
                                    >
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Calving Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(calvingRecord.calving_date) }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Calving Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getCalvingTypeBadgeClass(
                                                calvingRecord.calving_type,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ calvingRecord.calving_type || "—" }}
                                    </span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Number of Calves
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ calvingRecord.calves_count ?? "—" }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Calf Gender
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900 capitalize"
                                >
                                    {{ calvingRecord.calf_gender || "—" }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Calving Outcome
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getOutcomeBadgeClass(
                                                calvingRecord.calving_outcome,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{
                                            calvingRecord.calving_outcome || "—"
                                        }}
                                    </span>
                                </dd>
                            </div>

                            <div
                                class="md:col-span-2 border-l-4 border-pink-400 pl-4"
                            >
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Notes
                                </dt>
                                <dd
                                    class="mt-2 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        calvingRecord.notes ||
                                        "No notes available"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Summary
                    </h3>

                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Outcome</span
                            >
                            <span
                                :class="
                                    getOutcomeBadgeClass(
                                        calvingRecord.calving_outcome,
                                    )
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ calvingRecord.calving_outcome || "—" }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Type</span
                            >
                            <span
                                :class="
                                    getCalvingTypeBadgeClass(
                                        calvingRecord.calving_type,
                                    )
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ calvingRecord.calving_type || "—" }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Calves</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ calvingRecord.calves_count ?? "—" }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg shadow-lg p-6 border border-pink-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>

                    <div class="space-y-3">
                        <Link
                            :href="
                                route('calving-records.edit', calvingRecord.id)
                            "
                            class="flex items-center gap-3 p-3 bg-white hover:bg-pink-100 rounded-lg transition border border-pink-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-pink-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Record</span
                            >
                        </Link>

                        <Link
                            :href="route('calving-records.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-pink-100 rounded-lg transition border border-pink-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-pink-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Calving Records</span
                            >
                        </Link>

                        <button
                            @click="confirmDelete(calvingRecord)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-red-50 rounded-lg transition border border-pink-200 w-full text-left"
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
                            <span class="text-sm font-semibold text-gray-700"
                                >Delete Record</span
                            >
                        </button>
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
                    <div class="bg-red-100 rounded-full p-3 mr-4">
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
                    Are you sure you want to delete this calving record? This
                    action cannot be undone.
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
                        Delete Record
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
// Head, Link, and Layout are globally available via Inertia setup in app.js

const props = defineProps({
    calvingRecord: Object,
});

const showDeleteModal = ref(false);
const recordToDelete = ref(null);

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};

const getCalvingTypeBadgeClass = (type) => {
    const classes = {
        normal: "bg-blue-100 text-blue-800",
        assisted: "bg-purple-100 text-purple-800",
        c_section: "bg-indigo-100 text-indigo-800",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const getOutcomeBadgeClass = (outcome) => {
    const classes = {
        successful: "bg-green-100 text-green-800",
        stillbirth: "bg-red-100 text-red-800",
        complication: "bg-yellow-100 text-yellow-800",
    };
    return classes[outcome] || "bg-gray-100 text-gray-800";
};

const confirmDelete = (record) => {
    recordToDelete.value = record;
    showDeleteModal.value = true;
};

const deleteRecord = () => {
    Inertia.delete(route("calving-records.destroy", recordToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            recordToDelete.value = null;
        },
    });
};
</script>
