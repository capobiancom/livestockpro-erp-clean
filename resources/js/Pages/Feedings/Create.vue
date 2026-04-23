<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800"> {{ $t('record_feeding') }} </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Animal
                        </label>
                        <select
                            v-model="form.animal_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="[{ 'border-red-500': form.errors.animal_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                        >
                            <option :value="null">Select Animal</option>
                            <option
                                v-for="a in animals"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.tag }} - {{ a.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.animal_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.animal_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Group
                        </label>
                        <select
                            v-model="form.group_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="[{ 'border-red-500': form.errors.group_id }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                        >
                            <option :value="null">Select Group</option>
                            <option
                                v-for="h in herds"
                                :key="h.id"
                                :value="h.id"
                            >
                                {{ h.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.group_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.group_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Feeding Date *
                        </label>
                        <input
                            v-model="form.feeding_date"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{
                                'border-red-500': form.errors.feeding_date,
                            }"
                            required
                        />
                        <p
                            v-if="form.errors.feeding_date"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.feeding_date }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Feeding Time *
                        </label>
                        <select
                            v-model="form.feeding_time"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            :class="{
                                'border-red-500': form.errors.feeding_time,
                            }"
                            required
                        >
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                        </select>
                        <p
                            v-if="form.errors.feeding_time"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.feeding_time }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.notes }"
                            placeholder="Additional notes about this feeding..."
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Feeding Items
                    </h3>
                    <div
                        v-for="(item, index) in form.feeding_items"
                        :key="index"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 border border-gray-200 rounded-lg"
                    >
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Item *
                            </label>
                            <select
                                v-model="item.item_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                :class="{
                                    'border-red-500':
                                        form.errors[
                                            `feeding_items.${index}.item_id`
                                        ],
                                }"
                                required
                            >
                                <option :value="null">Select Item</option>
                                <option
                                    v-for="invItem in inventoryItems"
                                    :key="invItem.id"
                                    :value="invItem.id"
                                >
                                    {{ invItem.name }} ({{ invItem.quantity }}
                                    {{ invItem.unit }} available)
                                </option>
                            </select>
                            <p
                                v-if="
                                    form.errors[
                                        `feeding_items.${index}.item_id`
                                    ]
                                "
                                class="text-red-500 text-sm mt-1"
                            >
                                {{
                                    form.errors[
                                        `feeding_items.${index}.item_id`
                                    ]
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Quantity *
                            </label>
                            <input
                                v-model="item.quantity"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                :class="{
                                    'border-red-500':
                                        form.errors[
                                            `feeding_items.${index}.quantity`
                                        ],
                                }"
                                placeholder="Enter quantity"
                                required
                            />
                            <p
                                v-if="
                                    form.errors[
                                        `feeding_items.${index}.quantity`
                                    ]
                                "
                                class="text-red-500 text-sm mt-1"
                            >
                                {{
                                    form.errors[
                                        `feeding_items.${index}.quantity`
                                    ]
                                }}
                            </p>
                        </div>
                        <div class="flex items-end">
                            <button
                                type="button"
                                @click="removeFeedingItem(index)"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-200"
                                v-if="form.feeding_items.length > 1"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="addFeedingItem"
                        class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-200 mt-4"
                    >
                        Add Item
                    </button>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Feeding Record</span>
                    </button>
                    <Link
                        :href="route('feedings.index')"
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
import { computed, watch } from "vue";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    animals: Array,
    herds: Array,
    inventoryItems: Array,
});

const form = useForm({
    animal_id: null,
    group_id: null,
    feeding_date: new Date().toISOString().slice(0, 10), // Default to today's date
    feeding_time: "morning", // Default to morning
    notes: "",
    feeding_items: [
        {
            item_id: null,
            quantity: null,
        },
    ],
});

function addFeedingItem() {
    form.feeding_items.push({
        item_id: null,
        quantity: null,
    });
}

function removeFeedingItem(index) {
    form.feeding_items.splice(index, 1);
}

function submit() {
    form.post("/feedings");
}
</script>
