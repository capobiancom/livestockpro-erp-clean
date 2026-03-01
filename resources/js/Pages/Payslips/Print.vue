<template>
    <div class="p-8">
        <div class="flex items-center mb-4">
            <div class="ml-8 mb-4" v-if="showButtonBlk">
                <button
                    @click="togglePosPrint"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    {{
                        isPosPrint
                            ? "View Standard Payslip"
                            : "View POS Payslip"
                    }}
                </button>
            </div>

            <div class="ml-8 mb-4" v-if="showButtonBlk">
                <div>
                    <button
                        @click="printPayslip"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Print Payslip
                    </button>
                </div>
            </div>
        </div>
        <PayslipContent
            v-if="!isPosPrint"
            :payrollItem="payrollItem"
            :currency-symbol="$page.props.appSettings.currency_symbol"
            :net-salary-in-words="netSalaryInWords"
        />
        <PayslipPosContent
            v-else
            :payrollItem="payrollItem"
            :currency-symbol="$page.props.appSettings.currency_symbol"
            :net-salary-in-words="netSalaryInWords"
        />
    </div>
</template>

<script setup>
import PayslipContent from "@/Components/PayslipContent.vue";
import PayslipPosContent from "@/Components/PayslipPosContent.vue";
import { onMounted, ref, computed } from "vue";
import convertNumberToWords from "@/Utils/numberToWords";

const showButtonBlk = ref(true);
const isPosPrint = ref(false); // New ref to control POS print view

const props = defineProps({
    payrollItem: Object,
});

const netSalaryInWords = computed(() => {
    if (
        props.payrollItem &&
        props.payrollItem.net_salary !== undefined &&
        props.payrollItem.net_salary !== null
    ) {
        // Ensure we're passing a valid number
        const salary = parseFloat(props.payrollItem.net_salary);
        if (!isNaN(salary)) {
            return convertNumberToWords(salary);
        }
    }
    return "zero only";
});

const printPayslip = () => {
    showButtonBlk.value = false;
    setTimeout(() => {
        window.print();
    }, 500);

    window.onafterprint = () => {
        showButtonBlk.value = true;
        window.onafterprint = null; // Remove the event listener
    };
};

const togglePosPrint = () => {
    isPosPrint.value = !isPosPrint.value;
};

onMounted(() => {
    showButtonBlk.value = true;
});
</script>

<style scoped>
@media print {
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact; /* For Chrome/Safari */
        print-color-adjust: exact; /* Standard */
    }
    .p-8 {
        padding: 0 !important; /* Remove padding for print */
    }
    .print-area {
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none !important;
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
    /* Ensure background colors are printed */
    .bg-white {
        background-color: #fff !important;
    }
    .bg-rose-100 {
        background-color: #ffe4e6 !important;
    }
    .bg-pink-50 {
        background-color: #fff7f9 !important;
    }
    .bg-rose-50 {
        background-color: #fffafa !important;
    }
}
</style>
