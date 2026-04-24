<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('restablecer_contrasena')" />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" :value="$t('correo_electronico')" />
                <div class="mt-1">
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-rose-500 focus:outline-none focus:ring-rose-500 sm:text-sm"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" :value="$t('contrasena')" />
                <div class="mt-1">
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-rose-500 focus:outline-none focus:ring-rose-500 sm:text-sm"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    :value="$t('confirmar_contrasena')"
                />
                <div class="mt-1">
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-rose-500 focus:outline-none focus:ring-rose-500 sm:text-sm"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div>
                <PrimaryButton
                    class="flex w-full justify-center rounded-md border border-transparent bg-gradient-to-r from-rose-500 to-pink-500 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-rose-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition duration-200"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t('restablecer_contrasena') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
