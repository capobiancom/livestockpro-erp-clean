<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                Bienvenido de nuevo
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                Inicie sesión para acceder a su panel.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Correo electrónico" />
                <div class="mt-2">
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Contraseña" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-emerald-700 hover:text-emerald-600"
                    >
                        ¿Olvidó su contraseña?
                    </Link>
                </div>

                <div class="mt-2">
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <Checkbox
                        id="remember-me"
                        name="remember-me"
                        v-model:checked="form.remember"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                    />
                    <span class="text-sm text-slate-700"> {{ $t('recordarme') }} </span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center bg-emerald-600 hover:bg-emerald-500 focus:ring-emerald-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Iniciar sesión
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200" />
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-2 text-slate-500">
                        ¿Nuevo en Vacaliza?
                    </span>
                </div>
            </div>

            <div class="mt-5">
                <Link
                    :href="route('register')"
                    class="flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-900 shadow-sm transition hover:bg-slate-50"
                >
                    Crear una cuenta
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
