<template>
    <Head title="Pregnancy Record Details" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('pregnancies.index')"
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
                            Pregnancy Record Details
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Pregnancy Details
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('pregnancies.edit', pregnancy.id)"
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
                <!-- Pregnancy Details -->
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            Pregnancy Details
                        </h3>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal
                                </dt>
                                <dd class="mt-1 text-lg font-medium">
                                    <Link
                                        v-if="pregnancy.animal"
                                        :href="
                                            route(
                                                'animals.show',
                                                pregnancy.animal.id,
                                            )
                                        "
                                        class="text-sm font-medium text-pink-600 hover:text-pink-900"
                                    >
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ pregnancy.animal.tag }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ pregnancy.animal.name || "—" }}
                                        </div>
                                    </Link>
                                    <span v-else class="text-gray-500">—</span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Breeding Record
                                </dt>
                                <dd class="mt-1 text-lg font-medium">
                                    <Link
                                        v-if="pregnancy.reproduction_record"
                                        :href="
                                            route(
                                                'reproduction-records.show',
                                                pregnancy.reproduction_record
                                                    .id,
                                            )
                                        "
                                        class="text-sm font-medium text-pink-600 hover:text-pink-900"
                                    >
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                pregnancy.reproduction_record
                                                    .event
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{
                                                formatDate(
                                                    pregnancy
                                                        .reproduction_record
                                                        .event_date,
                                                )
                                            }}
                                        </div>
                                    </Link>
                                    <span v-else class="text-gray-500">—</span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Confirmed Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDate(
                                            pregnancy.pregnancy_confirmed_date,
                                        )
                                    }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Expected Gestation
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        pregnancy.expected_gestation_days
                                            ? pregnancy.expected_gestation_days +
                                              " days"
                                            : "—"
                                    }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Expected Calving Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDate(
                                            pregnancy.expected_calving_date,
                                        )
                                    }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Pregnancy Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getPregnancyStatusBadgeClass(
                                                pregnancy.pregnancy_status,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ pregnancy.pregnancy_status || "—" }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Health Notes -->
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
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"
                                />
                            </svg>
                            Health Notes
                        </h3>
                    </div>

                    <div class="p-6">
                        <p
                            v-if="pregnancy.health_notes"
                            class="text-gray-700 whitespace-pre-wrap"
                        >
                            {{ pregnancy.health_notes }}
                        </p>
                        <p v-else class="text-gray-500 italic">
                            No health notes available
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Status -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Status
                    </h3>

                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Status</span
                            >
                            <span
                                :class="
                                    getPregnancyStatusBadgeClass(
                                        pregnancy.pregnancy_status,
                                    )
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ pregnancy.pregnancy_status || "—" }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Confirmed</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{
                                    formatDate(
                                        pregnancy.pregnancy_confirmed_date,
                                    )
                                }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Expected Calving</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{
                                    formatDate(pregnancy.expected_calving_date)
                                }}
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
                            :href="route('pregnancies.edit', pregnancy.id)"
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
                            :href="route('pregnancies.index')"
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
                                >Back to Pregnancy Records</span
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
                                formatDateTime(pregnancy.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(pregnancy.updated_at)
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
// Head, Link, and Layout are globally available via Inertia setup in app.js

const props = defineProps({
    pregnancy: Object,
});

const getPregnancyStatusBadgeClass = (status) => {
    const classes = {
        ongoing: "bg-green-100 text-green-800",
        aborted: "bg-red-100 text-red-800",
        completed: "bg-blue-100 text-blue-800",
    };

    return classes[status] || "bg-gray-100 text-gray-800";
};

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
