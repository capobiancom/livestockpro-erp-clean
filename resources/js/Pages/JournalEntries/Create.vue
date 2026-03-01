<script setup>
import JournalEntryForm from "./Partials/JournalEntryForm.vue";

const props = defineProps({
    chartOfAccounts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Layout title="Create Journal Entry">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Journal Entry
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <JournalEntryForm :chartOfAccounts="chartOfAccounts" />
            </div>
        </div>
    </Layout>
</template>
