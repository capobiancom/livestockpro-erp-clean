<template>
    <div class="pos-print-area text-xs font-mono leading-tight">
        <div class="text-center mb-2">
            <h2 class="text-sm font-bold uppercase">AGRO SASS</h2>
            <p class="text-xs">Payslip</p>
            <p class="text-xs">
                {{ getMonthName(payrollItem.payroll_run?.month) }}
                {{ payrollItem.payroll_run?.year }}
            </p>
            <hr class="my-1 border-t border-dashed border-gray-400" />
        </div>

        <div class="mb-2">
            <h3 class="font-bold">Employee Details:</h3>
            <p>
                Name: {{ payrollItem.employee?.first_name }}
                {{ payrollItem.employee?.last_name }}
            </p>
            <p>ID: {{ payrollItem.employee?.employee_id }}</p>
            <p>Dept: {{ payrollItem.employee?.department?.name }}</p>
            <p>Desig: {{ payrollItem.employee?.designation?.name }}</p>
            <hr class="my-1 border-t border-dashed border-gray-400" />
        </div>

        <div class="mb-2">
            <h3 class="font-bold">Payroll Period:</h3>
            <p>Working Days: {{ payrollItem.working_days }}</p>
            <p>Paid Leave: {{ payrollItem.paid_leave_days }}</p>
            <p>Unpaid Leave: {{ payrollItem.unpaid_leave_days }}</p>
            <hr class="my-1 border-t border-dashed border-gray-400" />
        </div>

        <div class="mb-2">
            <h3 class="font-bold">Earnings:</h3>
            <div class="flex justify-between">
                <span>Basic Salary:</span>
                <span>{{ formatCurrency(payrollItem.basic_salary) }}</span>
            </div>
            <div class="flex justify-between">
                <span>House Allowance:</span>
                <span>{{ formatCurrency(payrollItem.house_allowance) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Medical Allowance:</span>
                <span>{{ formatCurrency(payrollItem.medical_allowance) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Transport Allowance:</span>
                <span>{{
                    formatCurrency(payrollItem.transport_allowance)
                }}</span>
            </div>
            <div class="flex justify-between">
                <span>Overtime:</span>
                <span>{{ formatCurrency(payrollItem.overtime_amount) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bonus:</span>
                <span>{{ formatCurrency(payrollItem.bonus) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Festival Bonus:</span>
                <span>{{ formatCurrency(payrollItem.festival_bonus) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Performance Incentive:</span>
                <span>{{
                    formatCurrency(payrollItem.performance_incentive)
                }}</span>
            </div>
            <hr class="my-1 border-t border-dashed border-gray-400" />
            <div class="flex justify-between font-bold">
                <span>Gross Salary:</span>
                <span>{{ formatCurrency(payrollItem.gross_salary) }}</span>
            </div>
            <hr class="my-1 border-t border-dashed border-gray-400" />
        </div>

        <div class="mb-2">
            <h3 class="font-bold">Deductions:</h3>
            <div class="flex justify-between">
                <span>Leave Deduction:</span>
                <span>{{ formatCurrency(payrollItem.leave_deduction) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tax Amount:</span>
                <span>{{ formatCurrency(payrollItem.tax_amount) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Loan Deduction:</span>
                <span>{{ formatCurrency(payrollItem.loan_deduction) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Other Deductions:</span>
                <span>{{ formatCurrency(payrollItem.other_deductions) }}</span>
            </div>
            <hr class="my-1 border-t border-dashed border-gray-400" />
            <div class="flex justify-between font-bold">
                <span>Total Deductions:</span>
                <span>{{ formatCurrency(payrollItem.deductions) }}</span>
            </div>
            <hr class="my-1 border-t border-dashed border-gray-400" />
        </div>

        <div class="flex justify-between items-center text-sm font-bold mb-2">
            <span>NET SALARY:</span>
            <span>{{ formatCurrency(payrollItem.net_salary) }}</span>
        </div>
        <div class="text-xs font-bold mb-2">
            <p>In Words: {{ netSalaryInWords }}</p>
        </div>
        <hr class="my-1 border-t border-dashed border-gray-400" />

        <div class="text-center text-xs mt-2">
            <p>This is a computer-generated payslip.</p>
            <p>Generated on: {{ formatDateTime(new Date()) }}</p>
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
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact; /* For Chrome/Safari */
        print-color-adjust: exact; /* Standard */
    }
    .pos-print-area {
        width: 58mm; /* Typical POS printer width */
        max-width: 58mm;
        margin: 0 auto;
        padding: 5mm; /* Small padding for edges */
        box-shadow: none !important;
        border-radius: 0 !important;
        font-size: 8pt; /* Adjust font size for POS */
        line-height: 1.2;
        color: #000; /* Ensure black text */
    }
    .pos-print-area h1,
    .pos-print-area h2,
    .pos-print-area h3,
    .pos-print-area strong {
        color: #000 !important;
    }
    .pos-print-area p,
    .pos-print-area span {
        color: #000 !important;
    }
    /* Hide everything else when printing */
    body > *:not(.pos-print-area) {
        display: none !important;
    }
}
</style>
