<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 translate-y-full"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 translate-y-full"
    >
        <div
            v-if="show"
            :class="[
                'fixed bottom-4 right-4 p-4 rounded-lg shadow-lg text-white flex items-center space-x-3 z-50',
                typeClasses,
            ]"
            role="alert"
        >
            <svg
                v-if="type === 'success'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <svg
                v-else-if="type === 'error'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <span>{{ message }}</span>
            <button
                @click="hideToast"
                class="ml-auto -mr-1.5 -my-1.5 p-1.5 rounded-md inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="buttonClasses"
            >
                <span class="sr-only">Dismiss</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    timeout: {
        type: Number,
        default: 5000, // 5 seconds
    },
});

const show = ref(false);
const message = ref("");
const type = ref("info"); // 'success', 'error', 'info'
let timer = null;

const typeClasses = computed(() => {
    switch (type.value) {
        case "success":
            return "bg-green-500";
        case "error":
            return "bg-red-500";
        case "info":
        default:
            return "bg-blue-500";
    }
});

const buttonClasses = computed(() => {
    switch (type.value) {
        case "success":
            return "text-green-200 hover:text-white hover:bg-green-600 focus:ring-green-500";
        case "error":
            return "text-red-200 hover:text-white hover:bg-red-600 focus:ring-red-500";
        case "info":
        default:
            return "text-blue-200 hover:text-white hover:bg-blue-600 focus:ring-blue-500";
    }
});

const showToast = (msg, toastType = "info") => {
    message.value = msg;
    type.value = toastType;
    show.value = true;
    if (timer) {
        clearTimeout(timer);
    }
    timer = setTimeout(() => {
        hideToast();
    }, props.timeout);
};

const hideToast = () => {
    show.value = false;
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }
};

defineExpose({ showToast, hideToast });
</script>

<style scoped>
/* No specific styles needed, Tailwind handles transitions and layout */
</style>
