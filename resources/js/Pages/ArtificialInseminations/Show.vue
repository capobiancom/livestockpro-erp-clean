<template>
    <Head title="Artificial Insemination Record Details" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('artificial-inseminations.index')"
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
                            AI Record Details
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Details for AI Record #{{
                                artificialInsemination.id
                            }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="
                            route(
                                'artificial-inseminations.edit',
                                artificialInsemination.id,
                            )
                        "
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 ml-5 hover:from-teal-600 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit AI Record
                    </Link>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- AI Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4"
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
                            Artificial Insemination Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Semen Batch No.
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ artificialInsemination.semen_batch_no }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Breed
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        artificialInsemination.breed?.name ||
                                        "N/A"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Semen Company
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        artificialInsemination.semen_company ||
                                        "N/A"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Insemination Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDate(
                                            artificialInsemination.insemination_date,
                                        )
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Vet
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        artificialInsemination.vet?.name ||
                                        "N/A"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Cost
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        $formatCurrency(
                                            artificialInsemination.cost,
                                        )
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Associated Reproduction Record -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4"
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
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                                />
                            </svg>
                            Associated Reproduction Record
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <Link
                                        v-if="
                                            artificialInsemination
                                                .reproduction_record?.animal?.id
                                        "
                                        :href="
                                            route(
                                                'animals.show',
                                                artificialInsemination
                                                    .reproduction_record?.animal
                                                    ?.id,
                                            )
                                        "
                                        class="text-teal-600 hover:text-teal-900"
                                    >
                                        {{
                                            artificialInsemination
                                                .reproduction_record?.animal
                                                ?.tag
                                        }}
                                        -
                                        {{
                                            artificialInsemination
                                                .reproduction_record?.animal
                                                ?.name
                                        }}
                                        ({{
                                            artificialInsemination
                                                .reproduction_record?.animal
                                                ?.gender
                                        }})
                                    </Link>
                                    <span v-else class="text-gray-500">—</span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Event Type
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        artificialInsemination
                                            .reproduction_record?.event
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Event Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDate(
                                            artificialInsemination
                                                .reproduction_record
                                                ?.event_date,
                                        )
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4"
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
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                />
                            </svg>
                            Remarks
                        </h3>
                    </div>
                    <div class="p-6">
                        <p
                            v-if="artificialInsemination.remarks"
                            class="text-gray-700 whitespace-pre-wrap"
                        >
                            {{ artificialInsemination.remarks }}
                        </p>
                        <p v-else class="text-gray-500 italic">
                            No remarks recorded
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div
                    class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg shadow-lg p-6 border border-teal-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="
                                route(
                                    'artificial-inseminations.edit',
                                    artificialInsemination.id,
                                )
                            "
                            class="flex items-center gap-3 p-3 bg-white hover:bg-teal-100 rounded-lg transition border border-teal-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-teal-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit AI Record</span
                            >
                        </Link>
                        <Link
                            :href="route('artificial-inseminations.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-teal-100 rounded-lg transition border border-teal-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-teal-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to AI Records</span
                            >
                        </Link>
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
                                formatDateTime(
                                    artificialInsemination.created_at,
                                )
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(
                                    artificialInsemination.updated_at,
                                )
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
    artificialInsemination: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
