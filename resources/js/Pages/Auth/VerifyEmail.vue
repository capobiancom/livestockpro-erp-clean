<script setup>
import { computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);
</script>

<template>
    <GuestLayout>
        <Head :title="$t('verificacion_correo')" />

        <div class="mb-4 text-sm text-gray-600">
            {{ $t('texto_gracias_registro_verificacion') }}
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ $t('texto_enlace_verificacion_enviado') }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <PrimaryButton
                    class="flex w-full justify-center rounded-md border border-transparent bg-gradient-to-r from-rose-500 to-pink-500 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-rose-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition duration-200"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t('reenviar_correo_verificacion') }}
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-2 text-gray-500">
                        {{ $t('o_cerrar_sesion') }}
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition duration-200"
                >
                    {{ $t('cerrar_sesion') }}
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
