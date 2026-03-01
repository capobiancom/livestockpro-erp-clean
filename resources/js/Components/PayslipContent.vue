<template>
    <div class="bg-white rounded-lg shadow-lg p-8 print-area">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Payslip</h1>
                <p class="text-gray-600">
                    For the month of
                    {{ getMonthName(payrollItem.payroll_run?.month) }}
                    {{ payrollItem.payroll_run?.year }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <img
                    v-if="$page.props.app_logo_path"
                    :src="'/storage/' + $page.props.app_logo_path"
                    alt="Company Logo"
                    class="h-10"
                />
                <h2 class="text-2xl font-bold text-gray-800">AGRO SASS</h2>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-y-4 gap-x-8 mb-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">
                    Employee Details
                </h3>
                <p>
                    <strong>Name:</strong>
                    {{ payrollItem.employee?.first_name }}
                    {{ payrollItem.employee?.last_name }}
                </p>
                <p>
                    <strong>Employee ID:</strong>
                    {{ payrollItem.employee?.employee_id }}
                </p>
                <p>
                    <strong>Department:</strong>
                    {{ payrollItem.employee?.department?.name }}
                </p>
                <p>
                    <strong>Designation:</strong>
                    {{ payrollItem.employee?.designation?.name }}
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">
                    Payroll Period
                </h3>
                <p>
                    <strong>Month:</strong>
                    {{ getMonthName(payrollItem.payroll_run?.month) }}
                </p>
                <p>
                    <strong>Year:</strong>
                    {{ payrollItem.payroll_run?.year }}
                </p>
                <p>
                    <strong>Working Days:</strong>
                    {{ payrollItem.working_days }}
                </p>
                <p>
                    <strong>Paid Leave Days:</strong>
                    {{ payrollItem.paid_leave_days }}
                </p>
                <p>
                    <strong>Unpaid Leave Days:</strong>
                    {{ payrollItem.unpaid_leave_days }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Earnings
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Basic Salary:</span>
                        <span>{{
                            formatCurrency(payrollItem.basic_salary)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>House Allowance:</span>
                        <span>{{
                            formatCurrency(payrollItem.house_allowance)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Medical Allowance:</span>
                        <span>{{
                            formatCurrency(payrollItem.medical_allowance)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Transport Allowance:</span>
                        <span>{{
                            formatCurrency(payrollItem.transport_allowance)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Overtime Amount:</span>
                        <span>{{
                            formatCurrency(payrollItem.overtime_amount)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Bonus:</span>
                        <span>{{ formatCurrency(payrollItem.bonus) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Festival Bonus:</span>
                        <span>{{
                            formatCurrency(payrollItem.festival_bonus)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Performance Incentive:</span>
                        <span>{{
                            formatCurrency(payrollItem.performance_incentive)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between font-bold border-t pt-2 mt-2"
                    >
                        <span>Gross Salary:</span>
                        <span>{{
                            formatCurrency(payrollItem.gross_salary)
                        }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Deductions
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Leave Deduction:</span>
                        <span>{{
                            formatCurrency(payrollItem.leave_deduction)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax Amount:</span>
                        <span>{{
                            formatCurrency(payrollItem.tax_amount)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Loan Deduction:</span>
                        <span>{{
                            formatCurrency(payrollItem.loan_deduction)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Other Deductions:</span>
                        <span>{{
                            formatCurrency(payrollItem.other_deductions)
                        }}</span>
                    </div>
                    <div
                        class="flex justify-between font-bold border-t pt-2 mt-2"
                    >
                        <span>Total Deductions:</span>
                        <span>{{
                            formatCurrency(payrollItem.deductions)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex justify-between items-center bg-rose-100 p-4 rounded-lg"
        >
            <h3 class="text-xl font-bold text-gray-800">Net Salary:</h3>
            <span class="text-2xl font-extrabold text-rose-700">{{
                formatCurrency(payrollItem.net_salary)
            }}</span>
        </div>
        <div class="mt-4 text-lg font-semibold text-gray-700">
            <p><strong>Net Salary in Words:</strong> {{ netSalaryInWords }}</p>
        </div>

        <div class="mt-12 text-gray-600 text-sm">
            <p>
                This is a computer-generated payslip and does not require a
                signature.
            </p>
            <p class="mt-2">Generated on: {{ formatDateTime(new Date()) }}</p>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";

const props = defineProps({
    payrollItem: Object,
    currencySymbol: String,
    netSalaryInWords: String,
});

const appLogoPath = computed(() => usePage().props.app_logo_path || {});

const formatCurrency = (value) => {
    if (value === null || value === undefined) {
        return `${props.currencySymbol} 0.00`;
    }
    return `${props.currencySymbol} ${parseFloat(value).toLocaleString(
        "en-US",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        },
    )}`;
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

<style scoped>
@media print {
    body > *:not(.print-area) {
        display: none !important;
    }
    .print-area {
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .print-area,
    .print-area * {
        visibility: visible;
    }
    .print-area .shadow-lg {
        box-shadow: none !important;
    }
    .print-area .rounded-lg {
        border-radius: 0 !important;
    }
    .print-area h1,
    .print-area h2,
    .print-area h3,
    .print-area strong {
        color: #000 !important;
    }
    .print-area p,
    .print-area span {
        color: #333 !important;
    }
}
</style>
