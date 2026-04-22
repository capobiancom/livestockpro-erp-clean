<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Medicine Group Details
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    View details of the medicine group
                </p>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Group Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Name:</p>
                        <p class="text-gray-900">{{ medicineGroup?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Farm:</p>
                        <p class="text-gray-900">
                            {{ medicineGroup?.farm?.name }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
            >
                <Link
                    :href="route('medicine-groups.index')"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                >
                    Back to List
                </Link>
                <Link
                    :href="route('medicine-groups.edit', medicineGroup.id)"
                    class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    Edit Medicine Group
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    medicineGroup: Object,
});
</script>
