<template>
    <Layout>
        <template #title>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Application Settings
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage general application settings
                </p>
            </div>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-2">
                <h1
                    class="text-2xl font-semibold tracking-tight text-slate-900"
                >
                    Application settings
                </h1>
                <p class="text-sm text-slate-600">
                    Update the application identity and operational defaults.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form card -->
                <div
                    class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                General settings
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Title, currency, timezone, and inventory
                                consumption type.
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
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Application title</label
                                >
                                <input
                                    v-model="form.app_title"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Vacaliza"
                                />
                                <div
                                    v-if="form.errors.app_title"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.app_title }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Currency</label
                                >
                                <select
                                    v-model="form.currency"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option
                                        v-for="c in currencyOptions"
                                        :key="c"
                                        :value="c"
                                    >
                                        {{ c }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.currency"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.currency }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Timezone</label
                                >
                                <select
                                    v-model="form.timezone"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option
                                        v-for="tz in timezones"
                                        :key="tz"
                                        :value="tz"
                                    >
                                        {{ tz }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.timezone"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.timezone }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Application logo</label
                                >
                                <div class="mt-2 flex items-center gap-4">
                                    <div
                                        class="h-14 w-14 overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="logoPreviewUrl"
                                            :src="logoPreviewUrl"
                                            alt="New Application Logo Preview"
                                            class="h-full w-full object-contain"
                                        />
                                        <img
                                            v-else-if="form.logo_path"
                                            :src="`/storage/${form.logo_path}`"
                                            alt="Application Logo"
                                            class="h-full w-full object-contain"
                                        />
                                        <div
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            Logo
                                        </div>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <input
                                            type="file"
                                            @change="handleLogoUpload"
                                            class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                                        />
                                        <div
                                            class="mt-2 text-xs text-slate-500"
                                        >
                                            PNG/SVG/WebP recommended. Max 2MB.
                                        </div>
                                        <div
                                            v-if="form.errors.logo_path"
                                            class="mt-2 text-sm text-red-600"
                                        >
                                            {{ form.errors.logo_path }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Inventory consumption type</label
                                >
                                <select
                                    v-model="form.inventory_consumption_type"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option :value="consumptionTypes.FIFO">
                                        FIFO (First-In, First-Out)
                                    </option>
                                    <option :value="consumptionTypes.FEFO">
                                        FEFO (First-Expired, First-Out)
                                    </option>
                                </select>
                                <div
                                    v-if="
                                        form.errors.inventory_consumption_type
                                    "
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.inventory_consumption_type }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="text-sm text-slate-500">
                                <span v-if="form.processing">Saving…</span>
                                <span v-else
                                    >Changes are applied immediately after
                                    saving.</span
                                >
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Side card -->
                <div
                    class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 p-1 ring-1 ring-slate-200/60"
                >
                    <div
                        class="rounded-[22px] bg-slate-950/70 p-6 text-slate-100 backdrop-blur"
                    >
                        <div class="text-xs font-semibold text-white/70">
                            Preview
                        </div>

                        <div class="mt-4">
                            <div class="text-lg font-semibold text-white">
                                {{ form.app_title || "Your application title" }}
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                Currency: {{ form.currency || "USD" }}
                            </div>
                            <div class="mt-1 text-sm text-white/70">
                                Timezone: {{ form.timezone || "UTC" }}
                            </div>
                        </div>

                        <div class="mt-6 text-xs text-white/50">
                            Tip: keep the title short for best readability.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";

const props = defineProps({
    settings: Object,
    timezones: Array,
    consumptionTypes: Object, // Add this prop
    currencyOptions: Array,
});

const form = useForm({
    app_title: props.settings.app_title || "",
    currency: props.settings.currency || "USD",
    logo_path: props.settings.logo_path || null,
    timezone: props.settings.timezone || "UTC",
    inventory_consumption_type:
        props.settings.inventory_consumption_type ||
        props.consumptionTypes.FIFO,
});

const logoPreviewUrl = ref(null);

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo_path = file;
        logoPreviewUrl.value = URL.createObjectURL(file);
    } else {
        form.logo_path = null;
        logoPreviewUrl.value = null;
    }
};

const submit = () => {
    form.post(route("settings.update"), {
        forceFormData: true, // Important for file uploads
        onSuccess: () => {
            // Reload the page to ensure appSettings are updated globally
            window.location.reload();
        },
        onError: (errors) => {
            // Handle errors
            console.error("Error saving settings:", errors);
        },
    });
};
</script>
