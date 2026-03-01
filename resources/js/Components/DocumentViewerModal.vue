<script setup>
import { defineProps, defineEmits, watch } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    filePath: {
        type: String,
        default: null,
    },
    documentType: {
        type: String,
        default: "Document",
    },
});

const emit = defineEmits(["close"]);

const close = () => {
    emit("close");
};

watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = null;
        }
    },
);
</script>

<template>
    <Transition leave-active-class="duration-200">
        <div
            v-show="show"
            class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
            scroll-on-close
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div class="absolute inset-0 bg-gray-500 opacity-75" />
                </div>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto max-w-4xl h-[90vh] flex flex-col"
                >
                    <div
                        class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-500 text-white"
                    >
                        <h3 class="text-lg font-semibold">
                            Viewing: {{ documentType }}
                        </h3>
                        <button
                            @click="close"
                            class="text-white hover:text-gray-200 focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 p-4 overflow-hidden">
                        <iframe
                            v-if="filePath"
                            :src="filePath"
                            class="w-full h-full border-0 rounded-md"
                            frameborder="0"
                        ></iframe>
                        <div
                            v-else
                            class="flex items-center justify-center h-full text-gray-500"
                        >
                            No file to display.
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
