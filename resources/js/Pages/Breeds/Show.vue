<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('breeds.index')"
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
                            {{ breed.name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Breed Details</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('breeds.edit', breed.id)"
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Breed
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
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4"
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
                            <div class="border-l-4 border-emerald-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Breed Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ breed.name }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Breed Code
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        v-if="breed.code"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800 font-mono"
                                    >
                                        {{ breed.code }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-lg font-medium text-gray-400"
                                    >
                                        —
                                    </span>
                                </dd>
                            </div>

                            <div
                                class="border-l-4 border-emerald-400 pl-4 md:col-span-2"
                            >
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Description
                                </dt>
                                <dd
                                    class="mt-2 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        breed.description ||
                                        "No description provided"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Origin & Type -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4"
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
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                            Origin & Type
                        </h3>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-emerald-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Origin
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ breed.origin || "—" }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-teal-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal Type
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ breed.animal_type || "—" }}
                                </dd>
                            </div>

                            <div
                                v-if="
                                    breed.characteristics &&
                                    Object.keys(breed.characteristics).length >
                                        0
                                "
                                class="md:col-span-2 border-l-4 border-emerald-400 pl-4"
                            >
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Characteristics
                                </dt>
                                <dd class="mt-2">
                                    <div class="space-y-2">
                                        <div
                                            v-for="(
                                                value, key
                                            ) in breed.characteristics"
                                            :key="key"
                                            class="flex items-start"
                                        >
                                            <span
                                                class="text-sm font-medium text-gray-700 capitalize"
                                                >{{ key }}:</span
                                            >
                                            <span
                                                class="text-sm text-gray-900 ml-2"
                                                >{{ value }}</span
                                            >
                                        </div>
                                    </div>
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
                            class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Breed</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                breed.name
                            }}</span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-teal-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Code</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                breed.code || "—"
                            }}</span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Animal Type</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                breed.animal_type || "—"
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg shadow-lg p-6 border border-emerald-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('breeds.edit', breed.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-emerald-100 rounded-lg transition border border-emerald-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-emerald-600"
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
                            :href="route('breeds.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-emerald-100 rounded-lg transition border border-emerald-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-emerald-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Breeds</span
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
                                formatDateTime(breed.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(breed.updated_at)
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

defineProps({
    breed: Object,
});

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
