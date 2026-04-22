<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('vaccinations.edit', vaccination.id)"
                        class="text-gray-800 px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2"
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
                            {{
                                vaccination.animal?.name ||
                                vaccination.animal?.tag
                            }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Vaccination Record Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('vaccinations.edit', vaccination.id)"
                        class="bg-gradient-to-r from-rose-500 to-pink-500 ml-5 hover:from-rose-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Record
                    </Link>
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
            <!-- Main Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Vaccination Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                            Vaccination Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Animal
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800"
                                    >
                                        {{ vaccination.animal?.tag }} -
                                        {{ vaccination.animal?.name }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-violet-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Administered At
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        formatDate(vaccination.administered_at)
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Next Due At
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    <span
                                        :class="
                                            getDueDateClass(
                                                vaccination.next_due_at,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                    >
                                        {{
                                            formatDate(vaccination.next_due_at)
                                        }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Medication Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                            Medication Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <div
                            v-if="
                                vaccination.medications &&
                                vaccination.medications.length > 0
                            "
                        >
                            <div
                                v-for="(
                                    medication, index
                                ) in vaccination.medications"
                                :key="index"
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 border border-gray-200 rounded-lg"
                            >
                                <div>
                                    <dt
                                        class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Medicine
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-medium text-gray-900"
                                    >
                                        {{ medication.medicine?.name || "—" }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Quantity
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-medium text-gray-900"
                                    >
                                        {{ medication.quantity || "—" }}
                                        {{ medication.medicine?.unit || "" }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                    >
                                        Dose
                                    </dt>
                                    <dd
                                        class="mt-1 text-lg font-medium text-gray-900"
                                    >
                                        {{ medication.dose || "—" }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            No medications recorded for this vaccination.
                        </p>
                    </div>
                </div>

                <!-- Staff Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
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
                                    d="M17 20h2a2 2 0 002-2V7a2 2 0 00-2-2h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293L4.586 5.293A1 1 0 003.879 5H2a2 2 0 00-2 2v11a2 2 0 002 2h2"
                                />
                            </svg>
                            Staff Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-purple-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Administered By
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        vaccination.staff
                                            ? `${vaccination.staff.first_name} ${vaccination.staff.last_name}`
                                            : "—"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Notes -->
                <div
                    class="bg-white rounded-lg shadow-lg overflow-hidden"
                    v-if="vaccination.notes"
                >
                    <div
                        class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4"
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="mt-2 text-gray-700 whitespace-pre-wrap">
                            {{ vaccination.notes }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Status Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Status
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-purple-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Vaccination Status</span
                            >
                            <span
                                :class="
                                    getDueDateClass(vaccination.next_due_at)
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{
                                    getVaccinationStatus(
                                        vaccination.next_due_at,
                                    )
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-violet-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Days Until Due</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                calculateDaysUntilDue(vaccination.next_due_at)
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-purple-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Record Age</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                calculateDaysInSystem(vaccination.created_at)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg shadow-lg p-6 border border-purple-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('vaccinations.edit', vaccination.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-purple-100 rounded-lg transition border border-purple-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Information</span
                            >
                        </Link>
                        <Link
                            :href="route('vaccinations.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-purple-100 rounded-lg transition border border-purple-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Vaccinations</span
                            >
                        </Link>
                    </div>
                </div>

                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(vaccination.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(vaccination.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    vaccination: Object,
});

const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
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

const getVaccinationStatus = (dueDateString) => {
    if (!dueDateString) return "Unknown";
    const dueDate = new Date(dueDateString);
    const now = new Date();
    const daysUntilDue = Math.floor((dueDate - now) / (1000 * 60 * 60 * 24));

    if (daysUntilDue < 0) {
        return "Overdue";
    } else if (daysUntilDue <= 14) {
        return "Due Soon";
    } else {
        return "Upcoming";
    }
};

const calculateDaysUntilDue = (dueDateString) => {
    if (!dueDateString) return "Unknown";
    const dueDate = new Date(dueDateString);
    const now = new Date();
    const diffTime = dueDate - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return "Today";
    if (diffDays > 0) return `${diffDays} days`;
    return `${Math.abs(diffDays)} days overdue`;
};

const calculateDaysInSystem = (createdDate) => {
    if (!createdDate) return "Unknown";
    const created = new Date(createdDate);
    const today = new Date();
    const days = Math.floor((today - created) / (1000 * 60 * 60 * 24));

    if (days === 0) return "Today";
    if (days === 1) return "1 day";
    if (days < 30) return `${days} days`;
    if (days < 365) return `${Math.floor(days / 30)} months`;

    const years = Math.floor(days / 365);
    return years === 1 ? "1 year" : `${years} years`;
};

const getDueDateClass = (dueDateString) => {
    if (!dueDateString) return "bg-gray-100 text-gray-800";
    const dueDate = new Date(dueDateString);
    const now = new Date();
    const daysUntilDue = Math.floor((dueDate - now) / (1000 * 60 * 60 * 24));

    if (daysUntilDue < 0) {
        return "bg-red-100 text-red-800"; // Overdue
    } else if (daysUntilDue <= 14) {
        return "bg-orange-100 text-orange-800"; // Due soon
    } else {
        return "bg-green-100 text-green-800"; // Good standing
    }
};
</script>
