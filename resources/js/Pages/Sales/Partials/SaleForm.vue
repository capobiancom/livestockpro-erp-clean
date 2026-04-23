<script setup>
import { watch, computed, onMounted } from "vue";
import { PlusIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    form: Object, // The form object is now passed as a prop
    customers: Array,
    inventoryItems: Array,
    animals: Array,
    saleStatuses: Array,
});

const calculateTotalAmount = () => {
    props.form.total_amount = props.form.sales_items.reduce(
        (sum, item) => sum + item.total_price,
        0,
    );

    // Update paid_amount and status based on total_amount
    if (props.form.total_amount == 0) {
        props.form.paid_amount = 0;
        props.form.status = "unpaid";
    } else if (props.form.paid_amount >= props.form.total_amount) {
        props.form.paid_amount = props.form.total_amount;
        props.form.status = "paid";
    } else if (
        props.form.paid_amount > 0 &&
        props.form.paid_amount < props.form.total_amount
    ) {
        props.form.status = "partial";
    } else {
        props.form.paid_amount = 0;
        props.form.status = "unpaid";
    }
};

const updateItemTotals = (item) => {
    item.total_price = item.quantity * item.unit_price;
    calculateTotalAmount();
};

const updateDerivedUnitPrice = (item) => {
    let selectedItem = null;
    let newDerivedPrice = 0;

    if (item.item_type === "App\\Models\\InventoryItem") {
        selectedItem = props.inventoryItems.find(
            (invItem) => invItem.id === item.item_id,
        );

        newDerivedPrice = selectedItem ? selectedItem.unit_price : 0; // unit_price now contains fifo_unit_price from backend
    } else if (item.item_type === "App\\Models\\Animal") {
        selectedItem = props.animals.find(
            (animal) => animal.id === item.item_id,
        );
        console.log("Selected Animal:", selectedItem);
        newDerivedPrice = selectedItem ? selectedItem.unit_price || 0 : 0;
    }

    item.unit_price = newDerivedPrice;
    updateItemTotals(item);
};

const addSalesItem = () => {
    props.form.sales_items.push({
        item_type: "", // 'App\\Models\\InventoryItem', 'App\\Models\\Animal', 'App\\Models\\MilkSale'
        item_id: "",
        quantity: 1,
        unit_price: 0,
        total_price: 0,
    });
    calculateTotalAmount(); // Recalculate total amount after adding a new item
};

const removeSalesItem = (index) => {
    props.form.sales_items.splice(index, 1);
    calculateTotalAmount(); // Recalculate total amount after removing an item
};

const filteredItems = computed(() => (itemType) => {
    if (itemType === "App\\Models\\InventoryItem") {
        return props.inventoryItems;
    } else if (itemType === "App\\Models\\Animal") {
        return props.animals;
    }
    return [];
});

const calculatedDueAmount = computed(() => {
    return props.form.total_amount - props.form.paid_amount;
});

onMounted(() => {
    // Ensure initial calculation for existing sales items when the component mounts
    props.form.sales_items.forEach((item) => {
        updateItemTotals(item);
    });
    calculateTotalAmount(); // Initial calculation for total_amount, paid_amount, and status
});

watch(
    () => props.form.paid_amount,
    () => {
        calculateTotalAmount();
    },
);

watch(
    () => props.form.status,
    (newStatus) => {
        if (newStatus === "unpaid") {
            props.form.paid_amount = 0;
        }
    },
);
</script>

<template>
    <!-- Sale Information Section -->
    <div class="mb-8">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >1</span
            >
            Sale Information
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Basic sale details and customer information
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Customer <span class="text-red-500">*</span>
                </label>
                <select
                    id="customer_id"
                    v-model="form.customer_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.customer_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                    required
                >
                    <option value=""> {{ $t('select_customer') }} </option>
                    <option
                        v-for="customer in customers"
                        :key="customer.id"
                        :value="customer.id"
                    >
                        {{ customer.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.customer_id"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.customer_id }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Invoice Date <span class="text-red-500">*</span>
                </label>
                <input
                    id="invoice_date"
                    v-model="form.invoice_date"
                    type="date"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.invoice_date }"
                />
                <p
                    v-if="form.errors.invoice_date"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.invoice_date }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    id="status"
                    v-model="form.status"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.status }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                >
                    <option value=""> {{ $t('select_status') }} </option>
                    <option
                        v-for="statusOption in saleStatuses"
                        :key="statusOption.value"
                        :value="statusOption.value"
                    >
                        {{ statusOption.name }}
                    </option>
                </select>
                <p v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                    {{ form.errors.status }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Invoice Number
                    <span class="text-gray-400 text-xs"
                        > {{ $t('auto_generated_if_empty') }} </span
                    >
                </label>
                <input
                    id="invoice_number"
                    v-model="form.invoice_number"
                    type="text"
                    readonly
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                    :class="{ 'border-red-500': form.errors.invoice_number }"
                />
                <p
                    v-if="form.errors.invoice_number"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.invoice_number }}
                </p>
            </div>
        </div>
    </div>

    <!-- Sales Items Section -->
    <div class="mb-8 pt-8 border-t border-gray-200">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >2</span
            >
            Sales Items
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Add inventory items to this sale
        </p>

        <div
            v-for="(item, index) in form.sales_items"
            :key="index"
            class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg relative bg-gray-50"
        >
            <div class="md:col-span-2">
                <label
                    :for="`item_type-${index}`"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Item Type <span class="text-red-500">*</span>
                </label>
                <select
                    :id="`item_type-${index}`"
                    v-model="item.item_type"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                    :class="{
                        'border-red-500':
                            form.errors[`sales_items.${index}.item_type`],
                    }"
                    required
                    @change="
                        item.item_id = '';
                        item.unit_price = 0;
                        updateDerivedUnitPrice(item);
                    "
                >
                    <option value=""> {{ $t('select_item_type') }} </option>
                    <option value="App\Models\InventoryItem">
                        Inventory Item
                    </option>
                    <option value="App\Models\Animal"> {{ $t('animal') }} </option>
                </select>
                <p
                    v-if="form.errors[`sales_items.${index}.item_type`]"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors[`sales_items.${index}.item_type`] }}
                </p>

                <label
                    :for="`item_id-${index}`"
                    class="block text-sm font-semibold text-gray-700 mb-2 mt-4"
                > {{ $t('select_item') }} <span class="text-red-500">*</span>
                </label>
                <select
                    :id="`item_id-${index}`"
                    v-model="item.item_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                    :class="{
                        'border-red-500':
                            form.errors[`sales_items.${index}.item_id`],
                    }"
                    required
                    @change="updateDerivedUnitPrice(item)"
                >
                    <option value="">Select Item</option>
                    <option
                        v-for="availableItem in filteredItems(item.item_type)"
                        :key="availableItem.id"
                        :value="availableItem.id"
                    >
                        <template
                            v-if="
                                item.item_type === 'App\\Models\\InventoryItem'
                            "
                        >
                            {{ availableItem.name }} ({{ availableItem.unit }})
                        </template>
                        <template
                            v-else-if="item.item_type === 'App\\Models\\Animal'"
                        >
                            {{ availableItem.name }} ({{ availableItem.tag }})
                            ({{ availableItem.current_weight_kg }} kg)
                        </template>
                        <template
                            v-else-if="
                                item.item_type === 'App\\Models\\MilkSale'
                            "
                        >
                            {{ availableItem.reference }} ({{
                                availableItem.total_price
                            }}
                            BDT)
                        </template>
                    </option>
                </select>
                <p
                    v-if="form.errors[`sales_items.${index}.item_id`]"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors[`sales_items.${index}.item_id`] }}
                </p>
            </div>

            <div>
                <label
                    :for="`quantity-${index}`"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input
                    :id="`quantity-${index}`"
                    v-model.number="item.quantity"
                    type="number"
                    min="1"
                    required
                    @input="updateItemTotals(item)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{
                        'border-red-500':
                            form.errors[`sales_items.${index}.quantity`],
                    }"
                />
                <p
                    v-if="form.errors[`sales_items.${index}.quantity`]"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors[`sales_items.${index}.quantity`] }}
                </p>
            </div>

            <div>
                <label
                    :for="`unit_price-${index}`"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Unit Price <span class="text-red-500">*</span>
                </label>
                <input
                    :id="`unit_price-${index}`"
                    v-model.number="item.unit_price"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    @input="updateItemTotals(item)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{
                        'border-red-500':
                            form.errors[`sales_items.${index}.unit_price`],
                    }"
                />
                <p
                    v-if="form.errors[`sales_items.${index}.unit_price`]"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors[`sales_items.${index}.unit_price`] }}
                </p>
            </div>

            <div>
                <label
                    :for="`total_price-${index}`"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Total Price
                </label>
                <input
                    :id="`total_price-${index}`"
                    :value="(item.total_price || 0).toFixed(2)"
                    type="text"
                    readonly
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                />
            </div>

            <button
                type="button"
                @click="removeSalesItem(index)"
                class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition"
                v-if="form.sales_items.length > 1"
            >
                <TrashIcon class="h-5 w-5" />
            </button>
        </div>

        <button
            type="button"
            @click="addSalesItem"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200"
        >
            <PlusIcon class="h-5 w-5" /> Add Item
        </button>
    </div>

    <!-- Payment and Notes Section -->
    <div class="pt-8 border-t border-gray-200">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >3</span
            >
            Payment & Notes
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Payment details and additional notes for the sale
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Total Amount
                </label>
                <input
                    id="total_amount"
                    :value="(form.total_amount || 0).toFixed(2)"
                    type="text"
                    readonly
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Paid Amount <span class="text-red-500">*</span>
                </label>
                <input
                    id="paid_amount"
                    v-model.number="form.paid_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    :max="form.total_amount"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.paid_amount }"
                />
                <p
                    v-if="form.errors.paid_amount"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.paid_amount }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Due Amount
                </label>
                <input
                    id="due_amount"
                    :value="(calculatedDueAmount || 0).toFixed(2)"
                    type="text"
                    readonly
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Notes <span class="text-gray-400 text-xs">(Optional)</span>
                </label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.notes }"
                    placeholder="Additional notes about this sale..."
                ></textarea>
                <p v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                    {{ form.errors.notes }}
                </p>
            </div>
        </div>
    </div>
</template>
