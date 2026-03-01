<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

const form = useForm({
    name: "",
    farm_name: "",
    farm_location: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                Create your account
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                Set up your farm profile in a few steps.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="Your name" />
                <div class="mt-2">
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <InputLabel for="farm_name" value="Farm name" />
                    <div class="mt-2">
                        <TextInput
                            id="farm_name"
                            type="text"
                            v-model="form.farm_name"
                            required
                            autocomplete="organization"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.farm_name" />
                </div>

                <div>
                    <InputLabel for="farm_location" value="Farm location" />
                    <div class="mt-2">
                        <TextInput
                            id="farm_location"
                            type="text"
                            v-model="form.farm_location"
                            required
                            autocomplete="address-level2"
                        />
                    </div>
                    <InputError
                        class="mt-2"
                        :message="form.errors.farm_location"
                    />
                </div>
            </div>

            <div>
                <InputLabel for="email" value="Email address" />
                <div class="mt-2">
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <InputLabel for="password" value="Password" />
                    <div class="mt-2">
                        <TextInput
                            id="password"
                            type="password"
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
                        value="Confirm password"
                    />
                    <div class="mt-2">
                        <TextInput
                            id="password_confirmation"
                            type="password"
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
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
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
                        Already have an account?
                    </span>
                </div>
            </div>

            <div class="mt-5">
                <Link
                    :href="route('login')"
                    class="flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-900 shadow-sm transition hover:bg-slate-50"
                >
                    Sign in
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
