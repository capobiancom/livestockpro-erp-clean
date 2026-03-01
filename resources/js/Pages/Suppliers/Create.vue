<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Add New Supplier</h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Supplier Information Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2">
                        <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">1</span>
                        Supplier Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">Enter the supplier's basic identification details</p>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Supplier Name <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter supplier name"
                                required
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2">
                        <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">2</span>
                        Contact Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">Add contact information for this supplier</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Person <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input
                                v-model="form.contact_name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter contact person name"
                            />
                            <p v-if="form.errors.contact_name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.contact_name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter phone number"
                            />
                            <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter email address"
                            />
                            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                                {{ form.errors.email }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2">
                        <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">3</span>
                        Address
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">Add the supplier's physical address</p>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Address <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <textarea
                            v-model="form.address"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Enter supplier address..."
                        ></textarea>
                        <p v-if="form.errors.address" class="text-red-500 text-sm mt-1">
                            {{ form.errors.address }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <Link
                        :href="route('suppliers.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ form.processing ? 'Creating...' : 'Create Supplier' }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const form = useForm({
    name: "",
    contact_name: "",
    phone: "",
    email: "",
    address: "",
});

function submit() {
    form.post("/suppliers");
}
</script>
