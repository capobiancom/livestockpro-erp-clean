<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Edit Animal
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update animal information for {{ animal.tag }}
                    </p>
                </div>
                <Link
                    :href="route('animals.show', animal.id)"
                    class="text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Back to Details
                </Link>
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
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update the animal's primary identification details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Tag / ID <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.tag"
                                type="text"
                                placeholder="e.g., A-001"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.tag }"
                            />
                            <p
                                v-if="form.errors.tag"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.tag }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Name
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Bessie"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Animal Type <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.animal_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.animal_type,
                                }"
                            >
                                <option value="">Select type...</option>
                                <option value="cow">Cow</option>
                                <option value="ox">Ox</option>
                                <option value="bull">Bull</option>
                                <option value="calf">Calf</option>
                                <option value="heifer">Heifer</option>
                                <option value="buffalo">Buffalo</option>
                                <option value="other">Other</option>
                            </select>
                            <p
                                v-if="form.errors.animal_type"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.animal_type }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Sex <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.sex"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.sex }"
                            >
                                <option value="">Select sex...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="unknown">Unknown</option>
                            </select>
                            <p
                                v-if="form.errors.sex"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.sex }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Date of Birth
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.dob"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.dob }"
                            />
                            <p
                                v-if="form.errors.dob"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.dob }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Color
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.color"
                                type="text"
                                placeholder="e.g., Brown and White"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.color }"
                            />
                            <p
                                v-if="form.errors.color"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.color }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Current Weight (kg)
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.current_weight_kg"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="e.g., 450.5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500':
                                        form.errors.current_weight_kg,
                                }"
                            />
                            <p
                                v-if="form.errors.current_weight_kg"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.current_weight_kg }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Location & Management Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Location & Management
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update farm and herd assignment
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Breed <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.breed_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.breed_id,
                                }"
                            >
                                <option value="">Select breed...</option>
                                <option
                                    v-for="breed in breeds"
                                    :key="breed.id"
                                    :value="breed.id"
                                >
                                    {{ breed.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.breed_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.breed_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Farm <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.farm_id"
                                @change="filterHerds"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.farm_id,
                                }"
                            >
                                <option value="">Select farm...</option>
                                <option
                                    v-for="farm in farms"
                                    :key="farm.id"
                                    :value="farm.id"
                                >
                                    {{ farm.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.farm_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.farm_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Herd
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.herd_id"
                                :disabled="!form.farm_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition disabled:bg-gray-100 disabled:cursor-not-allowed"
                                :class="{
                                    'border-red-500': form.errors.herd_id,
                                }"
                            >
                                <option value="">
                                    {{
                                        form.farm_id
                                            ? "Select herd..."
                                            : "Select a farm first"
                                    }}
                                </option>
                                <option
                                    v-for="herd in filteredHerds"
                                    :key="herd.id"
                                    :value="herd.id"
                                >
                                    {{ herd.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.herd_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.herd_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                            >
                                <option value="">Select status...</option>
                                <option value="active">Active</option>
                                <option value="sold">Sold</option>
                                <option value="deceased">Deceased</option>
                                <option value="transferred">Transferred</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acquisition Information Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Acquisition Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Update acquisition details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Acquired Date
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                v-model="form.acquired_at"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.acquired_at,
                                }"
                            />
                            <p
                                v-if="form.errors.acquired_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.acquired_at }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Purchase Price
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >{{
                                        $page.props.appSettings
                                            .currency_symbol || "$"
                                    }}</span
                                >
                                <input
                                    v-model="form.purchase_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="e.g., 50000.00"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                    :class="{
                                        'border-red-500':
                                            form.errors.purchase_price,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.purchase_price"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.purchase_price }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Source / Supplier
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <select
                                v-model="form.supplier_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.supplier_id,
                                }"
                            >
                                <option value="">Select supplier...</option>
                                <option
                                    v-for="supplier in suppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.supplier_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Animal Image
                                <span class="text-gray-400 text-xs"
                                    >(Optional)</span
                                >
                            </label>
                            <input
                                @change="handleImageUpload"
                                type="file"
                                accept="image/*"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100"
                                :class="{ 'border-red-500': form.errors.image }"
                            />
                            <p
                                v-if="animal.image"
                                class="mt-1 text-xs text-gray-600"
                            >
                                Current: {{ animal.image }}
                            </p>
                            <p
                                v-if="form.errors.image"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.image }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Supported formats: JPG, PNG, GIF (Max 2MB)
                            </p>
                        </div>

                        <div class="col-span-2">
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
                                placeholder="Add any additional information or observations about this animal..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.notes }"
                            ></textarea>
                            <p
                                v-if="form.errors.notes"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('animals.show', animal.id)"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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
                        {{ form.processing ? "Updating..." : "Update Animal" }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { computed } from "vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    animal: Object,
    breeds: Array,
    farms: Array,
    herds: Array,
    suppliers: Array, // Add suppliers to props
});

const today = new Date().toISOString().split("T")[0];

const form = useForm({
    tag: props.animal.tag || "",
    name: props.animal.name || "",
    animal_type: props.animal.animal_type || "",
    sex: props.animal.sex || "",
    dob: props.animal.dob
        ? new Date(props.animal.dob).toISOString().split("T")[0]
        : "",
    breed_id: props.animal.breed_id || "",
    farm_id: props.animal.farm_id || "",
    herd_id: props.animal.herd_id || "",
    status: props.animal.status || "active",
    current_weight_kg: props.animal.current_weight_kg || "",
    color: props.animal.color || "",
    acquired_at: props.animal.acquired_at
        ? new Date(props.animal.acquired_at).toISOString().split("T")[0]
        : "",
    purchase_price: props.animal.purchase_price || "",
    supplier_id: props.animal.supplier_id || "", // Changed 'source' to 'supplier_id'
    notes: props.animal.notes || "",
    image: null,
});

const filteredHerds = computed(() => {
    if (!form.farm_id || !props.herds) return [];
    return props.herds.filter((herd) => herd.farm_id == form.farm_id);
});

const filterHerds = () => {
    // Reset herd selection when farm changes if the current herd doesn't belong to the new farm
    if (form.herd_id) {
        const herdBelongsToFarm = props.herds?.some(
            (herd) => herd.id == form.herd_id && herd.farm_id == form.farm_id,
        );
        if (!herdBelongsToFarm) {
            form.herd_id = "";
        }
    }
};

const handleImageUpload = (event) => {
    form.image = event.target.files[0];
};

const submit = () => {
    form.put(`/animals/${props.animal.id}`);
};
</script>
