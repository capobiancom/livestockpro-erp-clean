<template>
    <Head :title="`Designation: ${designation.name}`" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Designation Details
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        View detailed information about the designation
                    </p>
                </div>
                <Link
                    :href="route('designations.index')"
                    class="bg-gradient-to-r ml-5 from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Back to Designations
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow-lg p-8 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Designation Name
                    </p>
                    <p class="text-lg text-gray-900 mt-1">
                        {{ designation.name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Level</p>
                    <p class="text-lg text-gray-900 mt-1">
                        {{ designation.level }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Farm</p>
                    <p class="text-lg text-gray-900 mt-1">
                        {{ designation.farm.name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Created At
                    </p>
                    <p class="text-lg text-gray-900 mt-1">
                        {{ new Date(designation.created_at).toLocaleString() }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Updated At
                    </p>
                    <p class="text-lg text-gray-900 mt-1">
                        {{ new Date(designation.updated_at).toLocaleString() }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                <Link
                    :href="route('designations.edit', designation.id)"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        />
                    </svg>
                    Edit Designation
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/Layout/AppLayout.vue"; // Assuming AppLayout is the correct layout

const props = defineProps({
    designation: Object,
});
</script>
