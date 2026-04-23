<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('suppliers.index')"
                        class="text-gray-600 hover:text-gray-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ supplier.name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1"> {{ $t('supplier_details') }} </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('suppliers.edit', supplier.id)"
                        class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                    </Link>
                    <button
                        @click="deleteSupplier"
                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-green-700 font-medium">{{ $page.props.flash.success }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Supplier Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Supplier Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-6">
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Supplier Name</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ supplier.name }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Contact Person</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">{{ supplier.contact_name || '—' }}</dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Phone</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">
                                    <a v-if="supplier.phone" :href="`tel:${supplier.phone}`" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ supplier.phone }}
                                    </a>
                                    <span v-else>—</span>
                                </dd>
                            </div>
                            <div class="md:col-span-2 border-l-4 border-indigo-400 pl-4">
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Email</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">
                                    <a v-if="supplier.email" :href="`mailto:${supplier.email}`" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ supplier.email }}
                                    </a>
                                    <span v-else>—</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Address
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="border-l-4 border-indigo-400 pl-4">
                            <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Physical Address</dt>
                            <dd class="mt-2 text-gray-700 whitespace-pre-wrap">{{ supplier.address || 'No address provided' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Inventory Items Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Inventory Summary</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg border-2 border-indigo-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-600">Total Items</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ supplier.inventory_items?.length || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4">Number of inventory items supplied by this supplier</p>
                </div>

                <!-- Quick Stats Card -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-lg p-6 border border-indigo-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Info</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Contact Person</span>
                            <span class="text-sm font-bold text-gray-900">{{ supplier.contact_name ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Has Phone</span>
                            <span class="text-sm font-bold text-gray-900">{{ supplier.phone ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Has Email</span>
                            <span class="text-sm font-bold text-gray-900">{{ supplier.email ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Has Address</span>
                            <span class="text-sm font-bold text-gray-900">{{ supplier.address ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-lg p-6 border border-indigo-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('suppliers.edit', supplier.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-indigo-100 rounded-lg transition border border-indigo-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">Edit Supplier</span>
                        </Link>
                        <Link
                            :href="route('suppliers.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-indigo-100 rounded-lg transition border border-indigo-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">Back to Suppliers</span>
                        </Link>
                        <button
                            @click="deleteSupplier"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-red-100 rounded-lg transition border border-red-200 w-full"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-semibold text-red-700">Delete Supplier</span>
                        </button>
                    </div>
                </div>

                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Record Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{ formatDateTime(supplier.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{ formatDateTime(supplier.updated_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    supplier: Object
});

const formatDateTime = (datetime) => {
    if (!datetime) return '—';
    return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const deleteForm = useForm({});

const deleteSupplier = () => {
    if (confirm(`Are you sure you want to delete ${props.supplier.name}? This action cannot be undone.`)) {
        deleteForm.delete(`/suppliers/${props.supplier.id}`);
    }
};
</script>
