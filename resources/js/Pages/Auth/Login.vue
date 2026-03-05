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
        <Head title="Log in" />

        <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                Welcome back
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                Sign in to continue to your dashboard.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email address" />
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
                    <InputLabel for="password" value="Password" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-indigo-700 hover:text-indigo-600"
                    >
                        Forgot password?
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
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="text-sm text-slate-700">Remember me</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
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
                        New to AgroSaaS?
                    </span>
                </div>
            </div>

            <div class="mt-5">
                <Link
                    :href="route('register')"
                    class="flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-900 shadow-sm transition hover:bg-slate-50"
                >
                    Create an account
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
