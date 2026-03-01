<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('purchases.index')"
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
                            Purchase Invoice #{{
                                purchase.invoice_number || purchase.id
                            }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Purchase Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 ml-5">
                    <Link
                        :href="route('purchases.edit', purchase.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Purchase
                    </Link>
                    <button
                        @click="printInvoice"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5 4V2a2 2 0 012-2h6a2 2 0 012 2v2h2a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h2zm0 2h10v7H5V6zm6 10a1 1 0 100 2h2a1 1 0 100-2h-2z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Print Invoice
                    </button>
                    <button
                        @click="deletePurchase"
                        class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Delete
                    </button>
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
                <!-- Purchase Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4"
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
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                            Purchase Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-amber-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Invoice Number
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ purchase.invoice_number || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-orange-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Purchase Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(purchase.purchased_at) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-amber-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Supplier
                                </dt>
                                <dd class="mt-1">
                                    <Link
                                        v-if="purchase.supplier"
                                        :href="route('suppliers.show', purchase.supplier.id)"
                                        class="text-lg font-medium text-amber-600 hover:text-amber-700 hover:underline"
                                    >
                                        {{ purchase.supplier.name }}
                                    </Link>
                                    <span
                                        v-else
                                        class="text-lg font-medium text-gray-400"
                                        >No supplier specified</span
                                    >
                                </dd>
                            </div>
                            <div class="border-l-4 border-orange-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Total Amount
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-amber-600"
                                >
                                    {{ money(purchase.total_amount) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-amber-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Discount
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <span
                                        v-if="
                                            purchase.discount_type === 'Percent'
                                        "
                                    >
                                        {{ purchase.discount }}%
                                        <span
                                            v-if="calculatedDiscountAmount > 0"
                                        >
                                            ({{
                                                money(calculatedDiscountAmount)
                                            }})
                                        </span>
                                    </span>
                                    <span v-else>
                                        {{ money(purchase.discount) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-orange-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Tax
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <span v-if="purchase.tax_percentage > 0">
                                        {{ purchase.tax_percentage }}%
                                        <span v-if="purchase.tax > 0">
                                            ({{ money(purchase.tax) }})
                                        </span>
                                    </span>
                                    <span v-else>
                                        {{ money(purchase.tax) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Purchase Items -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4"
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
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                />
                            </svg>
                            Items Purchased
                        </h3>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="purchase.purchase_items.length"
                            class="overflow-x-auto"
                        >
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Item Name
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Type
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Quantity
                                        </th>
                                        <th
                                            v-if="
                                                purchase.purchase_items.some(
                                                    (item) =>
                                                        item.item_type ===
                                                        'medicine_item',
                                                )
                                            "
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Batch No.
                                        </th>
                                        <th
                                            v-if="
                                                purchase.purchase_items.some(
                                                    (item) =>
                                                        item.item_type ===
                                                        'medicine_item',
                                                )
                                            "
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Expiry Date
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Unit Price
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Sub Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="item in purchase.purchase_items"
                                        :key="item.id"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        >
                                            <Link
                                                v-if="
                                                    item.item_type ===
                                                    'inventory_item'
                                                "
                                                :href="route('inventory.show', item.item_id)"
                                                class="text-amber-600 hover:text-amber-700 hover:underline"
                                            >
                                                {{ item.item?.name }}
                                            </Link>
                                            <Link
                                                v-else-if="
                                                    item.item_type ===
                                                    'medicine_item'
                                                "
                                                :href="route('medicines.show', item.item_id)"
                                                class="text-amber-600 hover:text-amber-700 hover:underline"
                                            >
                                                {{ item.item?.name }}
                                            </Link>
                                            <span v-else>{{
                                                item.item?.name || "N/A"
                                            }}</span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                item.item_type ===
                                                "inventory_item"
                                                    ? "Inventory"
                                                    : "Medicine"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ item.quantity }}
                                        </td>
                                        <td
                                            v-if="
                                                purchase.purchase_items.some(
                                                    (item) =>
                                                        item.item_type ===
                                                        'medicine_item',
                                                )
                                            "
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ item.batch_no || "—" }}
                                        </td>
                                        <td
                                            v-if="
                                                purchase.purchase_items.some(
                                                    (item) =>
                                                        item.item_type ===
                                                        'medicine_item',
                                                )
                                            "
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                item.expiry_date
                                                    ? formatDate(
                                                          item.expiry_date,
                                                      )
                                                    : "—"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ money(item.unit_price) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ money(item.sub_total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-else class="text-gray-500">No items purchased.</p>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4"
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
                        <div class="border-l-4 border-amber-400 pl-4">
                            <dt
                                class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                            >
                                Notes
                            </dt>
                            <dd class="mt-2 text-gray-700 whitespace-pre-wrap">
                                {{ purchase.notes || "No notes available" }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Purchase Summary Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Purchase Summary
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border-2 border-amber-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total Amount</span
                            >
                            <span
                                class="px-4 py-2 rounded-full text-sm font-bold bg-amber-100 text-amber-800"
                            >
                                {{ money(purchase.total_amount) }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border-2 border-orange-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Purchase Date</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                {{ formatDate(purchase.purchased_at) }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border-2 border-amber-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Discount</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                <span
                                    v-if="purchase.discount_type === 'Percent'"
                                >
                                    {{ purchase.discount }}%
                                    <span v-if="calculatedDiscountAmount > 0">
                                        ({{ money(calculatedDiscountAmount) }})
                                    </span>
                                </span>
                                <span v-else>
                                    {{ money(purchase.discount) }}
                                </span>
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border-2 border-orange-300"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Tax</span
                            >
                            <span class="text-sm font-bold text-gray-900">
                                <span v-if="purchase.tax_percentage > 0">
                                    {{ purchase.tax_percentage }}%
                                    <span v-if="purchase.tax > 0">
                                        ({{ money(purchase.tax) }})
                                    </span>
                                </span>
                                <span v-else>
                                    {{ money(purchase.tax) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div
                    class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-lg p-6 border border-amber-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Info
                    </h3>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Invoice Number</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                purchase.invoice_number || "—"
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Supplier</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                purchase.supplier?.name || "—"
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Total Items</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                purchase.purchase_items.length
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg shadow-lg p-6 border border-amber-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('purchases.edit', purchase.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-amber-100 rounded-lg transition border border-amber-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-amber-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Purchase</span
                            >
                        </Link>
                        <button
                            @click="deletePurchase"
                            class="w-full flex items-center gap-3 p-3 bg-white hover:bg-amber-100 rounded-lg transition border border-amber-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-amber-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Delete Purchase</span
                            >
                        </button>
                        <Link
                            :href="route('purchases.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-amber-100 rounded-lg transition border border-amber-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-amber-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Purchases</span
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
                                formatDateTime(purchase.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(purchase.updated_at)
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
import { computed } from "vue";
import { useMoneyFormatter } from "@/Utils/money";

const props = defineProps({
    purchase: Object,
});

const calculatedDiscountAmount = computed(() => {
    if (props.purchase.discount_type === "Percent") {
        const subTotalSum = props.purchase.purchase_items.reduce(
            (sum, item) => sum + item.sub_total,
            0,
        );
        return subTotalSum * (props.purchase.discount / 100);
    }
    return props.purchase.discount;
});

const { money } = useMoneyFormatter();

// Removed formatNumber as money will be used
// const formatNumber = (value) => {
//     if (value === null || value === undefined) return "0.00";
//     return parseFloat(value).toLocaleString("en-US", {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2,
//     });
// };

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

const deletePurchase = () => {
    if (
        confirm(
            "Are you sure you want to delete this purchase? This action cannot be undone.",
        )
    ) {
        useForm({}).delete(`/purchases/${props.purchase.id}`);
    }
};

const printInvoice = () => {
    window.open(route("purchases.print", props.purchase.id), "_blank");
};
</script>
