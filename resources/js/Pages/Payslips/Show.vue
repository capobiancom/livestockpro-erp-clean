<template>
    <AppLayout>
        <template #title>Payslip Details</template>
        <template #actions>
            <button
                @click="printPayslip"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
                Print Payslip
            </button>
        </template>
        <PayslipContent
            :payrollItem="payrollItem"
            :currencySymbol="currencySymbol"
        />
    </AppLayout>
</template>

<script setup>
import PayslipContent from "@/Components/PayslipContent.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";

const props = defineProps({
    payrollItem: Object,
});

const page = usePage();
const logoUrl = computed(() => page.props.app_logo_path);
const currencySymbol = computed(() => page.props.app_currency_symbol);

const printPayslip = () => {
    window.print();
};
</script>
