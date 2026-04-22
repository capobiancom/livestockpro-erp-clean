<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('fixed-assets.index')"
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
                            {{ fixedAsset.name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Fixed Asset Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('fixed-assets.edit', fixedAsset.id)"
                        class="bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Asset
                    </Link>
                </div>
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
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4"
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
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Asset Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Asset Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ fixedAsset.name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Asset Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getTypeBadgeClass(
                                                fixedAsset.asset_type,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                    >
                                        {{
                                            formatAssetType(
                                                fixedAsset.asset_type,
                                            )
                                        }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Serial / Reference Number
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ fixedAsset.serial_number || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getStatusBadgeClass(
                                                fixedAsset.status,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                    >
                                        {{ formatStatus(fixedAsset.status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Farm
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ fixedAsset.farm?.name || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Location / Area
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ fixedAsset.location || "—" }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Valuation & Depreciation -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4"
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
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Valuation & Depreciation
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Purchase Value
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}{{
                                        Number(
                                            fixedAsset.purchase_value,
                                        ).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                        })
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Current Book Value
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-green-700"
                                >
                                    {{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}{{
                                        Number(
                                            fixedAsset.current_book_value,
                                        ).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                        })
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Purchase Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(fixedAsset.purchase_date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Useful Life
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ fixedAsset.useful_life_years }}
                                    {{
                                        fixedAsset.useful_life_years === 1
                                            ? "year"
                                            : "years"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Depreciation Method
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    Straight Line
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Annual Depreciation
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-red-600"
                                >
                                    {{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}{{
                                        Number(
                                            fixedAsset.annual_depreciation,
                                        ).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                        })
                                    }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Depreciation Progress Bar -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p
                                class="text-sm font-semibold text-gray-600 mb-2"
                            >
                                Asset Value Progress
                            </p>
                            <div
                                class="w-full bg-gray-200 rounded-full h-3 overflow-hidden"
                            >
                                <div
                                    class="bg-gradient-to-r from-rose-500 to-pink-500 h-3 rounded-full transition-all duration-500"
                                    :style="{
                                        width: bookValuePercentage + '%',
                                    }"
                                ></div>
                            </div>
                            <div
                                class="flex justify-between text-xs text-gray-500 mt-1"
                            >
                                <span>Remaining Value: {{ bookValuePercentage }}%</span>
                                <span>Depreciated: {{ 100 - bookValuePercentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div
                    v-if="fixedAsset.notes"
                    class="bg-white rounded-lg shadow-lg overflow-hidden"
                >
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4"
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
                            Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">
                            {{ fixedAsset.notes }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Status
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Current Status</span
                            >
                            <span
                                :class="
                                    getStatusBadgeClass(fixedAsset.status)
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold"
                            >
                                {{ formatStatus(fixedAsset.status) }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Age</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                calculateAge(fixedAsset.purchase_date)
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Remaining Life</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                calculateRemainingLife(
                                    fixedAsset.purchase_date,
                                    fixedAsset.useful_life_years,
                                )
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Book Value</span
                            >
                            <span class="text-sm font-bold text-green-700"
                                >{{
                                    $page.props.appSettings.currency_symbol ||
                                    "$"
                                }}{{
                                    Number(
                                        fixedAsset.current_book_value,
                                    ).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                    })
                                }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg shadow-lg p-6 border border-rose-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('fixed-assets.edit', fixedAsset.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-rose-100 rounded-lg transition border border-rose-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Information</span
                            >
                        </Link>
                        <Link
                            :href="route('fixed-assets.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-rose-100 rounded-lg transition border border-rose-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Fixed Assets</span
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
                                formatDateTime(fixedAsset.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(fixedAsset.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    fixedAsset: Object,
});

const bookValuePercentage = computed(() => {
    if (!props.fixedAsset.purchase_value || props.fixedAsset.purchase_value <= 0)
        return 0;
    const pct =
        (props.fixedAsset.current_book_value / props.fixedAsset.purchase_value) * 100;
    return Math.max(0, Math.min(100, Math.round(pct)));
});

const getStatusBadgeClass = (status) => {
    const classes = {
        active: "bg-green-100 text-green-800",
        disposed: "bg-gray-100 text-gray-800",
        under_maintenance: "bg-yellow-100 text-yellow-800",
        sold: "bg-purple-100 text-purple-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getTypeBadgeClass = (type) => {
    const classes = {
        machinery: "bg-blue-100 text-blue-800",
        shed: "bg-amber-100 text-amber-800",
        vehicle: "bg-indigo-100 text-indigo-800",
        equipment: "bg-cyan-100 text-cyan-800",
        land: "bg-green-100 text-green-800",
        building: "bg-orange-100 text-orange-800",
        other: "bg-gray-100 text-gray-800",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const formatAssetType = (type) => {
    const labels = {
        machinery: "Machinery",
        shed: "Shed",
        vehicle: "Vehicle",
        equipment: "Equipment",
        land: "Land",
        building: "Building",
        other: "Other",
    };
    return labels[type] || type;
};

const formatStatus = (status) => {
    const labels = {
        active: "Active",
        disposed: "Disposed",
        under_maintenance: "Under Maintenance",
        sold: "Sold",
    };
    return labels[status] || status;
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

const calculateAge = (purchaseDate) => {
    if (!purchaseDate) return "Unknown";
    const purchased = new Date(purchaseDate);
    const today = new Date();
    const days = Math.floor((today - purchased) / (1000 * 60 * 60 * 24));

    if (days < 30) return `${days} days`;
    if (days < 365) return `${Math.floor(days / 30)} months`;

    const years = Math.floor(days / 365);
    const months = Math.floor((days % 365) / 30);
    return months > 0 ? `${years} yrs, ${months} mo` : `${years} years`;
};

const calculateRemainingLife = (purchaseDate, usefulLifeYears) => {
    if (!purchaseDate || !usefulLifeYears) return "Unknown";
    const purchased = new Date(purchaseDate);
    const endOfLife = new Date(purchased);
    endOfLife.setFullYear(endOfLife.getFullYear() + usefulLifeYears);
    const today = new Date();
    if (today >= endOfLife) return "Fully depreciated";
    const remainingDays = Math.floor(
        (endOfLife - today) / (1000 * 60 * 60 * 24),
    );
    const remainingYears = Math.floor(remainingDays / 365);
    const remainingMonths = Math.floor((remainingDays % 365) / 30);
    if (remainingYears > 0) {
        return remainingMonths > 0
            ? `${remainingYears} yrs, ${remainingMonths} mo`
            : `${remainingYears} years`;
    }
    return `${remainingMonths} months`;
};
</script>
