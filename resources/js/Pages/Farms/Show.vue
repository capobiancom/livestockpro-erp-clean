<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-800">Farm Details</h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('farms.edit', farm.id)"
                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
                    >
                        Edit Farm
                    </Link>
                    <Link
                        :href="route('farms.index')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200"
                    >
                        Back to List
                    </Link>
                </div>
            </div>
        </template>

        <div class="mt-6">
            <!-- Header Card -->
            <div
                class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg shadow-lg p-6 mb-6"
            >
                <div class="flex items-center">
                    <div
                        class="h-20 w-20 flex-shrink-0 bg-white rounded-full flex items-center justify-center text-green-600 font-bold text-3xl shadow-lg"
                    >
                        {{ getInitials(farm.name) }}
                    </div>
                    <div class="ml-6 text-white">
                        <h3 class="text-2xl font-bold">{{ farm.name }}</h3>
                        <p class="text-green-100 mt-1" v-if="farm.code">
                            Code: {{ farm.code }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Total Animals
                            </p>
                            <p class="text-3xl font-bold text-blue-700 mt-2">
                                {{ farm.animals_count || 0 }}
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Staff Members
                            </p>
                            <p class="text-3xl font-bold text-amber-700 mt-2">
                                {{ farm.staff_count || 0 }}
                            </p>
                        </div>
                        <div class="bg-amber-100 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-amber-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">Herds</p>
                            <p class="text-3xl font-bold text-green-700 mt-2">
                                {{ farm.herds_count || 0 }}
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-green-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Farm Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b border-green-200"
                    >
                        Farm Information
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Farm Name
                            </p>
                            <p class="text-lg text-gray-900 mt-1">
                                {{ farm.name }}
                            </p>
                        </div>

                        <div v-if="farm.code">
                            <p class="text-sm text-gray-600 font-semibold">
                                Farm Code
                            </p>
                            <p class="text-lg text-gray-900 mt-1">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800"
                                >
                                    {{ farm.code }}
                                </span>
                            </p>
                        </div>

                        <div v-if="farm.address">
                            <p class="text-sm text-gray-600 font-semibold">
                                Address
                            </p>
                            <p class="text-lg text-gray-900 mt-1">
                                {{ farm.address }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b border-green-200"
                    >
                        Contact Information
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Contact Name
                            </p>
                            <p class="text-lg text-gray-900 mt-1">
                                {{ farm.contact_name || "—" }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Contact Phone
                            </p>
                            <p class="text-lg text-gray-900 mt-1">
                                <a
                                    v-if="farm.contact_phone"
                                    :href="`tel:${farm.contact_phone}`"
                                    class="text-green-600 hover:text-green-700 hover:underline"
                                >
                                    {{ farm.contact_phone }}
                                </a>
                                <span v-else>—</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded-lg shadow-lg p-6 md:col-span-2">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-4 pb-3 border-b border-green-200"
                    >
                        System Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Farm ID
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ farm.id }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Record Created
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ formatDateTime(farm.created_at) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Last Updated
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ formatDateTime(farm.updated_at) }}
                            </p>
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
    farm: Object,
});

const getInitials = (name) => {
    const words = name.split(" ");
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
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
