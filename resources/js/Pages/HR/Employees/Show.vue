<template>
    <Head
        :title="`Employee Details: ${employee.first_name} ${employee.last_name}`"
    />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('employees.index')"
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
                            {{ employee.first_name }} {{ employee.last_name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Employee Details
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('employees.edit', employee.id)"
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white ml-5 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
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
                        Edit Employee
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
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4"
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
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Employee Code
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ employee.employee_code }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Full Name
                                </dt>
                                <dd
                                    class="mt-1 text-2xl font-bold text-gray-900"
                                >
                                    {{ employee.first_name }}
                                    {{ employee.last_name }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Gender
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getGenderBadgeClass(employee.gender)
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ employee.gender }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Date of Birth
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(employee.date_of_birth) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Phone
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ employee.phone || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Email
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ employee.email || "—" }}
                                </dd>
                            </div>
                            <div
                                class="col-span-2 border-l-4 border-indigo-400 pl-4"
                            >
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Address
                                </dt>
                                <dd
                                    class="mt-2 text-gray-700 whitespace-pre-wrap"
                                >
                                    {{
                                        employee.address ||
                                        "No address available"
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4"
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
                                    d="M21 13.255A23.55 23.55 0 0112 15c-1.632 0-3.23-.26-4.745-.745L3 15V4a1 1 0 011-1h16a1 1 0 011 1v9.255z"
                                />
                            </svg>
                            Employment Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Join Date
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ formatDate(employee.join_date) }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Employment Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getEmploymentTypeBadgeClass(
                                                employee.employment_type,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ employee.employment_type }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Salary Type
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getSalaryTypeBadgeClass(
                                                employee.salary_type,
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ employee.salary_type }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="
                                            getStatusBadgeClass(employee.status)
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                    >
                                        {{ employee.status }}
                                    </span>
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Department
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ employee.department?.name || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-blue-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    Designation
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ employee.designation?.name || "—" }}
                                </dd>
                            </div>
                            <div class="border-l-4 border-indigo-400 pl-4">
                                <dt
                                    class="text-sm font-semibold text-gray-500 uppercase tracking-wide"
                                >
                                    User Email
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ employee.user_email || "—" }}
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
                            class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Current Status</span
                            >
                            <span
                                :class="getStatusBadgeClass(employee.status)"
                                class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                            >
                                {{ employee.status }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-blue-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Years of Service</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                calculateYearsOfService(employee.join_date)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-lg p-6 border border-indigo-200"
                >
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link
                            :href="route('employees.edit', employee.id)"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-indigo-100 rounded-lg transition border border-indigo-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-indigo-600"
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
                            :href="route('employees.index')"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-indigo-100 rounded-lg transition border border-indigo-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-indigo-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >Back to Employees</span
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
                                formatDateTime(employee.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(employee.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    employee: Object,
});

const getGenderBadgeClass = (gender) => {
    const classes = {
        male: "bg-blue-100 text-blue-800",
        female: "bg-pink-100 text-pink-800",
        other: "bg-gray-100 text-gray-800",
    };
    return classes[gender] || "bg-gray-100 text-gray-800";
};

const getEmploymentTypeBadgeClass = (type) => {
    const classes = {
        full_time: "bg-green-100 text-green-800",
        part_time: "bg-yellow-100 text-yellow-800",
        contract: "bg-purple-100 text-purple-800",
        internship: "bg-indigo-100 text-indigo-800",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const getSalaryTypeBadgeClass = (type) => {
    const classes = {
        monthly: "bg-blue-100 text-blue-800",
        hourly: "bg-orange-100 text-orange-800",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const getStatusBadgeClass = (status) => {
    const classes = {
        active: "bg-green-100 text-green-800",
        inactive: "bg-yellow-100 text-yellow-800",
        terminated: "bg-red-100 text-red-800",
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

const calculateYearsOfService = (joinDate) => {
    if (!joinDate) return "Unknown";
    const join = new Date(joinDate);
    const today = new Date();
    const diffTime = Math.abs(today - join);
    const diffYears = Math.floor(diffTime / (1000 * 60 * 60 * 24 * 365.25));

    if (diffYears === 0) {
        const diffMonths = Math.floor(diffTime / (1000 * 60 * 60 * 24 * 30.44));
        return diffMonths > 0 ? `${diffMonths} months` : "Less than a month";
    }
    return diffYears === 1 ? "1 year" : `${diffYears} years`;
};
</script>
