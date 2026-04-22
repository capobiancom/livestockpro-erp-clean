<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('feedings.index')"
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
                            Feeding Record #{{ feeding.id }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Feeding Record Details
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('feedings.edit', feeding.id)"
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                <!-- Feeding Information -->
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
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Feeding Information
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
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-teal-100 text-teal-800"
                                    >
                                        {{ feeding.animal?.tag }} -
                                        {{ feeding.animal?.name }}
                                    </span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Group
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ feeding.group?.name || "—" }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Feeding Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(feeding.feeding_date) }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Feeding Time
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ feeding.feeding_time || "—" }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Feeding Items -->
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
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                            Feeding Items
                        </h3>
                    </div>

                    <div class="p-6">
                        <div
                            v-if="feeding.feeding_items.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="item in feeding.feeding_items"
                                :key="item.id"
                                class="border-l-4 border-teal-400 pl-4"
                            >
                                <p
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Item
                                </p>
                                <p
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ item.item?.name }} ({{ item.quantity }}
                                    {{ item.item?.unit }})
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-gray-700">
                            No feeding items recorded.
                        </p>
                    </div>
                </div>

                <!-- Notes -->
                <div
                    v-if="feeding.notes"
                    class="bg-white rounded-lg shadow-lg overflow-hidden"
                >
                    <div
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4"
                    >
                        <h3 class="text-xl font-bold text-white">Notes</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">
                            {{ feeding.notes }}
                        </p>
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
                            class="flex items-center justify-between p-3 bg-teal-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Animal</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ feeding.animal?.tag || "—" }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-cyan-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Date</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ formatDate(feeding.feeding_date) }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-teal-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Items</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ feeding.feeding_items?.length || 0 }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg shadow-lg p-6 border border-teal-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('feedings.edit', feeding.id)"
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
                                >Edit Record</span
                            >
                        </Link>

                        <Link
                            :href="route('feedings.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-teal-100 rounded-lg transition border border-teal-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-teal-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Feedings</span
                            >
                        </Link>
                    </div>
                </div>

                <!-- Record Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(feeding.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(feeding.updated_at)
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
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    feeding: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};

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
