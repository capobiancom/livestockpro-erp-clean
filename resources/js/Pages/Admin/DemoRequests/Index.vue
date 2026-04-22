<script setup>
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    requests: {
        type: Object,
        required: true,
    },
});

const flashSuccess = computed(() => usePage().props.flash?.success);

const selected = ref(null);
const showEmailModal = ref(false);

const emailForm = useForm({
    to: "",
    subject: "Vacaliza demo presentation",
    scheduled_at: "",
    message:
        "Hello,\n\nThank you for requesting a demo of Vacaliza.\n\nWe’d be happy to walk you through the platform and answer your questions. Please reply with your preferred time slots, or confirm the proposed schedule below.\n\nProposed schedule:\n- Date:\n- Time:\n- Timezone:\n- Meeting link:\n\nBest regards,\nVacaliza Demo Team\n",
});

function openEmail(request) {
    selected.value = request;
    emailForm.to = request.email;
    emailForm.scheduled_at = request.preferred_date
        ? `${request.preferred_date}`
        : "";
    showEmailModal.value = true;
}

function sendEmail() {
    if (!selected.value) return;

    emailForm.post(route("admin.demo-requests.send-email", selected.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEmailModal.value = false;
            selected.value = null;
            emailForm.reset();
        },
    });
}

const rows = computed(() => props.requests?.data ?? []);
</script>

<template>
    <Head title="Demo Requests" />

    <AppLayout>
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-2">
                <h1
                    class="text-2xl font-semibold tracking-tight text-slate-900"
                >
                    Demo requests
                </h1>
                <p class="text-sm text-slate-600">
                    Review incoming demo requests and send a demo presentation
                    email to the client.
                </p>
            </div>

            <div
                v-if="flashSuccess"
                class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900"
            >
                {{ flashSuccess }}
            </div>

            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-200 px-6 py-4">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div class="text-sm font-semibold text-slate-900">
                            Inbox
                        </div>
                        <div class="text-xs text-slate-500">
                            Showing {{ rows.length }} of
                            {{ props.requests?.total ?? rows.length }}
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600"
                                >
                                    Client
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600"
                                >
                                    Company
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600"
                                >
                                    Preferred
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600"
                                >
                                    Status
                                </th>
                                <th class="px-6 py-3" />
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="r in rows" :key="r.id">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-900"
                                    >
                                        {{ r.name }}
                                    </div>
                                    <div class="mt-1 text-sm text-slate-600">
                                        {{ r.email }}
                                        <span
                                            v-if="r.phone"
                                            class="text-slate-400"
                                        >
                                            • {{ r.phone }}
                                        </span>
                                    </div>
                                    <div
                                        v-if="r.country"
                                        class="mt-1 text-xs text-slate-500"
                                    >
                                        {{ r.country }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ r.company || "—" }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    <div>
                                        {{ r.preferred_date || "—" }}
                                        <span v-if="r.preferred_time">
                                            • {{ r.preferred_time }}
                                        </span>
                                    </div>
                                    <div
                                        v-if="r.timezone"
                                        class="mt-1 text-xs text-slate-500"
                                    >
                                        {{ r.timezone }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1"
                                        :class="
                                            r.status === 'emailed'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                                : 'bg-amber-50 text-amber-700 ring-amber-200'
                                        "
                                    >
                                        {{ r.status }}
                                    </span>
                                    <div
                                        v-if="r.emailed_at"
                                        class="mt-1 text-xs text-slate-500"
                                    >
                                        Emailed: {{ r.emailed_at }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="button"
                                        @click="openEmail(r)"
                                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                    >
                                        Send email
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No demo requests yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="props.requests?.links?.length"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-600">
                        Page {{ props.requests.current_page }} of
                        {{ props.requests.last_page }}
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a
                            v-for="l in props.requests.links"
                            :key="l.label"
                            :href="l.url || '#'"
                            class="rounded-xl px-3 py-2 text-sm ring-1 ring-slate-200 transition"
                            :class="
                                l.active
                                    ? 'bg-slate-900 text-white ring-slate-900'
                                    : l.url
                                      ? 'bg-white text-slate-700 hover:bg-slate-50'
                                      : 'bg-slate-50 text-slate-400 cursor-not-allowed'
                            "
                            v-html="l.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Email modal -->
        <div
            v-if="showEmailModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div
                class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                @click="showEmailModal = false"
            />
            <div
                class="relative w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200"
            >
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                Send demo presentation
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Email will be sent to
                                <span class="font-medium text-slate-900">{{
                                    emailForm.to
                                }}</span>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="showEmailModal = false"
                            class="rounded-2xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <form @submit.prevent="sendEmail" class="p-6">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700"
                                >To</label
                            >
                            <input
                                v-model="emailForm.to"
                                type="email"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="emailForm.errors.to"
                                class="mt-2 text-sm text-rose-600"
                            >
                                {{ emailForm.errors.to }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700"
                                >Subject</label
                            >
                            <input
                                v-model="emailForm.subject"
                                type="text"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="emailForm.errors.subject"
                                class="mt-2 text-sm text-rose-600"
                            >
                                {{ emailForm.errors.subject }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700"
                                >Scheduled at (optional)</label
                            >
                            <input
                                v-model="emailForm.scheduled_at"
                                type="datetime-local"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="emailForm.errors.scheduled_at"
                                class="mt-2 text-sm text-rose-600"
                            >
                                {{ emailForm.errors.scheduled_at }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700"
                                >Message</label
                            >
                            <textarea
                                v-model="emailForm.message"
                                rows="10"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="emailForm.errors.message"
                                class="mt-2 text-sm text-rose-600"
                            >
                                {{ emailForm.errors.message }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="text-sm text-slate-500">
                            Uses the SMTP settings from
                            <span class="font-medium">Email Settings</span>.
                        </div>

                        <button
                            type="submit"
                            :disabled="emailForm.processing"
                            class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Send email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
