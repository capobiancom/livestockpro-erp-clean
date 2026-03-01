<template>
    <Head title="Edit Calf" />

    <Layout>
        <template #title>
            <h2 class="text-3xl font-bold text-gray-800">Edit Calf</h2>
            <p class="text-sm text-gray-500 mt-1">
                Update the details for this calf.
            </p>
        </template>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mother -->
                    <div>
                        <label
                            for="mother_id"
                            class="block text-sm font-medium text-gray-700"
                            >Mother (Animal)</label
                        >
                        <select
                            id="mother_id"
                            v-model="form.mother_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        >
                            <option :value="null">Select Mother</option>
                            <option
                                v-for="animal in animals"
                                :key="animal.id"
                                :value="animal.id"
                            >
                                {{ animal.tag }} ({{ animal.name }})
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.mother_id"
                            class="mt-2"
                        />
                    </div>

                    <!-- Father -->
                    <div>
                        <label
                            for="father_id"
                            class="block text-sm font-medium text-gray-700"
                            >Father (Animal - Optional)</label
                        >
                        <select
                            id="father_id"
                            v-model="form.father_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        >
                            <option :value="null">Select Father</option>
                            <option
                                v-for="animal in fanimals"
                                :key="animal.id"
                                :value="animal.id"
                            >
                                {{ animal.tag }} ({{ animal.name }})
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.father_id"
                            class="mt-2"
                        />
                    </div>

                    <!-- Gender -->
                    <div>
                        <label
                            for="gender"
                            class="block text-sm font-medium text-gray-700"
                            >Gender</label
                        >
                        <select
                            id="gender"
                            v-model="form.gender"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        >
                            <option :value="null">Select Gender</option>
                            <option
                                v-for="genderOption in calfGenders"
                                :key="genderOption"
                                :value="genderOption"
                            >
                                {{ genderOption }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.gender"
                            class="mt-2"
                        />
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label
                            for="birth_date"
                            class="block text-sm font-medium text-gray-700"
                            >Birth Date</label
                        >
                        <input
                            type="date"
                            id="birth_date"
                            v-model="form.birth_date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        />
                        <InputError
                            :message="form.errors.birth_date"
                            class="mt-2"
                        />
                    </div>

                    <!-- Birth Weight -->
                    <div>
                        <label
                            for="birth_weight"
                            class="block text-sm font-medium text-gray-700"
                            >Birth Weight (kg - Optional)</label
                        >
                        <input
                            type="number"
                            step="0.01"
                            id="birth_weight"
                            v-model="form.birth_weight"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        />
                        <InputError
                            :message="form.errors.birth_weight"
                            class="mt-2"
                        />
                    </div>

                    <!-- Health Status -->
                    <div>
                        <label
                            for="health_status"
                            class="block text-sm font-medium text-gray-700"
                            >Health Status</label
                        >
                        <select
                            id="health_status"
                            v-model="form.health_status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                        >
                            <option :value="null">Select Health Status</option>
                            <option
                                v-for="status in healthStatuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.health_status"
                            class="mt-2"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <Link
                        :href="route('calves.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                            Saving...
                        </span>
                        <span v-else>Update Calf</span>
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { useForm, Head, Link } from "@inertiajs/inertia-vue3";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    calf: Object,
    animals: Array,
    fanimals: Array,
    calfGenders: Array,
    healthStatuses: Array,
});

const form = useForm({
    _method: "put",
    tag_number: props.calf.tag_number,
    mother_id: props.calf.mother_id,
    father_id: props.calf.father_id,
    gender: props.calf.gender,
    birth_date: props.calf.birth_date
        ? new Date(props.calf.birth_date).toISOString().split("T")[0]
        : "",
    birth_weight: props.calf.birth_weight,
    health_status: props.calf.health_status,
});

const submit = () => {
    form.post(route("calves.update", props.calf.id));
};
</script>
