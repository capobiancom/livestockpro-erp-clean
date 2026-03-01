<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Inventory Item
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update inventory information for {{ item.name }}
                    </p>
                </div>
                <Link
                    :href="route('inventory.show', item.id)"
                    class="bg-gradient-to-r from-indigo-500 ml-5 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the item's primary identification details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Name <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Fertilizer NPK 20-20-20"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
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
                                SKU
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                placeholder="e.g., FERT-NPK-001"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.sku }"
                            />
                            <p
                                v-if="form.errors.sku"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.sku }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Category
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.inventory_category_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.inventory_category_id,
                                }"
                            >
                                <option :value="null">
                                    Select category...
                                </option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.inventory_category_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.inventory_category_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Supplier
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.supplier_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.supplier_id,
                                }"
                            >
                                <option :value="null">
                                    Select supplier...
                                </option>
                                <option
                                    v-for="supplier in suppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.supplier_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quantity & Stock Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Quantity & Stock Management
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update stock levels and unit information
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Quantity
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.quantity"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="e.g., 100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.quantity,
                                }"
                            />
                            <p
                                v-if="form.errors.quantity"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.quantity }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Unit
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.unit"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.unit }"
                            >
                                <option value="">Select Unit</option>
                                <option value="kg">kg</option>
                                <option value="quintal">quintal</option>
                                <option value="ton">ton</option>
                                <option value="liter">liter</option>
                                <option value="gallon">gallon</option>
                                <option value="pcs">pcs</option>
                                <option value="pair">pair</option>
                                <option value="bundle">bundle</option>
                                <option value="roll">roll</option>
                                <option value="bag">bag</option>
                                <option value="sack">sack</option>
                                <option value="meter">meter</option>
                                <option value="feet">feet</option>
                                <option value="sqft">sqft</option>
                                <option value="sqm">sqm</option>
                                <option value="bale">bale</option>
                                <option value="packet">packet</option>
                                <option value="box">box</option>
                                <option value="carton">carton</option>
                            </select>
                            <p
                                v-if="form.errors.unit"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.unit }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Minimum Quantity
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.min_quantity"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="e.g., 10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.min_quantity,
                                }"
                            />
                            <p
                                v-if="form.errors.min_quantity"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.min_quantity }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Alert when stock falls below this level
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Unit Cost
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >$</span
                                >
                                <input
                                    v-model="form.unit_cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    :class="{
                                        'border-red-500': form.errors.unit_cost,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.unit_cost"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.unit_cost }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Additional Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Add any additional notes or information
                    </p>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
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
                                placeholder="Add any additional information or observations about this item..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.notes }"
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
                        :href="route('inventory.show', item.id)"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                        {{ form.processing ? "Updating..." : "Update Item" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    item: Object,
    suppliers: Array,
    categories: Array, // Add categories to props
});

const form = useForm({
    name: props.item.name || "",
    sku: props.item.sku || "",
    inventory_category_id: props.item.inventory_category_id || null, // Change to inventory_category_id
    quantity: props.item.quantity || "",
    unit: props.item.unit || "",
    min_quantity: props.item.min_quantity || "",
    unit_cost: props.item.unit_cost || "",
    supplier_id: props.item.supplier_id || null,
    notes: props.item.notes || "",
});

const submit = () => {
    form.put(`/inventory/${props.item.id}`);
};
</script>
