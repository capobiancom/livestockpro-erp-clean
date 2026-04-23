<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800"> {{ $t('add_new_herd') }} </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Farm <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.farm_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="[{ 'border-red-500': form.errors.farm_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                            required
                        >
                            <option value="">Select Farm</option>
                            <option v-for="farm in farms" :key="farm.id" :value="farm.id">
                                {{ farm.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.farm_id" class="text-red-500 text-sm mt-1">
                            {{ form.errors.farm_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="e.g., North Pasture, Dairy Herd A"
                            required
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Code
                        </label>
                        <input
                            v-model="form.code"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.code }"
                            placeholder="e.g., NP01, DHA"
                        />
                        <p v-if="form.errors.code" class="text-red-500 text-sm mt-1">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.description }"
                            placeholder="Herd description and notes..."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Herd</span>
                    </button>
                    <Link
                        :href="route('herds.index')"
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

defineProps({
    farms: Array,
});

const form = useForm({
    farm_id: "",
    name: "",
    code: "",
    description: "",
});

function submit() {
    form.post("/herds");
}
</script>
