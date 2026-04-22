<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Fixed Asset
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update information for {{ fixedAsset.name }}
                    </p>
                </div>
                <Link
                    :href="route('fixed-assets.show', fixedAsset.id)"
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
                    Back to Details
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Asset Identification Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Asset Identification
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the asset's primary identification details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Asset Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Tractor John Deere 5E"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Asset Type <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.asset_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.asset_type,
                                }"
                            >
                                <option value="">Select type...</option>
                                <option value="machinery">Machinery</option>
                                <option value="shed">Shed</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="equipment">Equipment</option>
                                <option value="land">Land</option>
                                <option value="building">Building</option>
                                <option value="other">Other</option>
                            </select>
                            <p
                                v-if="form.errors.asset_type"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.asset_type }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Serial / Reference Number
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.serial_number"
                                type="text"
                                placeholder="e.g., SN-2024-001"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.serial_number,
                                }"
                            />
                            <p
                                v-if="form.errors.serial_number"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.serial_number }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                            >
                                <option value="">Select status...</option>
                                <option value="active">Active</option>
                                <option value="under_maintenance">
                                    Under Maintenance
                                </option>
                                <option value="sold">Sold</option>
                                <option value="disposed">Disposed</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Location & Assignment Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Location & Assignment
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update farm assignment and location
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Farm <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.farm_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.farm_id,
                                }"
                            >
                                <option value="">Select farm...</option>
                                <option
                                    v-for="farm in farms"
                                    :key="farm.id"
                                    :value="farm.id"
                                >
                                    {{ farm.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.farm_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.farm_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Location / Area
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.location"
                                type="text"
                                placeholder="e.g., North Barn, Field 3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.location,
                                }"
                            />
                            <p
                                v-if="form.errors.location"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.location }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Valuation & Depreciation Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Valuation & Depreciation
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update purchase details and depreciation settings
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Purchase Value
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >{{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}</span
                                >
                                <input
                                    v-model="form.purchase_value"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="e.g., 250000.00"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                    :class="{
                                        'border-red-500':
                                            form.errors.purchase_value,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.purchase_value"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.purchase_value }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Purchase Date
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.purchase_date"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.purchase_date,
                                }"
                            />
                            <p
                                v-if="form.errors.purchase_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.purchase_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Useful Life (Years)
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.useful_life_years"
                                type="number"
                                min="1"
                                max="100"
                                placeholder="e.g., 10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.useful_life_years,
                                }"
                            />
                            <p
                                v-if="form.errors.useful_life_years"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.useful_life_years }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Depreciation Method
                                <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.depreciation_method"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.depreciation_method,
                                }"
                            >
                                <option value="straight_line">
                                    Straight Line
                                </option>
                            </select>
                            <p
                                v-if="form.errors.depreciation_method"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.depreciation_method }}
                            </p>
                        </div>

                        <!-- Depreciation Preview -->
                        <div
                            v-if="annualDepreciation > 0"
                            class="col-span-2 bg-rose-50 border border-rose-200 rounded-lg p-4"
                        >
                            <p class="text-sm font-semibold text-rose-700 mb-2">
                                Depreciation Preview (Straight Line)
                            </p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600"
                                        >Annual Depreciation:</span
                                    >
                                    <span class="font-bold text-gray-900 ml-2"
                                        >{{
                                            $page.props.appSettings
                                                .currency_symbol || "$"
                                        }}{{
                                            annualDepreciation.toLocaleString(
                                                undefined,
                                                { minimumFractionDigits: 2 },
                                            )
                                        }}</span
                                    >
                                </div>
                                <div>
                                    <span class="text-gray-600"
                                        >Monthly Depreciation:</span
                                    >
                                    <span class="font-bold text-gray-900 ml-2"
                                        >{{
                                            $page.props.appSettings
                                                .currency_symbol || "$"
                                        }}{{
                                            (annualDepreciation / 12).toLocaleString(
                                                undefined,
                                                { minimumFractionDigits: 2 },
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Notes
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                placeholder="Add any additional information about this asset..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.notes,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.notes"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('fixed-assets.show', fixedAsset.id)"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                        {{ form.processing ? "Updating..." : "Update Asset" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { computed } from "vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    fixedAsset: Object,
    farms: Array,
});

const today = new Date().toISOString().split("T")[0];

const form = useForm({
    name: props.fixedAsset.name || "",
    asset_type: props.fixedAsset.asset_type || "",
    farm_id: props.fixedAsset.farm_id || "",
    purchase_value: props.fixedAsset.purchase_value || "",
    purchase_date: props.fixedAsset.purchase_date
        ? new Date(props.fixedAsset.purchase_date).toISOString().split("T")[0]
        : "",
    useful_life_years: props.fixedAsset.useful_life_years || "",
    depreciation_method: props.fixedAsset.depreciation_method || "straight_line",
    status: props.fixedAsset.status || "active",
    location: props.fixedAsset.location || "",
    serial_number: props.fixedAsset.serial_number || "",
    notes: props.fixedAsset.notes || "",
});

const annualDepreciation = computed(() => {
    const val = parseFloat(form.purchase_value);
    const years = parseInt(form.useful_life_years);
    if (!val || !years || years <= 0) return 0;
    return val / years;
});

const submit = () => {
    form.put(`/fixed-assets/${props.fixedAsset.id}`);
};
</script>
