<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    show: Boolean,
    farmId: Number,
});

const emit = defineEmits(["close"]);

const form = useForm({
    farm_id: props.farmId,
});

const seedDemoData = () => {
    form.post(route("register.seed-demo"), {
        onSuccess: () => {
            emit("close");
        },
    });
};

const skipDemoData = () => {
    form.post(route("register.skip-demo"), {
        onSuccess: () => {
            emit("close");
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-8 bg-white rounded-lg shadow-xl text-center">
            <div
                class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10 text-white"
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
            </div>
            <h3 class="text-3xl font-bold text-gray-800 mb-4">
                Welcome to Vacaliza!
            </h3>
            <p class="text-md text-gray-600 mb-8 leading-relaxed">
                Your farm has been successfully registered. To help you get
                started, you can seed your account with some realistic demo
                data. This will populate your dashboard with examples of
                animals, feedings, health records, and more.
            </p>

            <div class="flex flex-col space-y-4">
                <PrimaryButton
                    @click="seedDemoData"
                    class="w-full bg-green-600 hover:bg-green-700 focus:ring-green-500 text-lg py-3"
                >
                    Seed Demo Data
                </PrimaryButton>
                <SecondaryButton
                    @click="skipDemoData"
                    class="w-full border-gray-300 text-gray-700 hover:bg-gray-100 text-lg py-3"
                >
                    Skip Demo Data
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
