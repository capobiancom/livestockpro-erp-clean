<script setup>
import { computed } from "vue";
import { useForm, Link, Head } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    employeeDocument: Object,
    employees: Array,
});

const today = computed(() => new Date().toISOString().split("T")[0]);

const form = useForm({
    _method: "PUT",
    employee_id: props.employeeDocument.employee_id,
    document_type: props.employeeDocument.document_type,
    document_number: props.employeeDocument.document_number,
    expiry_date: props.employeeDocument.expiry_date,
    file: null, // File input for update
});

const submit = () => {
    form.post(route("employee-documents.update", props.employeeDocument.id));
};
</script>

<template>
    <Head title="Edit Employee Document" />

    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Employee Document
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update an existing employee document in the system
                    </p>
                </div>
                <Link
                    :href="route('employee-documents.index')"
                    class="text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2"
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
                    Back to Employee Documents
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Document Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Document Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the document's primary details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel
                                for="employee_id"
                                value="Employee"
                                class="mb-2"
                            />
                            <select
                                id="employee_id"
                                v-model="form.employee_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.employee_id,
                                }"
                            >
                                <option value="">Select Employee</option>
                                <option
                                    v-for="employee in employees"
                                    :key="employee.id"
                                    :value="employee.id"
                                >
                                    {{ employee.first_name }}
                                    {{ employee.last_name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-1 text-sm text-red-600"
                                :message="form.errors.employee_id"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="document_type"
                                value="Document Type"
                                class="mb-2"
                            />
                            <select
                                id="document_type"
                                v-model="form.document_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.document_type,
                                }"
                                required
                            >
                                <option value="">Select Document Type</option>
                                <option value="NID">NID</option>
                                <option value="License">License</option>
                                <option value="Certificate">Certificate</option>
                            </select>
                            <InputError
                                class="mt-1 text-sm text-red-600"
                                :message="form.errors.document_type"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="document_number"
                                value="Document Number"
                                class="mb-2"
                            />
                            <TextInput
                                id="document_number"
                                type="text"
                                class="w-full px-4 py-3"
                                v-model="form.document_number"
                                required
                                autocomplete="document_number"
                                :class="{
                                    'border-red-500':
                                        form.errors.document_number,
                                }"
                            />
                            <InputError
                                class="mt-1 text-sm text-red-600"
                                :message="form.errors.document_number"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="expiry_date"
                                value="Expiry Date"
                                class="mb-2"
                            />
                            <TextInput
                                id="expiry_date"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-3"
                                v-model="form.expiry_date"
                                autocomplete="expiry_date"
                                :class="{
                                    'border-red-500': form.errors.expiry_date,
                                }"
                            />
                            <InputError
                                class="mt-1 text-sm text-red-600"
                                :message="form.errors.expiry_date"
                            />
                        </div>

                        <div class="col-span-2">
                            <InputLabel
                                for="file"
                                value="Document File"
                                class="mb-2"
                            />
                            <input
                                type="file"
                                @input="form.file = $event.target.files[0]"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                :class="{ 'border-red-500': form.errors.file }"
                            />
                            <InputError
                                class="mt-1 text-sm text-red-600"
                                :message="form.errors.file"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Supported formats: PDF, DOCX, JPG, PNG (Max 5MB)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('employee-documents.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        {{ form.processing ? "Saving..." : "Update Document" }}
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
