<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 text-indigo-500 mx-auto mb-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"
                    />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    Welcome to AgroSass!
                </h2>
                <p class="text-gray-600">
                    To help you get started, would you like to seed your farm
                    with some demo data? This includes sample animals, feed
                    types, and health events.
                </p>
            </div>

            <div v-if="processing" class="text-center">
                <div
                    class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-4"
                >
                    <div
                        class="bg-indigo-600 h-2.5 rounded-full"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>
                <p class="text-indigo-600 font-semibold text-lg">
                    Seeding Data... {{ progress }}%
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    This may take a moment. Please do not close this window.
                </p>
            </div>

            <div v-else class="flex flex-col space-y-4">
                <button
                    @click="seedDemoData"
                    :disabled="processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Seed Demo Data
                </button>
                <button
                    @click="skipDemoData"
                    :disabled="processing"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Skip for now
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from "vue"; // Import ref
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    farmId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["close", "seed-data"]);

const form = useForm({});
const processing = ref(false); // New ref for tracking processing state
const progress = ref(0); // New ref for tracking progress percentage

const seedDemoData = () => {
    processing.value = true;
    progress.value = 0; // Reset progress

    // Simulate progress updates (since backend seeding is a single request)
    let interval = setInterval(() => {
        if (progress.value < 90) {
            progress.value += 10;
        }
    }, 300); // Update every 300ms

    form.post(route("register.seed-demo", { farm: props.farmId }), {
        // Pass farmId as route parameter
        onStart: () => {
            processing.value = true;
            progress.value = 0;
        },
        onProgress: (event) => {
            // Inertia's onProgress event provides loaded/total bytes,
            // but for a backend seeding process, it might not be granular.
            // We'll rely on our simulated progress for a smoother UX.
            // If the backend could send progress updates, this would be used.
        },
        onSuccess: () => {
            clearInterval(interval); // Stop simulation
            progress.value = 100; // Ensure it reaches 100%
            processing.value = false;
            // After successful seeding, Inertia will handle the redirect with flash message
            // No need to emit 'seed-data' or manually close the popup here,
            // as the redirect will cause a full page reload and the popup will naturally disappear.
        },
        onError: (errors) => {
            clearInterval(interval); // Stop simulation
            progress.value = 0; // Reset on error
            processing.value = false;
            console.error("Error seeding demo data:", errors);
            // Optionally, show an error message to the user
            emit("close"); // Close even on error to avoid blocking
        },
    });
};

const skipDemoData = () => {
    form.post(route("register.skip-demo", { farm: props.farmId }), {
        // Pass farmId as route parameter
        onSuccess: () => {
            emit("close");
        },
        onError: (errors) => {
            console.error("Error skipping demo data:", errors);
            emit("close");
        },
    });
};
</script>

<style scoped>
/* Add any specific styles here if needed, though Tailwind should handle most */
</style>
