<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('attendances.index')"
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
                            Attendance Details
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed view of an attendance record.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('attendances.edit', attendance.id)"
                        class="ml-5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Attendance
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
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4"
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
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Employee Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ attendance.employee?.first_name }}
                                    {{ attendance.employee?.last_name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Farm Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ attendance.farm?.name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(attendance.date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Check In
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        attendance.check_in
                                            ? formatTime(attendance.check_in)
                                            : "—"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Check Out
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        attendance.check_out
                                            ? formatTime(attendance.check_out)
                                            : "—"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Working Hours & Status -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4"
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
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Working Hours & Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Working Minutes
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ attendance.working_minutes || "0" }}
                                    minutes
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Overtime Minutes
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ attendance.overtime_minutes || "0" }}
                                    minutes
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getStatusBadgeClass(
                                                attendance.status,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ attendance.status }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Source
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getSourceBadgeClass(
                                                attendance.source,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ attendance.source }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
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
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Current Status</span
                            >
                            <span
                                :class="getStatusBadgeClass(attendance.status)"
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ attendance.status }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Recorded By</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                attendance.user?.name
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg shadow-lg p-6 border border-rose-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('attendances.edit', attendance.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-rose-100 rounded-lg transition border border-rose-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Edit Attendance</span
                            >
                        </Link>
                        <Link
                            :href="route('attendances.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-rose-100 rounded-lg transition border border-rose-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Attendances</span
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
                                formatDateTime(attendance.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(attendance.updated_at)
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
import Layout from "../Layout/AppLayout.vue";

const props = defineProps({
    attendance: Object,
});

const getStatusBadgeClass = (status) => {
    const classes = {
        present: "bg-green-100 text-green-800",
        absent: "bg-red-100 text-red-800",
        leave: "bg-yellow-100 text-yellow-800",
        late: "bg-orange-100 text-orange-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getSourceBadgeClass = (source) => {
    const classes = {
        manual: "bg-blue-100 text-blue-800",
        biometric: "bg-purple-100 text-purple-800",
        mobile: "bg-indigo-100 text-indigo-800",
    };
    return classes[source] || "bg-gray-100 text-gray-800";
};

const formatTime = (datetimeString) => {
    const date = new Date(datetimeString);
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

const formatDate = (datetimeString) => {
    const date = new Date(datetimeString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatDateTime = (datetimeString) => {
    if (!datetimeString) return "—";
    return new Date(datetimeString).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
