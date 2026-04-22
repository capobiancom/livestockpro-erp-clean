<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { computed } from "vue";

const props = defineProps({
    stockMovement: Object,
});

const formattedMovementDate = computed(() => {
    if (!props.stockMovement?.movement_date) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(props.stockMovement.movement_date).toLocaleDateString(
        undefined,
        options,
    );
});

const formattedQuantity = computed(() => {
    if (
        props.stockMovement?.quantity === null ||
        props.stockMovement?.quantity === undefined
    )
        return "0.00";
    return parseFloat(props.stockMovement.quantity).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
});

const itemTypeDisplayName = computed(() => {
    if (!props.stockMovement?.item_type) return "N/A";
    const parts = props.stockMovement.item_type.split("\\");
    return parts[parts.length - 1];
});

const sourceTypeDisplayName = computed(() => {
    if (!props.stockMovement?.source_type) return "N/A";
    const parts = props.stockMovement.source_type.split("\\");
    return parts[parts.length - 1];
});

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
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
</script>

<template>
    <AppLayout>
        <Head :title="`Stock Movement: ${stockMovement.id}`" />

        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('stock-movements.index')"
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
                            Stock Movement Details
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed information for Stock Movement #{{
                                stockMovement.id
                            }}
                        </p>
                    </div>
                </div>
                <!-- No "Edit Item" button for stock movements -->
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Movement Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4"
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
                                    d="M8 13v-1m4 1v-1m4 1v-1M2 11a2 2 0 012-2h16a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6zM6 7V5a2 2 0 012-2h8a2 2 0 012 2v2"
                                />
                            </svg>
                            Movement Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    ID
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ stockMovement.id }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Item
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ stockMovement.item?.name ?? "N/A" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Item Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800"
                                    >
                                        {{ itemTypeDisplayName }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Movement Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            stockMovement.movement_type === 'in'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                    >
                                        {{ stockMovement.movement_type }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Quantity
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ formattedQuantity }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Unit Cost
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        $formatCurrency(stockMovement.unit_cost)
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Movement Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formattedMovementDate }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Source Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4"
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
                                    d="M8 13v-1m4 1v-1m4 1v-1M2 11a2 2 0 012-2h16a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6zM6 7V5a2 2 0 012-2h8a2 2 0 012 2v2"
                                />
                            </svg>
                            Source Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Source Event Type
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        stockMovement.source_event_type ?? "N/A"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Source Type
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ sourceTypeDisplayName }}
                                </dd>
                            </div>
                            <template
                                v-if="
                                    stockMovement.item_type ===
                                    'App\\Models\\Medicine'
                                "
                            >
                                <div class="border-l-4 border-indigo-400 pl-4">
                                    <dt
                                        class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Batch No
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-medium text-gray-900"
                                    >
                                        {{ stockMovement.batch_no ?? "N/A" }}
                                    </dd>
                                </div>
                                <div class="border-l-4 border-blue-400 pl-4">
                                    <dt
                                        class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Expiry Date
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-medium text-gray-900"
                                    >
                                        {{
                                            formatDate(
                                                stockMovement.expiry_date,
                                            )
                                        }}
                                    </dd>
                                </div>
                            </template>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Source ID
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ stockMovement.source_id }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Movement Summary Card -->
                <div
                    class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-lg p-6 border border-indigo-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Movement Summary
                    </h3>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Item</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                stockMovement.item?.name ?? "N/A"
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Movement Type</span
                            >
                            <span
                                :class="
                                    stockMovement.movement_type === 'in'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ stockMovement.movement_type }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Quantity</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                formattedQuantity
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Unit Cost</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                $formatCurrency(stockMovement.unit_cost)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- User & Farm Information -->
                <div
                    class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-lg p-6 border border-indigo-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        User & Farm Information
                    </h3>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Recorded By</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                stockMovement.user?.name ?? "N/A"
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Farm</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                stockMovement.farm?.name ?? "N/A"
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Timestamps
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(stockMovement.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(stockMovement.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any specific styles for this page here */
</style>
