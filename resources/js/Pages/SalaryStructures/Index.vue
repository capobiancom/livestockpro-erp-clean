<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Salary Structures Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage employee salary structures
                    </p>
                </div>
                <Link
                    :href="route('salary-structures.create')"
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
                    Add New Salary Structure
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
                                v-model="form.q"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by employee name..."
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
                        v-if="form.q"
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

        <!-- Salary Structures Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div
                v-if="salaryStructures.data.length === 0"
                class="text-center py-12"
            >
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
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Salary Structures Found
                </h3>
                <p class="text-gray-500 mb-4">
                    Get started by adding your first salary structure.
                </p>
                <Link
                    :href="route('salary-structures.create')"
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
                    Add Your First Salary Structure
                </Link>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-blue-500">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Employee
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Farm
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Basic Salary
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Effective From
                        </th>
                        <th
                            class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="salaryStructure in salaryStructures.data"
                        :key="salaryStructure.id"
                        class="hover:bg-indigo-50 transition duration-150"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ salaryStructure.employee.first_name }}
                                {{ salaryStructure.employee.last_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ salaryStructure.farm.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ salaryStructure.basic_salary }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(salaryStructure.effective_from) }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    :href="
                                        route(
                                            'salary-structures.show',
                                            salaryStructure.id,
                                        )
                                    "
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
                                    :href="
                                        route(
                                            'salary-structures.edit',
                                            salaryStructure.id,
                                        )
                                    "
                                    class="text-indigo-600 hover:text-indigo-800 font-semibold transition"
                                    title="Edit Salary Structure"
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
                                    @click="confirmDelete(salaryStructure)"
                                    class="text-red-600 hover:text-red-800 font-semibold transition"
                                    title="Delete Salary Structure"
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
            v-if="salaryStructures.links.length > 3"
            class="bg-gray-50 px-6 py-4 border-t border-gray-200"
        >
            <div class="flex justify-center gap-1">
                <Link
                    v-for="(link, index) in salaryStructures.links"
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
                    Are you sure you want to delete this salary structure? This
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
                        @click="deleteSalaryStructureConfirmed"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Salary Structure
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm, router } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";

const props = defineProps({
    salaryStructures: Object,
    filters: Object,
});

const form = ref({
    q: props.filters.q,
});

const showDeleteModal = ref(false);
const salaryStructureToDelete = ref(null);

watch(
    () => form.value.q,
    debounce(() => {
        router.get(
            route("salary-structures.index"),
            { q: form.value.q },
            { preserveState: true, replace: true },
        );
    }, 300),
);

const deleteForm = useForm({});

const handleSearch = () => {
    router.get(
        route("salary-structures.index"),
        { q: form.value.q },
        { preserveState: true, replace: true },
    );
};

const clearSearch = () => {
    form.value.q = "";
    router.get(
        route("salary-structures.index"),
        {},
        { preserveState: true, replace: true },
    );
};

const confirmDelete = (salaryStructure) => {
    salaryStructureToDelete.value = salaryStructure;
    showDeleteModal.value = true;
};

const deleteSalaryStructureConfirmed = () => {
    deleteForm.delete(
        route("salary-structures.destroy", salaryStructureToDelete.value.id),
        {
            onSuccess: () => {
                showDeleteModal.value = false;
                salaryStructureToDelete.value = null;
            },
        },
    );
};

const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>
