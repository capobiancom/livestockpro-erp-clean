<template>
    <Head title="Leave Types" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Leave Types Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Define and manage different types of leaves
                    </p>
                </div>
                <Link
                    :href="route('leave-types.create')"
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
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add New Leave Type
                </Link>
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
                                placeholder="Search by leave type name..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

        <!-- Leave Types Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div v-if="leaveTypes.data.length === 0" class="text-center py-12">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 mx-auto text-gray-300 mb-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h-10c-1.104 0-2-.896-2-2v-10c0-1.104.896-2 2-2h10c1.104 0 2 .896 2 2v10c0 1.104-.896 2-2 2zm-1-10h-8v6h8v-6z"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Leave Types Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Get started by adding your first leave type to the system.
                </p>
                <Link
                    :href="route('leave-types.create')"
                    class="inline-flex items-center bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-3 rounded-lg font-semibold"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add Your First Leave Type
                </Link>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-blue-500">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            ID
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Paid
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Max Days Per Year
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="leaveType in leaveTypes.data"
                        :key="leaveType.id"
                        class="hover:bg-indigo-50 transition duration-150"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ leaveType.id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ leaveType.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="{
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                    'bg-green-100 text-green-800':
                                        leaveType.paid,
                                    'bg-red-100 text-red-800': !leaveType.paid,
                                }"
                            >
                                {{ leaveType.paid ? "Yes" : "No" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ leaveType.max_days_per_year }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    :href="
                                        route('leave-types.show', leaveType.id)
                                    "
                                    class="text-gray-600 hover:text-gray-800 font-semibold transition"
                                    title="View Leave Type"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </Link>

                                <Link
                                    :href="
                                        route('leave-types.edit', leaveType.id)
                                    "
                                    class="text-indigo-600 hover:text-indigo-800 font-semibold transition"
                                    title="Edit Leave Type"
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
                                    @click="confirmDelete(leaveType)"
                                    class="text-red-600 hover:text-red-800 font-semibold transition"
                                    title="Delete Leave Type"
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
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="leaveTypes.links.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-lg mt-6"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in leaveTypes.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                        link.active
                            ? 'bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow'
                            : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'opacity-50 cursor-not-allowed',
                    ]"
                />
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
                    Are you sure you want to delete
                    <span class="font-semibold">{{
                        leaveTypeToDelete?.name
                    }}</span
                    >? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteLeaveType"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Leave Type
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    leaveTypes: Object,
    filters: Object,
});

const showDeleteModal = ref(false);
const leaveTypeToDelete = ref(null);
const searchQuery = ref(props.filters?.search || "");

const handleSearch = () => {
    Inertia.get(
        "/leave-types",
        { search: searchQuery.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearSearch = () => {
    searchQuery.value = "";
    Inertia.get(
        "/leave-types",
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const form = useForm({});

const confirmDelete = (leaveType) => {
    leaveTypeToDelete.value = leaveType;
    showDeleteModal.value = true;
};

const deleteLeaveType = () => {
    form.delete(route("leave-types.destroy", leaveTypeToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            leaveTypeToDelete.value = null;
        },
    });
};
</script>
