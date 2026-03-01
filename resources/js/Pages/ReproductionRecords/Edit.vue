<template>
    <Head title="Edit Reproduction Record" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">
                Edit Reproduction Record
            </h2>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Animal Information Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >1</span
                        >
                        Animal Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Select the animal and breeding partner
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Animal <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.animal_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.animal_id,
                                }"
                                required
                            >
                                <option value="">Select Animal</option>
                                <option
                                    v-for="a in animals.filter(
                                        (animal) => animal.sex === 'female',
                                    )"
                                    :key="a.id"
                                    :value="a.id"
                                >
                                    {{ a.tag }} - {{ a.name }} ({{ a.sex }})
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
                                Partner
                            </label>
                            <select
                                v-model="form.partner_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.partner_id,
                                }"
                            >
                                <option value="">Select Partner</option>
                                <option
                                    v-for="a in animals.filter(
                                        (animal) => animal.sex === 'male',
                                    )"
                                    :key="a.id"
                                    :value="a.id"
                                >
                                    {{ a.tag }} - {{ a.name }} ({{ a.sex }})
                                </option>
                            </select>
                            <p
                                v-if="form.errors.partner_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.partner_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Event Details Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >2</span
                        >
                        Event Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the reproduction event information
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Event <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="event"
                                v-model="form.event"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.event,
                                }"
                                required
                            >
                                <option value="natural">Natural</option>
                                <option value="artificial_insemination">
                                    Artificial Insemination
                                </option>
                            </select>

                            <p
                                v-if="form.errors.event"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.event }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Event Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.event_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.event_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.event_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.event_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Heat Stage
                            </label>
                            <select
                                v-model="form.heat_stage"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.heat_stage,
                                }"
                            >
                                <option value="">Select Heat Stage</option>
                                <option value="early">Early</option>
                                <option value="optimal">Optimal</option>
                                <option value="late">Late</option>
                            </select>
                            <p
                                v-if="form.errors.heat_stage"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.heat_stage }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Performed By
                            </label>
                            <select
                                v-model="form.performed_by"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                :class="{
                                    'border-red-500': form.errors.performed_by,
                                }"
                            >
                                <option value="">Select User</option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.performed_by"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.performed_by }}
                            </p>
                        </div>
                    </div>

                    <!-- Artificial Insemination Fields (conditionally rendered) -->
                    <div
                        v-if="form.event === 'artificial_insemination'"
                        class="mt-8 pt-8 border-t border-gray-200"
                    >
                        <h3
                            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                        >
                            <span
                                class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                                style="
                                    background: linear-gradient(
                                        to right,
                                        #ec4899,
                                        #f43f5e
                                    );
                                "
                                >3</span
                            >
                            Artificial Insemination Details
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Enter the artificial insemination information
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Semen Batch No.
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.semen_batch_no"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors.semen_batch_no,
                                    }"
                                    placeholder="e.g., ABC-12345"
                                    :required="
                                        form.event === 'artificial_insemination'
                                    "
                                />
                                <p
                                    v-if="form.errors.semen_batch_no"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.semen_batch_no }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Breed <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.breed_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                    :class="{
                                        'border-red-500': form.errors.breed_id,
                                    }"
                                    required
                                >
                                    <option value="">Select Breed</option>
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
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.breed_id }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Semen Company
                                </label>
                                <input
                                    v-model="form.semen_company"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors.semen_company,
                                    }"
                                    placeholder="e.g., ABS Global"
                                />
                                <p
                                    v-if="form.errors.semen_company"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.semen_company }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Insemination Date
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.insemination_date"
                                    type="date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500':
                                            form.errors.insemination_date,
                                    }"
                                    :required="
                                        form.event === 'artificial_insemination'
                                    "
                                />
                                <p
                                    v-if="form.errors.insemination_date"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.insemination_date }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Vet
                                </label>
                                <select
                                    v-model="form.vet_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                    :class="{
                                        'border-red-500': form.errors.vet_id,
                                    }"
                                >
                                    <option value="">Select Vet</option>
                                    <option
                                        v-for="vet in vets"
                                        :key="vet.id"
                                        :value="vet.id"
                                    >
                                        {{ vet.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.vet_id"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.vet_id }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
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
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                        :class="{
                                            'border-red-500': form.errors.cost,
                                        }"
                                        placeholder="e.g., 50.00"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.cost"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.cost }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outcome Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >3</span
                        >
                        Outcome
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Record the outcome of the reproduction event
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Outcome
                            </label>
                            <select
                                v-model="form.outcome"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{
                                    'border-red-500': form.errors.outcome,
                                }"
                            >
                                <option value="">Select Outcome</option>
                                <option value="successful">Successful</option>
                                <option value="failed">Failed</option>
                                <option value="pending">Pending</option>
                            </select>
                            <p
                                v-if="form.errors.outcome"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.outcome }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-8 pt-8 border-t border-gray-200">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            style="
                                background: linear-gradient(
                                    to right,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                            >4</span
                        >
                        Notes
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Additional observations or comments
                    </p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Additional Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.notes }"
                                placeholder="Any additional notes or observations..."
                            ></textarea>
                            <p
                                v-if="form.errors.notes"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('reproduction-records.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        style="
                            background: linear-gradient(
                                to right,
                                #ec4899,
                                #f43f5e
                            );
                        "
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
                        {{
                            form.processing
                                ? "Saving..."
                                : "Update Reproduction Record"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { useForm, Link, Head } from "@inertiajs/inertia-vue3";
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    record: Object,
    animals: Array,
    users: Array, // Add users prop
    vets: Array, // Add vets prop
    breeds: Array, // Add breeds prop
    errors: Object,
});

const form = useForm({
    animal_id: props.record.animal_id,
    partner_id: props.record.partner_id || "",
    event: props.record.event,
    event_date: props.record.event_date
        ? new Date(props.record.event_date).toISOString().split("T")[0]
        : "",
    outcome: props.record.outcome || "",
    notes: props.record.notes || "",
    heat_stage: props.record.heat_stage || "",
    performed_by: props.record.performed_by || "",
    // Artificial Insemination fields
    semen_batch_no: props.record.artificial_insemination?.semen_batch_no || "",
    breed_id: props.record.artificial_insemination?.breed.id || "",
    semen_company: props.record.artificial_insemination?.semen_company || "",
    insemination_date: props.record.artificial_insemination?.insemination_date
        ? new Date(props.record.artificial_insemination.insemination_date)
              .toISOString()
              .split("T")[0]
        : "",
    vet_id: props.record.artificial_insemination?.vet_id || "",
    cost: props.record.artificial_insemination?.cost || 0,
    remarks: props.record.artificial_insemination?.remarks || "",
});

function submit() {
    form.put(`/reproduction-records/${props.record.id}`);
}
</script>
