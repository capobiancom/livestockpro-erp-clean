<script setup>
import { computed } from "vue";
import { Head, useForm, usePage } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const flashSuccess = computed(() => usePage().props.flash?.success);

const form = useForm({
    super_admin_mail_mailer: props.settings.super_admin_mail_mailer ?? "smtp",
    super_admin_mail_host: props.settings.super_admin_mail_host ?? "",
    super_admin_mail_port: props.settings.super_admin_mail_port ?? 587,
    super_admin_mail_username: props.settings.super_admin_mail_username ?? "",
    super_admin_mail_password: "",
    super_admin_mail_encryption:
        props.settings.super_admin_mail_encryption ?? "tls",
    super_admin_mail_from_address:
        props.settings.super_admin_mail_from_address ?? "",
    super_admin_mail_from_name: props.settings.super_admin_mail_from_name ?? "",
});

function submit() {
    form.post(route("admin.settings.email.update"), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Email Settings" />

    <AppLayout>
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-2">
                <h1
                    class="text-2xl font-semibold tracking-tight text-slate-900"
                >
                    Email configuration
                </h1>
                <p class="text-sm text-slate-600">
                    Configure SMTP settings used by the Super Admin to send demo
                    presentation emails.
                </p>
            </div>

            <div
                v-if="flashSuccess"
                class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900"
            >
                {{ flashSuccess }}
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div
                    class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                SMTP settings
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                These settings are stored in the database. For
                                production, ensure your server allows outbound
                                SMTP connections.
                            </div>
                        </div>

                        <div
                            class="hidden sm:flex items-center gap-2 rounded-2xl bg-slate-50 px-3 py-2 ring-1 ring-slate-200"
                        >
                            <div
                                class="h-2 w-2 rounded-full"
                                :class="
                                    form.isDirty
                                        ? 'bg-amber-400'
                                        : 'bg-emerald-400'
                                "
                            />
                            <div class="text-xs text-slate-600">
                                {{ form.isDirty ? "Unsaved changes" : "Saved" }}
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('mailer') }} </label
                                >
                                <select
                                    v-model="form.super_admin_mail_mailer"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option value="smtp"> {{ $t('smtp') }} </option>
                                    <option value="sendmail"> {{ $t('sendmail') }} </option>
                                    <option value="log"> {{ $t('log') }} </option>
                                </select>
                                <div
                                    v-if="form.errors.super_admin_mail_mailer"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.super_admin_mail_mailer }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('encryption') }} </label
                                >
                                <select
                                    v-model="form.super_admin_mail_encryption"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option value=""> {{ $t('none') }} </option>
                                    <option value="tls"> {{ $t('tls') }} </option>
                                    <option value="ssl"> {{ $t('ssl') }} </option>
                                </select>
                                <div
                                    v-if="
                                        form.errors.super_admin_mail_encryption
                                    "
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{
                                        form.errors.super_admin_mail_encryption
                                    }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('host') }} </label
                                >
                                <input
                                    v-model="form.super_admin_mail_host"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="smtp.gmail.com"
                                />
                                <div
                                    v-if="form.errors.super_admin_mail_host"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.super_admin_mail_host }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('port') }} </label
                                >
                                <input
                                    v-model="form.super_admin_mail_port"
                                    type="number"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="587"
                                />
                                <div
                                    v-if="form.errors.super_admin_mail_port"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.super_admin_mail_port }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('username') }} </label
                                >
                                <input
                                    v-model="form.super_admin_mail_username"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="your@email.com"
                                />
                                <div
                                    v-if="form.errors.super_admin_mail_username"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.super_admin_mail_username }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('password') }} </label
                                >
                                <input
                                    v-model="form.super_admin_mail_password"
                                    type="password"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="••••••••"
                                />
                                <div class="mt-2 text-xs text-slate-500">
                                    Leave blank to keep the existing password.
                                </div>
                                <div
                                    v-if="form.errors.super_admin_mail_password"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.super_admin_mail_password }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <div
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <div
                                        class="text-sm font-semibold text-slate-900"
                                    >
                                        From address
                                    </div>
                                    <div class="mt-1 text-sm text-slate-600">
                                        This is the sender shown to clients.
                                    </div>

                                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700"
                                                > {{ $t('from_email') }} </label
                                            >
                                            <input
                                                v-model="
                                                    form.super_admin_mail_from_address
                                                "
                                                type="email"
                                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="noreply@yourdomain.com"
                                            />
                                            <div
                                                v-if="
                                                    form.errors
                                                        .super_admin_mail_from_address
                                                "
                                                class="mt-2 text-sm text-red-600"
                                            >
                                                {{
                                                    form.errors
                                                        .super_admin_mail_from_address
                                                }}
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700"
                                                > {{ $t('from_name') }} </label
                                            >
                                            <input
                                                v-model="
                                                    form.super_admin_mail_from_name
                                                "
                                                type="text"
                                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Vacaliza Demo Team"
                                            />
                                            <div
                                                v-if="
                                                    form.errors
                                                        .super_admin_mail_from_name
                                                "
                                                class="mt-2 text-sm text-red-600"
                                            >
                                                {{
                                                    form.errors
                                                        .super_admin_mail_from_name
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="text-sm text-slate-500">
                                <span v-if="form.processing"> {{ $t('saving') }} </span>
                                <span v-else
                                    >Changes are applied for future
                                    emails.</span
                                >
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Save email settings
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 p-1 ring-1 ring-slate-200/60"
                >
                    <div
                        class="rounded-[22px] bg-slate-950/70 p-6 text-slate-100 backdrop-blur"
                    >
                        <div class="text-xs font-semibold text-white/70">
                            Security notes
                        </div>

                        <ul class="mt-4 space-y-3 text-sm text-white/70">
                            <li class="flex gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-300/80"
                                />
                                <span>
                                    Use an app password for Gmail/Outlook when
                                    2FA is enabled.
                                </span>
                            </li>
                            <li class="flex gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-300/80"
                                />
                                <span>
                                    Prefer a dedicated sender address (e.g.
                                    demo@yourdomain.com).
                                </span>
                            </li>
                            <li class="flex gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-300/80"
                                />
                                <span>
                                    If you use “log” mailer, emails will be
                                    written to logs instead of being sent.
                                </span>
                            </li>
                        </ul>

                        <div class="mt-6 text-xs text-white/50">
                            Tip: test sending from the Demo Requests page after
                            saving.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
