<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('milk-sales.index')"
                        class="text-gray-600 hover:text-gray-800"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{
                                milkSale.invoice_number ||
                                "Milk Sale #" + milkSale.id
                            }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Milk Sale Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('milk-sales.edit', milkSale.id)"
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Milk Sale
                    </Link>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-cyan-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-cyan-700 font-medium">
                    {{ $page.props.flash.success }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Sale Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
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
                            Sale Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Sale Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(milkSale.sale_date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Quantity
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-emerald-600"
                                >
                                    {{ $formatNumber(milkSale.quantity) }}
                                    <span
                                        class="text-sm font-medium text-gray-600"
                                        >{{ milkSale.unit }}</span
                                    >
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Unit Price
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-blue-600"
                                >
                                    {{ $formatCurrency(milkSale.unit_price) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                            Customer Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-cyan-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Customer Name
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        v-if="milkSale.customer"
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ milkSale.customer.name }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-lg font-medium text-gray-400"
                                        >No customer specified</span
                                    >
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Contact Person
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        v-if="milkSale.customer?.contact_person"
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ milkSale.customer.contact_person }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-lg font-medium text-gray-400"
                                        >—</span
                                    >
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
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
                            Additional Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="border-l-4 border-cyan-400 pl-4">
                            <dt
                                class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                            >
                                Notes
                            </dt>
                            <dd class="mt-2 text-gray-700 whitespace-pre-wrap">
                                {{ milkSale.notes || "No notes available" }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Sale Summary Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Sale Summary
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg border-2 border-cyan-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total Revenue</span
                            >
                            <span
                                class="px-4 py-2 rounded-full text-sm font-bold bg-cyan-100 text-cyan-800"
                            >
                                {{ $formatCurrency(milkSale.total_price) }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border-2 border-emerald-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total Quantity</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ $formatNumber(milkSale.quantity) }}
                                {{ milkSale.unit }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Sale Date</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ formatDate(milkSale.sale_date) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div
                    class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg shadow-lg p-6 border border-cyan-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Info
                    </h3>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Unit Price</span
                            >
                            <span class="text-sm font-bold text-blue-600">{{
                                $formatCurrency(milkSale.unit_price)
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Quantity</span
                            >
                            <span class="text-sm font-bold text-emerald-600"
                                >{{ $formatNumber(milkSale.quantity) }}
                                {{ milkSale.unit }}</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total</span
                            >
                            <span class="text-sm font-bold text-cyan-600">{{
                                $formatCurrency(milkSale.total_price)
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Customer</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                milkSale.customer?.name || "—"
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Reference</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                milkSale.reference || "—"
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg shadow-lg p-6 border border-cyan-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('milk-sales.edit', milkSale.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-cyan-100 rounded-lg transition border border-cyan-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-cyan-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Milk Sale</span
                            >
                        </Link>
                        <Link
                            :href="route('milk-sales.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-cyan-100 rounded-lg transition border border-cyan-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-cyan-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Milk Sales</span
                            >
                        </Link>
                    </div>
                </div>

                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(milkSale.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(milkSale.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    milkSale: Object,
    currency: String,
});

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatDateTime = (datetime) => {
    if (!datetime) return "—";
    return new Date(datetime).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const deleteMilkSale = () => {
    if (
        confirm(
            "Are you sure you want to delete this milk sale? This action cannot be undone.",
        )
    ) {
        useForm({}).delete(`/milk-sales/${props.milkSale.id}`);
    }
};
</script>
