<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Inventory Category Details
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Details for category: {{ category.name }}
                    </p>
                </div>
                <Link
                    :href="route('categories.index')"
                    class="inline-flex items-center ml-5 gap-2 bg-gradient-to-r from-indigo-700 to-blue-500 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
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
                    Back to Categories
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-8">
                <h3
                    class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                >
                    <span
                        class="text-white rounded-full w-8 h-8 bg-gradient-to-r from-indigo-700 to-blue-500 flex items-center justify-center text-sm"
                        >1</span
                    >
                    Category Information
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Basic details about the inventory category
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">
                            Category Name:
                        </p>
                        <p class="text-gray-900 text-lg">{{ category.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">
                            Description:
                        </p>
                        <p class="text-gray-900 text-lg">
                            {{ category.description || "N/A" }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">
                            Created At:
                        </p>
                        <p class="text-gray-900 text-lg">
                            {{
                                new Date(
                                    category.created_at,
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">
                            Last Updated:
                        </p>
                        <p class="text-gray-900 text-lg">
                            {{
                                new Date(
                                    category.updated_at,
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
            >
                <Link
                    :href="route('categories.edit', category.id)"
                    class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-lg bg-gradient-to-r from-indigo-700 to-blue-500 font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                    Edit Category
                </Link>
                <button
                    @click="confirmDelete(category.id)"
                    class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    style="
                        background: linear-gradient(to right, #ef4444, #ec4899);
                    "
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
                    Delete Category
                </button>
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
                    <span class="font-semibold">{{ category.name }}</span
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
                        @click="deleteCategory"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Category
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    category: Object,
});

const showDeleteModal = ref(false);
const categoryToDelete = ref(null);

const deleteForm = useForm({});

const confirmDelete = (id) => {
    categoryToDelete.value = id;
    showDeleteModal.value = true;
};

const deleteCategory = () => {
    deleteForm.delete(route("categories.destroy", categoryToDelete.value), {
        onSuccess: () => {
            showDeleteModal.value = false;
            categoryToDelete.value = null;
        },
        onError: (errors) => {
            alert("Error deleting category: " + errors.message);
        },
    });
};
</script>
