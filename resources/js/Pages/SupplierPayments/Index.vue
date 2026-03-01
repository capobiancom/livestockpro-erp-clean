<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Layout from "../Layout/AppLayout.vue";
import { debounce } from "lodash";

const props = defineProps({
    invoices: Object,
    filters: Object,
    statistics: Object,
});

const search = ref(props.filters?.search || "");
const showDeleteModal = ref(false);
const paymentToDelete = ref(null);

const handleSearch = debounce(() => {
    Inertia.get(
        route("supplier-payments.index"),
        {
            q: search.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

const clearSearch = () => {
    search.value = "";
    Inertia.get(
        route("supplier-payments.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (payment) => {
    paymentToDelete.value = payment;
    showDeleteModal.value = true;
};

const deletePayment = () => {
    Inertia.delete(
        route("supplier-payments.destroy", paymentToDelete.value.id),
        {
            onSuccess: () => {
                showDeleteModal.value = false;
                paymentToDelete.value = null;
            },
        },
    );
};

const getInvoiceTypeLabel = (type) => {
    if (type === "App\\Models\\Purchase") {
        return "Purchase Invoice";
    }
    return "Invoice";
};
</script>

<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Supplier Payments
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your supplier payment records
                    </p>
                </div>
                <Link
                    :href="route('supplier-payments.create')"
                    class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Payment
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
                                v-model="search"
                                @input="handleSearch"
                                type="text"
                                placeholder="Search by supplier name or reference number..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                        v-if="search"
                        @click="clearSearch"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            Total Payments
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics?.total_payments || 0 }}
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
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow-md p-5 border border-purple-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">
                            Total Amount Paid
                        </p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statistics?.total_amount_paid || 0 }} BDT
                        </p>
                    </div>
                    <div class="bg-purple-500 rounded-full p-3">
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg shadow-md p-5 border border-emerald-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-emerald-600 font-semibold">
                            Payments This Month
                        </p>
                        <p class="text-3xl font-bold text-emerald-700 mt-1">
                            {{ statistics?.this_month_payments || 0 }} BDT
                        </p>
                    </div>
                    <div class="bg-emerald-500 rounded-full p-3">
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
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-md p-5 border border-amber-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-semibold">
                            Payments This Year
                        </p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">
                            {{ statistics?.this_year_payments || 0 }} BDT
                        </p>
                    </div>
                    <div class="bg-amber-500 rounded-full p-3">
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Payments List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead
                        class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Date
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Supplier
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Payable Type
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Payable Ref.
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Amount
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Method
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Reference
                            </th>
                            <th
                                class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template
                            v-for="invoice in invoices.data"
                            :key="invoice.id"
                        >
                            <tr
                                class="bg-gray-100 hover:bg-gray-200 transition duration-150 cursor-pointer"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-gray-800"
                                >
                                    {{ invoice.invoice_number }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-gray-800"
                                >
                                    {{ invoice.supplier?.name || 'N/A' }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-gray-800"
                                    colspan="4"
                                >
                                    {{ getInvoiceTypeLabel(invoice.type) }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-gray-800 text-right"
                                >
                                    Total:
                                    {{
                                        invoice.total_amount
                                    }}
                                    BDT
                                </td>
                                <td
                                    class="px-6 py-4 text-center whitespace-nowrap"
                                >
                                    <!-- Actions for invoice if needed -->
                                </td>
                            </tr>
                            <tr
                                v-for="payment in invoice.supplier_payments"
                                :key="payment.id"
                                class="hover:bg-gray-50 transition duration-150"
                            >
                                <td
                                    class="px-6 py-2 text-sm text-gray-600 pl-10"
                                >
                                    {{
                                        new Date(
                                            payment.payment_date,
                                        ).toLocaleDateString("en-US", {
                                            day: "numeric",
                                            month: "short",
                                            year: "numeric",
                                        })
                                    }}
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    <!-- Supplier name already displayed in parent invoice row -->
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    Payment
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    {{ payment.reference_number }}
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    {{ payment.amount }} BDT
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    {{ payment.payment_method }}
                                </td>
                                <td class="px-6 py-2 text-sm text-gray-600">
                                    {{ payment.notes }}
                                </td>
                                <td
                                    class="px-6 py-2 text-center whitespace-nowrap"
                                >
                                    <div
                                        class="flex items-center justify-center space-x-2"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'supplier-payments.show',
                                                    payment.id,
                                                )
                                            "
                                            class="text-blue-600 hover:text-blue-800 font-semibold transition"
                                            title="View Details"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 inline-block"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="
                                                route(
                                                    'supplier-payments.edit',
                                                    payment.id,
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-800 font-semibold transition"
                                            title="Edit Payment"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 inline-block"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.38-2.827-2.828z"
                                                />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="confirmDelete(payment)"
                                            class="text-red-600 hover:text-red-800 font-semibold transition"
                                            title="Delete Payment"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 inline-block"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="!invoices.data.length">
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
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        />
                                    </svg>
                                    <p
                                        class="text-gray-500 text-lg font-medium"
                                    >
                                        No invoices found
                                    </p>
                                    <p class="text-gray-400 text-sm mt-1">
                                        Click "Add Payment" to record your first
                                        supplier payment
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="invoices.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in invoices.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow'
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
                    Are you sure you want to delete the payment
                    <span class="font-semibold">{{ paymentToDelete?.id }}</span
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
                        @click="deletePayment"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Payment
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>
