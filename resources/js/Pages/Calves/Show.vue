<template>
    <Head :title="`Calf: ${calf.tag_number}`" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('calves.index')"
                        class="text-gray-600 hover:text-gray-800"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ calf.tag_number }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1"> {{ $t('calf_details') }} </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('calves.edit', calf.id)"
                        class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                            />
                        </svg>
                        Edit Calf
                    </Link>

                    <button
                        @click="confirmDelete(calf)"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Delete Calf
                    </button>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-green-700 font-medium">
                    {{ $page.props.flash.success }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4"
                    >
                        <h3
                            class="text-xl font-bold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Basic Information
                        </h3>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Tag Number
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ calf.tag_number }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Gender
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize bg-gray-100 text-gray-800"
                                    >
                                        {{ calf.gender || "—" }}
                                    </span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Birth Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(calf.birth_date) }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Birth Weight
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        calf.birth_weight
                                            ? calf.birth_weight + " kg"
                                            : "—"
                                    }}
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Health Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getHealthBadgeClass(
                                                calf.health_status,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ calf.health_status || "—" }}
                                    </span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Mother
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <Link
                                        v-if="calf.mother"
                                        :href="
                                            route(
                                                'animals.show',
                                                calf.mother.id,
                                            )
                                        "
                                        class="text-pink-600 hover:text-pink-900 font-semibold"
                                    >
                                        {{ calf.mother.tag }}
                                        <span class="text-gray-600 font-normal"
                                            >({{ calf.mother.name }})</span
                                        >
                                    </Link>
                                    <span v-else>—</span>
                                </dd>
                            </div>

                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Father
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <Link
                                        v-if="calf.father"
                                        :href="
                                            route(
                                                'animals.show',
                                                calf.father.id,
                                            )
                                        "
                                        class="text-pink-600 hover:text-pink-900 font-semibold"
                                    >
                                        {{ calf.father.tag }}
                                        <span class="text-gray-600 font-normal"
                                            >({{ calf.father.name }})</span
                                        >
                                    </Link>
                                    <span v-else>—</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Status -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Status
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Health</span
                            >
                            <span
                                :class="getHealthBadgeClass(calf.health_status)"
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ calf.health_status || "—" }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Created</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                formatDateTime(calf.created_at)
                            }}</span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Last Updated</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                formatDateTime(calf.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div
                    class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg shadow-lg p-6 border border-pink-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('calves.edit', calf.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-pink-100 rounded-lg transition border border-pink-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-pink-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Record</span
                            >
                        </Link>

                        <Link
                            :href="route('calves.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-pink-100 rounded-lg transition border border-pink-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-pink-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Calves</span
                            >
                        </Link>

                        <button
                            @click="confirmDelete(calf)"
                            class="w-full flex items-center gap-3 p-3 bg-white hover:bg-red-50 rounded-lg transition border border-red-200 text-left"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-red-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Delete Record</span
                            >
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Confirm Deletion
                    </h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete this calf record? This
                    action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteCalf"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Calf
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    calf: Object,
});

const showDeleteModal = ref(false);
const calfToDelete = ref(null);

const getHealthBadgeClass = (healthStatus) => {
    const classes = {
        healthy: "bg-green-100 text-green-800",
        weak: "bg-yellow-100 text-yellow-800",
        critical: "bg-red-100 text-red-800",
    };
    return classes[healthStatus] || "bg-gray-100 text-gray-800";
};

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};

const formatDateTime = (datetime) => {
    if (!datetime) return "—";
    return new Date(datetime).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const confirmDelete = (calf) => {
    calfToDelete.value = calf;
    showDeleteModal.value = true;
};

const deleteCalf = () => {
    Inertia.delete(route("calves.destroy", calfToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            calfToDelete.value = null;
            Inertia.visit(route("calves.index"));
        },
    });
};
</script>
