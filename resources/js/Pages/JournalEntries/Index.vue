<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    TrashIcon,
    PencilIcon,
    PrinterIcon,
    DocumentChartBarIcon,
} from "@heroicons/vue/24/outline";
import Pagination from "@/Components/Pagination.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import { computed, ref } from "vue";

const dateFormatter = new Intl.DateTimeFormat(undefined, {
    year: "numeric",
    month: "short",
    day: "2-digit",
});

const formatEntryDate = (value) => {
    if (!value) return "—";

    // Common backend formats: "YYYY-MM-DD" or ISO strings.
    // Force YYYY-MM-DD to be parsed as UTC midnight to avoid timezone shifting.
    if (typeof value === "string" && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
        const [y, m, d] = value.split("-").map(Number);
        const dt = new Date(Date.UTC(y, m - 1, d));
        return dateFormatter.format(dt);
    }

    const dt = new Date(value);
    if (Number.isNaN(dt.getTime())) return String(value);

    return dateFormatter.format(dt);
};

const props = defineProps({
    journalEntries: {
        type: Object,
        required: true,
    },
});

const showingConfirmDeleteModal = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showingConfirmDeleteModal.value = true;
};

const closeModal = () => {
    showingConfirmDeleteModal.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;

    Inertia.delete(route("journal-entries.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => closeModal(),
    });
};
</script>

<template>
    <Layout title="Journal Entries">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Journal Entries
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg">
                    <div
                        class="p-6 bg-white border-b border-gray-200 flex justify-between items-center"
                    >
                        <h3 class="text-lg font-medium text-gray-900">
                            All Journal Entries
                        </h3>
                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('journal-voucher-report.index')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg border border-indigo-200 transition-all duration-200"
                            >
                                <DocumentChartBarIcon class="h-4 w-4" />
                                Voucher Report
                            </Link>
                            <Link :href="route('journal-entries.create')">
                                <PrimaryButton
                                    >Create Journal Entry</PrimaryButton
                                >
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        ID
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Farm
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Entry Date
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Reference Type
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Created By
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="entry in journalEntries.data"
                                    :key="entry.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    >
                                        {{ entry.id }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ entry.farm?.name ?? "N/A" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ formatEntryDate(entry.entry_date) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ entry.reference_type }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        <span
                                            :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                'bg-blue-100 text-blue-800':
                                                    entry.status === 'draft',
                                                'bg-green-100 text-green-800':
                                                    entry.status === 'posted',
                                                'bg-red-100 text-red-800':
                                                    entry.status === 'reversed',
                                            }"
                                        >
                                            {{ entry.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            entry.createdBy?.name ??
                                            entry.created_by?.name ??
                                            "N/A"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <a
                                            :href="
                                                route(
                                                    'journal-entries.print-voucher',
                                                    entry.id,
                                                )
                                            "
                                            target="_blank"
                                            class="text-indigo-600 hover:text-indigo-900 mr-2 inline-flex items-center p-2 border border-transparent rounded-full shadow-sm bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            title="Print Voucher"
                                        >
                                            <PrinterIcon class="h-4 w-4" />
                                        </a>
                                        <Link
                                            :href="
                                                route(
                                                    'journal-entries.edit',
                                                    entry.id,
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-900 mr-2 inline-flex items-center p-2 border border-transparent rounded-full shadow-sm bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            title="Edit"
                                        >
                                            <PencilIcon class="h-4 w-4" />
                                        </Link>
                                        <button
                                            @click="confirmDelete(entry)"
                                            class="text-red-600 hover:text-red-900 inline-flex items-center p-2 border border-transparent rounded-full shadow-sm bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            title="Delete"
                                        >
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="journalEntries.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"
                                    >
                                        No journal entries found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination
                        :links="journalEntries.links"
                        class="mt-4 p-6"
                    />
                </div>
            </div>
        </div>

        <ConfirmDeleteModal
            :show="showingConfirmDeleteModal"
            @close="closeModal"
            @confirm="deleteItem"
        />
    </Layout>
</template>
