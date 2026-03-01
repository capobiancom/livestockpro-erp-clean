<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('payroll-runs.show', payrollItem.payroll_run_id)"
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
                            Payroll Item for
                            {{ payrollItem.employee?.first_name }}
                            {{ payrollItem.employee?.last_name }} ({{
                                getMonthName(payrollItem.payroll_run?.month)
                            }}
                            {{ payrollItem.payroll_run?.year }})
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Payroll Item Details
                        </p>
                    </div>
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
                <!-- Payroll Item Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4 border-b border-gray-200"
                    >
                        <h3
                            class="text-xl font-semibold text-white flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-white"
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
                            Payroll Item Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Payroll Run</span
                                >
                                <span class="text-xl font-bold text-gray-900">
                                    {{
                                        getMonthName(
                                            payrollItem.payroll_run?.month,
                                        )
                                    }}
                                    {{ payrollItem.payroll_run?.year }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Employee</span
                                >
                                <span class="text-xl font-bold text-gray-900">
                                    {{ payrollItem.employee?.first_name }}
                                    {{ payrollItem.employee?.last_name }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Basic Salary</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.basic_salary }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >House Allowance</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.house_allowance }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Medical Allowance</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.medical_allowance }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Transport Allowance</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.transport_allowance }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Bonus</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.bonus }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Festival Bonus</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.festival_bonus }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Performance Incentive</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.performance_incentive }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Overtime Hours</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.overtime_hours }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Overtime Rate</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.overtime_rate }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Overtime Amount</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.overtime_amount }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Gross Salary</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.gross_salary }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Leave Deductions</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.leave_deductions ?? 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Loan Deductions</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.loan_deductions ?? 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Tax Amount</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.tax_amount ?? 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Other Deductions</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.other_deductions ?? 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Total Deductions</span
                                >
                                <span
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ payrollItem.deductions }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600"
                                    >Net Salary</span
                                >
                                <span class="text-xl font-bold text-gray-900">
                                    {{ payrollItem.net_salary }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Quick Info
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Employee</span
                            >
                            <span class="text-sm font-bold text-gray-900"
                                >{{ payrollItem.employee?.first_name }}
                                {{ payrollItem.employee?.last_name }}</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-pink-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Payroll Run</span
                            >
                            <span class="text-sm font-bold text-gray-900"
                                >{{
                                    getMonthName(payrollItem.payroll_run?.month)
                                }}
                                {{ payrollItem.payroll_run?.year }}</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Working Days</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                payrollItem.working_days
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Paid Leave Days</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                payrollItem.paid_leave_days
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Un Paid Leave Days</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                payrollItem.unpaid_leave_days
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-rose-50 rounded-lg"
                        >
                            <span class="text-sm font-semibold text-gray-700"
                                >Net Salary</span
                            >
                            <span class="text-sm font-bold text-gray-900">{{
                                payrollItem.net_salary
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
                            :href="route('payroll-runs.show', payrollItem.payroll_run_id)"
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
                                >Back to Payroll Run</span
                            >
                        </Link>
                        <button
                            @click="showPayslipModal = true"
                            class="flex items-center gap-3 p-3 bg-white hover:bg-rose-100 rounded-lg transition border border-rose-200 w-full text-left"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 4V2a2 2 0 012-2h6a2 2 0 012 2v2h2a2 2 0 012 2v7a2 2 0 01-2 2h-2v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4H3a2 2 0 01-2-2V6a2 2 0 012-2h2zm0 2H3v7h2V6zm10 0h2v7h-2V6zM7 2h6v2H7V2zm0 16h6v-4H7v4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700"
                                >View Payslip</span
                            >
                        </button>
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
                                formatDateTime(payrollItem.created_at)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{
                                formatDateTime(payrollItem.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showPayslipModal" @close="showPayslipModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Payslip for
                    {{ payrollItem.employee?.first_name }}
                    {{ payrollItem.employee?.last_name }} ({{
                        getMonthName(payrollItem.payroll_run?.month)
                    }}
                    {{ payrollItem.payroll_run?.year }})
                </h2>
                <div id="payslip-modal-content">
                    <PayslipContent
                        :payrollItem="payrollItem"
                        :currency-symbol="
                            $page.props.appSettings.currency_symbol
                        "
                    />
                </div>
                <div class="mt-6 flex justify-end no-print">
                    <button
                        @click="printPayslip"
                        class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-900 focus:outline-none focus:border-rose-900 focus:ring ring-rose-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5 4V2a2 2 0 012-2h6a2 2 0 012 2v2h2a2 2 0 012 2v7a2 2 0 01-2 2h-2v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4H3a2 2 0 01-2-2V6a2 2 0 012-2h2zm0 2H3v7h2V6zm10 0h2v7h-2V6zM7 2h6v2H7V2zm0 16h6v-4H7v4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Print Payslip
                    </button>
                    <button
                        @click="showPayslipModal = false"
                        class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Close
                    </button>
                </div>
            </div>
        </Modal>
    </Layout>
</template>

<script setup>
import { Link, router } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import PayslipContent from "@/Components/PayslipContent.vue";

const props = defineProps({
    payrollItem: Object,
});

const showPayslipModal = ref(false);

const printPayslip = () => {
    const url = route("payroll-items.print-payslip", {
        payroll_item: props.payrollItem.id,
    });
    window.open(url, "_blank");
};

const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

const getMonthName = (monthNumber) => {
    return monthNames[monthNumber - 1] || "Unknown";
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
