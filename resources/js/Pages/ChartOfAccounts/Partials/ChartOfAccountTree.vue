<template>
    <div class="space-y-2">
        <div
            v-for="account in accounts"
            :key="account.id"
            class="flex flex-col"
        >
            <div
                class="flex items-center justify-between rounded-md bg-gray-100 p-3 shadow-sm"
            >
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-800"
                        >{{ account.code }} - {{ account.name }}</span
                    >
                    <span class="text-sm text-gray-500"
                        >({{ account.type }})</span
                    >
                    <span
                        v-if="account.is_system"
                        class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800"
                        >System</span
                    >
                    <span
                        v-if="!account.is_active"
                        class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800"
                        >Inactive</span
                    >
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="$emit('add-child', account)"
                        class="text-green-600 hover:text-green-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                    <button
                        @click="$emit('edit', account)"
                        class="text-indigo-600 hover:text-indigo-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.389-8.389-2.828-2.828z"
                            />
                        </svg>
                    </button>
                    <button
                        @click="$emit('delete', account)"
                        class="text-red-600 hover:text-red-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm-1 3a1 1 0 100 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
            <div
                v-if="account.children && account.children.length"
                class="ml-6 mt-2 border-l-2 border-gray-200 pl-4"
            >
                <ChartOfAccountTree
                    :accounts="account.children"
                    @edit="$emit('edit', $event)"
                    @delete="$emit('delete', $event)"
                    @add-child="$emit('add-child', $event)"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
    accounts: {
        type: Array,
        default: () => [],
    },
});

defineEmits(["edit", "delete", "add-child"]);
</script>

<style scoped>
/* Add any specific styles for the tree view here */
</style>
