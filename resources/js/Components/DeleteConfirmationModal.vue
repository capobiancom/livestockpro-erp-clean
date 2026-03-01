<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4 animate-fadeIn">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 rounded-full p-3 mr-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">
                    Confirm Deletion
                </h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete
                <span class="font-semibold">{{ itemName }}</span>?
                This action cannot be undone.
            </p>
            <div class="flex gap-3 justify-end">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                >
                    Cancel
                </button>
                <button
                    @click="$emit('confirm')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                >
                    {{ confirmText || 'Delete' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    itemName: String,
    confirmText: String,
});

defineEmits(['close', 'confirm']);
</script>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>
