<template>
    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Edit Logistics Record
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Update logistics transportation record
            </p>
        </template>

        <div class="max-w-4xl mx-auto">
            <form
                @submit.prevent="submit"
                class="bg-white rounded-lg shadow-md p-6"
            >
                <!-- Section 1: Basic Information -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200"
                    >
                        1. Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Reference Number
                            </label>
                            <input
                                v-model="form.reference"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Optional reference number"
                            />
                            <p
                                v-if="form.errors.reference"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.reference }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.purpose"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="e.g., Transport to market"
                                required
                            />
                            <p
                                v-if="form.errors.purpose"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.purpose }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Route Details -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200"
                    >
                        2. Route Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                From Location
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.from_location"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Starting location"
                                required
                            />
                            <p
                                v-if="form.errors.from_location"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.from_location }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                To Location <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.to_location"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Destination location"
                                required
                            />
                            <p
                                v-if="form.errors.to_location"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.to_location }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Transport Information -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200"
                    >
                        3. Transport Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Vehicle
                            </label>
                            <input
                                v-model="form.vehicle"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Vehicle information"
                            />
                            <p
                                v-if="form.errors.vehicle"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.vehicle }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Driver
                            </label>
                            <input
                                v-model="form.driver"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Driver name"
                            />
                            <p
                                v-if="form.errors.driver"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.driver }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Animals Count
                            </label>
                            <input
                                v-model="form.animals_count"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Number of animals"
                            />
                            <p
                                v-if="form.errors.animals_count"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.animals_count }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Cost
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
                                    v-model="form.cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    placeholder="0.00"
                                />
                            </div>
                            <p
                                v-if="form.errors.cost"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.cost }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Schedule -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200"
                    >
                        4. Schedule
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Departure Date & Time
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.departure_at"
                                type="datetime-local"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                required
                            />
                            <p
                                v-if="form.errors.departure_at"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.departure_at }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Arrival Date & Time
                            </label>
                            <input
                                v-model="form.arrival_at"
                                type="datetime-local"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            />
                            <p
                                v-if="form.errors.arrival_at"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.arrival_at }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Additional Information -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200"
                    >
                        5. Additional Information
                    </h3>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Additional notes or comments"
                        ></textarea>
                        <p
                            v-if="form.errors.notes"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex justify-end gap-3 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('logistics.index')"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-6 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? "Updating..."
                                : "Update Logistics Record"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    logistic: Object,
});

const form = useForm({
    reference: props.logistic.reference || "",
    vehicle: props.logistic.vehicle || "",
    driver: props.logistic.driver || "",
    purpose: props.logistic.purpose || "",
    from_location: props.logistic.from_location || "",
    to_location: props.logistic.to_location || "",
    departure_at: props.logistic.departure_at
        ? props.logistic.departure_at.slice(0, 16)
        : "",
    arrival_at: props.logistic.arrival_at
        ? props.logistic.arrival_at.slice(0, 16)
        : "",
    animals_count: props.logistic.animals_count,
    cost: props.logistic.cost,
    notes: props.logistic.notes || "",
});

const submit = () => {
    form.put(route("logistics.update", props.logistic.id));
};
</script>
