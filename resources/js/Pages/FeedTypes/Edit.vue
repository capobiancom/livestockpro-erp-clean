<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Edit Feed Type</h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="e.g., Alfalfa Hay, Corn Grain"
                            required
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.category }"
                            required
                        >
                            <option value="">Select Category</option>
                            <option value="grass">Grass</option>
                            <option value="grain">Grain</option>
                            <option value="hay">Hay</option>
                            <option value="silage">Silage</option>
                            <option value="supplements">Supplements</option>
                            <option value="concentrates">Concentrates</option>
                            <option value="other">Other</option>
                        </select>
                        <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Unit
                        </label>
                        <input
                            v-model="form.unit"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.unit }"
                            placeholder="kg, lbs, bales, etc."
                        />
                        <p v-if="form.errors.unit" class="text-red-500 text-sm mt-1">
                            {{ form.errors.unit }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Unit Cost
                        </label>
                        <input
                            v-model="form.unit_cost"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.unit_cost }"
                            placeholder="Cost per unit"
                        />
                        <p v-if="form.errors.unit_cost" class="text-red-500 text-sm mt-1">
                            {{ form.errors.unit_cost }}
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
                            placeholder="Feed type description..."
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
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update Feed Type</span>
                    </button>
                    <Link
                        :href="route('feed-types.index')"
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

const props = defineProps({
    feedType: Object,
});

const form = useForm({
    name: props.feedType.name || "",
    category: props.feedType.category || "",
    unit: props.feedType.unit || "",
    unit_cost: props.feedType.unit_cost || "",
    description: props.feedType.description || "",
});

function submit() {
    form.put(`/feed-types/${props.feedType.id}`);
}
</script>
