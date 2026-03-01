<template>
    <Layout title="Leave Request Details">
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('leave-requests.index')"
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
                            Leave Request for
                            {{ leaveRequest.employee.first_name }}
                            {{ leaveRequest.employee.last_name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed information about the leave request.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('leave-requests.edit', leaveRequest.id)"
                        class="bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Leave Request
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
                <!-- Leave Request Information -->
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
                            Request Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Employee
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ leaveRequest.employee.first_name }}
                                    {{ leaveRequest.employee.last_name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Leave Type
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ leaveRequest.leave_type?.name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Start Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(leaveRequest.start_date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    End Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(leaveRequest.end_date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-rose-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Total Days
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ leaveRequest.total_days }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getStatusBadgeClass(
                                                leaveRequest.status,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ leaveRequest.status }}
                                    </span>
                                </dd>
                            </div>
                            <div
                                class="col-span-2 border-l-4 border-rose-400 pl-4"
                            >
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Reason
                                </dt>
                                <dd
                                    class="mt-2 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        leaveRequest.reason ||
                                        "No reason provided"
                                    }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-pink-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Approved By
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{
                                        leaveRequest.approved_by
                                            ? `${leaveRequest.approved_by?.first_name} ${leaveRequest.approved_by?.last_name}`
                                            : "N/A"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Record Timestamps -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Record Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(leaveRequest.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(leaveRequest.updated_at)
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

const props = defineProps({
    leaveRequest: Object,
});

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        approved: "bg-green-100 text-green-800",
        rejected: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

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
</script>
