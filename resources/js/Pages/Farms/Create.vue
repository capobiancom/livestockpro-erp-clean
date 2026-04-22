<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Add New Farm</h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Farm Name *
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Enter farm name"
                            required
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Farm Code
                        </label>
                        <input
                            v-model="form.code"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="e.g., FARM001"
                        />
                        <p v-if="form.errors.code" class="text-red-500 text-sm mt-1">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Address
                        </label>
                        <input
                            v-model="form.address"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Enter farm address"
                        />
                        <p
                            v-if="form.errors.address"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Contact Name
                        </label>
                        <input
                            v-model="form.contact_name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Enter contact person name"
                        />
                        <p
                            v-if="form.errors.contact_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.contact_name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Contact Phone
                        </label>
                        <input
                            v-model="form.contact_phone"
                            type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Enter contact phone number"
                        />
                        <p
                            v-if="form.errors.contact_phone"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.contact_phone }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Farm</span>
                    </button>
                    <Link
                        :href="route('farms.index')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200"
                    >
                        Cancel
                    </Link>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const form = useForm({
    name: "",
    code: "",
    address: "",
    contact_name: "",
    contact_phone: "",
});

function submit() {
    form.post("/farms");
}
</script>
