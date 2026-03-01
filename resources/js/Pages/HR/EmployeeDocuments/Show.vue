<script setup>
import { Head, Link } from "@inertiajs/inertia-vue3";
import DocumentViewerModal from "@/Components/DocumentViewerModal.vue";
import { ref } from "vue";

const props = defineProps({
    employeeDocument: Object,
});

const showDocumentViewer = ref(false);

const openDocumentViewer = () => {
    showDocumentViewer.value = true;
};

const closeDocumentViewer = () => {
    showDocumentViewer.value = false;
};
</script>

<template>
    <Head :title="`Employee Document: ${employeeDocument.name}`" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Employee Document: {{ employeeDocument.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p
                                        class="text-gray-600 text-xl font-semibold"
                                    >
                                        Document Number
                                    </p>
                                    <p
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ employeeDocument.document_number }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-gray-600 text-xl font-semibold"
                                    >
                                        Employee
                                    </p>
                                    <p
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{
                                            employeeDocument.employee.first_name
                                        }}
                                        {{
                                            employeeDocument.employee.last_name
                                        }}
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <p
                                        class="text-gray-600 text-xl font-semibold"
                                    >
                                        Document Type
                                    </p>
                                    <p
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ employeeDocument.document_type }}
                                        {{
                                            employeeDocument.expiry_date
                                                ? `-
                                        Expires on ${employeeDocument.expiry_date}`
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <p
                                        class="text-gray-600 text-xl font-semibold mb-2"
                                    >
                                        File
                                    </p>
                                    <div
                                        class="flex items-center space-x-4 gap-2"
                                    >
                                        <button
                                            @click="openDocumentViewer"
                                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2"
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
                                            Open in Viewer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <Link
                                :href="route('employee-documents.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            >
                                Back to Employee Documents
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <DocumentViewerModal
            :show="showDocumentViewer"
            :filePath="
                employeeDocument.file_path
                    ? `${employeeDocument.file_path}`
                    : null
            "
            @close="closeDocumentViewer"
        />
    </Layout>
</template>
