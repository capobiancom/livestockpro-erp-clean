<template>
    <Head title="Calves" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Newborn Calves
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage all newborn calves
                    </p>
                </div>
                <Link
                    :href="route('calves.create')"
                    class="bg-gradient-to-r ml-5 from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add Calf
                </Link>
            </div>
        </template>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex gap-4 items-center">
                    <div class="flex-1">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by tag number, mother, or health status..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            />
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 absolute left-3 top-3 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                    </div>
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg shadow-md p-5 border border-pink-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-pink-600 font-semibold">
                            Total Calves
                        </p>
                        <p class="text-3xl font-bold text-pink-700 mt-1">
                            {{ statistics.total_calves }}
                        </p>
                    </div>
                    <div class="bg-pink-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
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
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-5 border border-green-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-semibold">
                            Healthy Calves
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ statistics.healthy_calves }}
                        </p>
                    </div>
                    <div class="bg-green-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg shadow-md p-5 border border-yellow-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-600 font-semibold">
                            Weak Calves
                        </p>
                        <p class="text-3xl font-bold text-yellow-700 mt-1">
                            {{ statistics.weak_calves }}
                        </p>
                    </div>
                    <div class="bg-yellow-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
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
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-red-50 to-orange-50 rounded-lg shadow-md p-5 border border-red-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-600 font-semibold">
                            Critical Calves
                        </p>
                        <p class="text-3xl font-bold text-red-700 mt-1">
                            {{ statistics.critical_calves }}
                        </p>
                    </div>
                    <div class="bg-red-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            Male / Female
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics.male_calves }} /
                            {{ statistics.female_calves }}
                        </p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calves Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-gradient-to-r from-pink-500 to-rose-500 text-white"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Tag Number
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Mother
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Father
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Gender
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Birth Date
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Weight
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Health Status
                            </th>
                            <th
                                class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="calf in calves.data"
                            :key="calf.id"
                            class="hover:bg-pink-50 transition duration-150"
                        >
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ calf.tag_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <Link
                                    v-if="calf.mother"
                                    :href="
                                        route('animals.show', calf.mother.id)
                                    "
                                    class="text-sm font-medium text-pink-600 hover:text-pink-900"
                                >
                                    {{ calf.mother.tag }} ({{
                                        calf.mother.name
                                    }})
                                </Link>
                                <span v-else class="text-sm text-gray-500"
                                    >N/A</span
                                >
                            </td>
                            <td class="px-6 py-4">
                                <Link
                                    v-if="calf.father"
                                    :href="
                                        route('animals.show', calf.father.id)
                                    "
                                    class="text-sm font-medium text-pink-600 hover:text-pink-900"
                                >
                                    {{ calf.father.tag }} ({{
                                        calf.father.name
                                    }})
                                </Link>
                                <span v-else class="text-sm text-gray-500"
                                    >N/A</span
                                >
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 capitalize"
                            >
                                {{ calf.gender }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                            >
                                {{ formatDate(calf.birth_date) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                            >
                                {{ calf.birth_weight ?? "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            calf.health_status === 'healthy',
                                        'bg-yellow-100 text-yellow-800':
                                            calf.health_status === 'weak',
                                        'bg-red-100 text-red-800':
                                            calf.health_status === 'critical',
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                                >
                                    {{ calf.health_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('calves.show', calf.id)"
                                        class="text-blue-600 hover:text-blue-800 font-semibold transition"
                                        title="View Details"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                            />
                                            <path
                                                fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="route('calves.edit', calf.id)"
                                        class="text-pink-600 hover:text-pink-800 font-semibold transition"
                                        title="Edit Record"
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
                                    </Link>
                                    <button
                                        @click="confirmDelete(calf)"
                                        class="text-red-600 hover:text-red-800 font-semibold transition"
                                        title="Delete Record"
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
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!calves.data.length">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-12 w-12 text-gray-400 mb-4"
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
                                    <p
                                        class="text-gray-500 text-lg font-medium"
                                    >
                                        No calves found
                                    </p>
                                    <p class="text-gray-400 text-sm mt-1">
                                        Newborn calves will appear here after
                                        successful calving records.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="calves.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in calves.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white shadow'
                                : 'bg-white text-gray-700 hover:bg-gray-100',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                    />
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
                    Are you sure you want to delete this calf record? This
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
                        @click="deleteCalf"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Calf
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { debounce } from "lodash";
import { Head, Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    calves: Object,
    filters: Object,
    statistics: {
        type: Object,
        default: () => ({
            total_calves: 0,
            healthy_calves: 0,
            weak_calves: 0,
            critical_calves: 0,
            male_calves: 0,
            female_calves: 0,
        }),
    },
});

const searchQuery = ref(props.filters.q);
const showDeleteModal = ref(false);
const calfToDelete = ref(null);

const handleSearch = debounce((value) => {
    Inertia.get(
        route("calves.index"),
        { q: value },
        { preserveState: true, replace: true },
    );
}, 300);

const clearSearch = () => {
    searchQuery.value = "";
    Inertia.get(
        route("calves.index"),
        {},
        { preserveState: true, replace: true },
    );
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

const confirmDelete = (calf) => {
    calfToDelete.value = calf;
    showDeleteModal.value = true;
};

const deleteCalf = () => {
    Inertia.delete(route("calves.destroy", calfToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            calfToDelete.value = null;
        },
    });
};
</script>
