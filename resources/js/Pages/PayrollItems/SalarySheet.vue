<template>
    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('payroll-items.index')"
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
                            Salary Sheet for
                            {{ getMonthName(payrollRun.month) }}
                            {{ payrollRun.year }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed salary information for all employees
                        </p>
                    </div>
                </div>
                <button
                    @click="printSalarySheet"
                    class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white ml-5 uppercase tracking-widest hover:bg-rose-700 active:bg-rose-900 focus:outline-none focus:border-rose-900 focus:ring ring-rose-300 disabled:opacity-25 transition ease-in-out duration-150"
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
                    Print Salary Sheet
                </button>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6 p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Salary Sheet</h1>
                <p class="text-xl text-gray-600">
                    For {{ getMonthName(payrollRun.month) }}
                    {{ payrollRun.year }}
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Generated on: {{ formatDateTime(new Date()) }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Employee
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Designation
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Basic Salary
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Allowances
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Overtime
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Gross Salary
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Deductions
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Net Salary
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="item in payrollItems"
                            :key="item.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ item.employee?.first_name }}
                                    {{ item.employee?.last_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ item.employee?.department?.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ item.employee?.designation?.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ currencySymbol }}{{ item.basic_salary }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <p>
                                    H: {{ currencySymbol
                                    }}{{ item.house_allowance }}
                                </p>
                                <p>
                                    M: {{ currencySymbol
                                    }}{{ item.medical_allowance }}
                                </p>
                                <p>
                                    T: {{ currencySymbol
                                    }}{{ item.transport_allowance }}
                                </p>
                                <p>B: {{ currencySymbol }}{{ item.bonus }}</p>
                                <p>
                                    FB: {{ currencySymbol
                                    }}{{ item.festival_bonus }}
                                </p>
                                <p>
                                    PI: {{ currencySymbol
                                    }}{{ item.performance_incentive }}
                                </p>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <p>Hrs: {{ item.overtime_hours }}</p>
                                <p>
                                    Rate: {{ currencySymbol
                                    }}{{ item.overtime_rate }}
                                </p>
                                <p>
                                    Amt: {{ currencySymbol
                                    }}{{ item.overtime_amount }}
                                </p>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900"
                            >
                                {{ currencySymbol }}{{ item.gross_salary }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <p>
                                    Leave: {{ currencySymbol
                                    }}{{ item.leave_deduction }}
                                </p>
                                <p>
                                    Loan: {{ currencySymbol
                                    }}{{ item.loan_deduction }}
                                </p>
                                <p>
                                    Tax: {{ currencySymbol
                                    }}{{ item.tax_amount }}
                                </p>
                                <p>
                                    Other: {{ currencySymbol
                                    }}{{ item.other_deductions }}
                                </p>
                                <p class="font-semibold">
                                    Total: {{ currencySymbol
                                    }}{{ item.deductions }}
                                </p>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap font-bold text-lg text-rose-600"
                            >
                                {{ currencySymbol }}{{ item.net_salary }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th
                                colspan="2"
                                class="px-6 py-3 text-right text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                Total:
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalBasicSalary }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalAllowances }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalOvertimeAmount }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalGrossSalary }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-gray-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalDeductions }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-base font-bold text-rose-700 uppercase tracking-wider"
                            >
                                {{ currencySymbol }}{{ totalNetSalary }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/Layout/AppLayout.vue";

const props = defineProps({
    payrollRun: Object,
    payrollItems: Array,
    appSettings: Object,
});

const currencySymbol = computed(
    () => props.appSettings?.currency_symbol || "$",
);

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

const totalBasicSalary = computed(() =>
    props.payrollItems
        .reduce((sum, item) => sum + parseFloat(item.basic_salary), 0)
        .toFixed(2),
);

const totalAllowances = computed(() =>
    props.payrollItems
        .reduce(
            (sum, item) =>
                sum +
                parseFloat(item.house_allowance) +
                parseFloat(item.medical_allowance) +
                parseFloat(item.transport_allowance) +
                parseFloat(item.bonus) +
                parseFloat(item.festival_bonus) +
                parseFloat(item.performance_incentive),
            0,
        )
        .toFixed(2),
);

const totalOvertimeAmount = computed(() =>
    props.payrollItems
        .reduce((sum, item) => sum + parseFloat(item.overtime_amount), 0)
        .toFixed(2),
);

const totalGrossSalary = computed(() =>
    props.payrollItems
        .reduce((sum, item) => sum + parseFloat(item.gross_salary), 0)
        .toFixed(2),
);

const totalDeductions = computed(() =>
    props.payrollItems
        .reduce((sum, item) => sum + parseFloat(item.deductions), 0)
        .toFixed(2),
);

const totalNetSalary = computed(() =>
    props.payrollItems
        .reduce((sum, item) => sum + parseFloat(item.net_salary), 0)
        .toFixed(2),
);

const printSalarySheet = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    /* Make the entire layout visible for printing */
    #app,
    #app > div, /* Target the main content wrapper within #app */
    #app > div * {
        visibility: visible;
    }

    /* Ensure the main content area takes full width and is positioned correctly */
    #app {
        position: static !important; /* Override absolute positioning */
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Hide elements not needed for print, like the header/navigation */
    .app-sidebar, /* Hide the main application sidebar */
    .app-header, /* Hide the main application header */
    .flex.items-center.justify-between {
        /* Hide the header with back button and print button within SalarySheet.vue */
        display: none !important;
    }

    /* Ensure the main content area takes full width when sidebar and header are hidden */
    .flex-1.flex.flex-col.h-screen.transition-all.duration-300.ease-in-out.lg\:ml-64 {
        margin-left: 0 !important;
        width: 100% !important;
    }

    .bg-white.rounded-lg.shadow-lg.overflow-hidden.mt-6.p-6 {
        /* Target the main content container */
        visibility: visible !important;
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important; /* Remove shadow for print */
        overflow: visible !important; /* Ensure content is not hidden by overflow */
    }

    /* Ensure table and its container are fully visible and take full width */
    .overflow-x-auto {
        overflow-x: visible !important;
        width: 100% !important;
    }

    table {
        width: 100% !important;
        table-layout: auto !important; /* Allow columns to adjust naturally */
        border-collapse: collapse;
    }

    th,
    td {
        white-space: normal !important; /* Allow text to wrap */
        word-wrap: break-word;
        padding: 8px 4px !important; /* Adjust padding for print */
        font-size: 10px !important; /* Smaller font size for more content */
    }

    /* Adjust specific column widths if necessary, e.g., for allowances/deductions */
    td:nth-child(4), /* Allowances */
    td:nth-child(7) {
        /* Deductions */
        width: 15% !important; /* Give more space to these columns */
    }

    /* Remove background colors and shadows for a cleaner print */
    .bg-white,
    .bg-gray-50 {
        background-color: transparent !important;
    }
    .shadow-lg {
        box-shadow: none !important;
    }
}
</style>
