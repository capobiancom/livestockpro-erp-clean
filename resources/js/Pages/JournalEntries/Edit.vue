<script setup>
import JournalEntryForm from "./Partials/JournalEntryForm.vue";

const props = defineProps({
    journalEntry: {
        type: Object,
        required: true,
    },
    chartOfAccounts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Layout title="Edit Journal Entry">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Journal Entry
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <JournalEntryForm
                    :journalEntry="journalEntry"
                    :chartOfAccounts="chartOfAccounts"
                    isEdit
                />
            </div>
        </div>
    </Layout>
</template>
