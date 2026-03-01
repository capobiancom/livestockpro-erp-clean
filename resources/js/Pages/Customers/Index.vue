<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Customer Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your customer base and their details
                    </p>
                </div>
                <Link
                    :href="route('customers.create')"
                    class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                    Add Customer
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
                                placeholder="Search by customer name, email or phone..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <select
                        v-model="selectedType"
                        @change="handleSearch"
                        class="border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option :value="null">All Types</option>
                        <option
                            v-for="type in customerTypes"
                            :key="type"
                            :value="type"
                        >
                            {{ type }}
                        </option>
                    </select>
                    <button
                        v-if="search || selectedType || selectedFarm"
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
                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-5 border border-green-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-semibold">
                            Total Customers
                        </p>
                        <p class="text-3xl font-bold text-green-700 mt-1">
                            {{ statistics.total_customers }}
                        </p>
                    </div>
                    <div class="bg-green-500 rounded-full p-3">
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
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a3 3 0 015.356-1.857M17 20v-2c0-.653-.114-1.28-.327-1.857M12 11V9m0 0V7m0 2h2m-2 0H8m0 0c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-md p-5 border border-blue-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-semibold">
                            Individual Customers
                        </p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">
                            {{ statistics.individual_customers }}
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-md p-5 border border-purple-200"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-semibold">
                            Business Customers
                        </p>
                        <p class="text-3xl font-bold text-purple-700 mt-1">
                            {{ statistics.business_customers }}
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
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 0h-1m1 4h1m-1 0h-1m-1 4h1m-1 0h-1m4-4h1m-1 0h-1m-1 4h1m-1 0h-1m4-4h1m-1 0h-1m-1 4h1m-1 0h-1m4-4h1m-1 0h-1m-1 4h1m-1 0h-1"
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
                            Customers with Farms
                        </p>
                        <p class="text-3xl font-bold text-amber-700 mt-1">
                            {{ statistics.customers_with_farms }}
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
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-gradient-to-r from-green-500 to-emerald-500 text-white"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Name
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Phone
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Email
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Type
                            </th>
                            <th
                                class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider"
                            >
                                Farm
                            </th>
                            <th
                                class="px-6 py-4 text-center font-semibold text-sm uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="customer in customers.data"
                            :key="customer.id"
                            class="hover:bg-green-50 transition duration-150"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium text-gray-900"
                            >
                                {{ customer.name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ customer.phone }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ customer.email }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                    :class="{
                                        'bg-blue-100 text-blue-800':
                                            customer.type === 'Individual',
                                        'bg-purple-100 text-purple-800':
                                            customer.type === 'Business',
                                    }"
                                >
                                    {{ customer.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ customer.farm?.name || "N/A" }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="
                                            route('customers.show', customer.id)
                                        "
                                        class="text-blue-600 hover:text-blue-800 font-semibold transition"
                                        title="View Details"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                            />
                                            <path
                                                fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="
                                            route('customers.edit', customer.id)
                                        "
                                        class="text-green-600 hover:text-green-800 font-semibold transition"
                                        title="Edit Customer"
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
                                    </Link>
                                    <button
                                        @click="confirmDelete(customer)"
                                        class="text-red-600 hover:text-red-800 font-semibold transition"
                                        title="Delete Customer"
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
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!customers.data.length">
                            <td colspan="6" class="px-6 py-12 text-center">
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
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                    <p
                                        class="text-gray-500 text-lg font-medium"
                                    >
                                        No customers found
                                    </p>
                                    <p class="text-gray-400 text-sm mt-1">
                                        Click "Add Customer" to record your
                                        first customer
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="customers.links.length > 3"
                class="bg-gray-50 px-6 py-4 border-t border-gray-200"
            >
                <div class="flex justify-center gap-1">
                    <Link
                        v-for="(link, index) in customers.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                            link.active
                                ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow'
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
                    Are you sure you want to delete the customer
                    <span class="font-semibold">{{
                        customerToDelete?.name
                    }}</span
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
                        @click="deleteCustomer"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Layout from "../Layout/AppLayout.vue";
import { debounce } from "lodash";

const props = defineProps({
    customers: Object,
    statistics: Object, // Added for statistics cards
    filters: Object,
    customerTypes: Array,
    farms: Array,
});

const search = ref(props.filters?.search || "");
const selectedType = ref(props.filters?.type || null);
const selectedFarm = ref(props.filters?.farm_id || null);
const showDeleteModal = ref(false);
const customerToDelete = ref(null);

const handleSearch = debounce(() => {
    Inertia.get(
        route("customers.index"),
        {
            search: search.value,
            type: selectedType.value,
            farm_id: selectedFarm.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

const clearSearch = () => {
    search.value = "";
    selectedType.value = null;
    selectedFarm.value = null;
    Inertia.get(
        route("customers.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const confirmDelete = (customer) => {
    customerToDelete.value = customer;
    showDeleteModal.value = true;
};

const deleteCustomer = () => {
    Inertia.delete(route("customers.destroy", customerToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            customerToDelete.value = null;
        },
    });
};
</script>
