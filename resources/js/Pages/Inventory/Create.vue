<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Add Inventory Item
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Track and manage your inventory items and stock levels
                </p>
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
                        Enter the item's primary identification details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Item Name <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter item name"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., INV-001"
                            />
                            <p
                                v-if="form.errors.sku"
                                class="text-red-500 text-sm mt-1"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            >
                                <option value="">Select Category</option>
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
                                class="text-red-500 text-sm mt-1"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            >
                                <option value="">Select Supplier</option>
                                <option
                                    v-for="s in suppliers"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_id"
                                class="text-red-500 text-sm mt-1"
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
                        Quantity & Stock Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Manage stock levels and unit information
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter quantity"
                            />
                            <p
                                v-if="form.errors.quantity"
                                class="text-red-500 text-sm mt-1"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
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
                                class="text-red-500 text-sm mt-1"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Alert threshold"
                            />
                            <p
                                v-if="form.errors.min_quantity"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.min_quantity }}
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
                            <input
                                v-model="form.unit_cost"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Cost per unit"
                            />
                            <p
                                v-if="form.errors.unit_cost"
                                class="text-red-500 text-sm mt-1"
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
                        Add any additional notes or details
                    </p>

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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Additional notes about this item..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('inventory.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        {{ form.processing ? "Creating..." : "Create Item" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    suppliers: Array,
    categories: Array, // Add categories to props
});

const form = useForm({
    name: "",
    sku: "",
    inventory_category_id: "", // Change to inventory_category_id
    quantity: "",
    unit: "",
    min_quantity: "",
    unit_cost: "",
    supplier_id: "",
    notes: "",
});

function submit() {
    form.post("/inventory");
}
</script>
