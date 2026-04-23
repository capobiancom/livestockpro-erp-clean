<script setup>
import { computed } from "vue";
import { Head, useForm, usePage } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    currencyOptions: {
        type: Array,
        required: true,
    },
});

const flashSuccess = computed(() => usePage().props.flash?.success);

const form = useForm({
    site_title: props.settings.site_title ?? "",
    site_description: props.settings.site_description ?? "",
    website_currency: props.settings.website_currency ?? "USD",
    website_logo: null,
});

const websiteLogoUrl = computed(() => props.settings.website_logo_url ?? null);

const currencySymbol = computed(() => {
    const map = {
        USD: "$",
        EUR: "€",
        GBP: "£",
        INR: "₹",
        JPY: "¥",
        CNY: "¥",
        AUD: "A$",
        CAD: "C$",
        CHF: "CHF",
        SEK: "kr",
        NZD: "NZ$",
        MXN: "MX$",
        SGD: "S$",
        HKD: "HK$",
        NOK: "kr",
        KRW: "₩",
        TRY: "₺",
        RUB: "₽",
        BRL: "R$",
        ZAR: "R",
        PKR: "₨",
        BDT: "৳",
        AED: "د.إ",
        SAR: "﷼",
    };

    return map[form.website_currency] ?? form.website_currency;
});

function submit() {
    form.post(route("admin.settings.website.update"), {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Website Settings" />

    <AppLayout>
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-2">
                <h1
                    class="text-2xl font-semibold tracking-tight text-slate-900"
                >
                    Website design & branding
                </h1>
                <p class="text-sm text-slate-600">
                    Update the public website identity. Changes apply to the
                    landing page and public-facing pages.
                </p>
            </div>

            <div
                v-if="flashSuccess"
                class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900"
            >
                {{ flashSuccess }}
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form card -->
                <div
                    class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                Website settings
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Title, description, and currency used across the
                                public website.
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
                                    > {{ $t('website_logo') }} </label
                                >
                                <div class="mt-2 flex items-center gap-4">
                                    <div
                                        class="h-14 w-14 overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="websiteLogoUrl"
                                            :src="websiteLogoUrl"
                                            alt="Website logo"
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
                                            accept="image/*"
                                            @change="
                                                (e) =>
                                                    (form.website_logo =
                                                        e.target.files?.[0] ??
                                                        null)
                                            "
                                            class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                                        />
                                        <div
                                            class="mt-2 text-xs text-slate-500"
                                        >
                                            PNG/SVG/WebP recommended. Max 2MB.
                                            This affects the public website
                                            only.
                                        </div>
                                        <div
                                            v-if="form.errors.website_logo"
                                            class="mt-2 text-sm text-red-600"
                                        >
                                            {{ form.errors.website_logo }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('site_title') }} </label
                                >
                                <input
                                    v-model="form.site_title"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Vacaliza"
                                />
                                <div
                                    v-if="form.errors.site_title"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.site_title }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('site_description') }} </label
                                >
                                <textarea
                                    v-model="form.site_description"
                                    rows="4"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Short description for the website"
                                />
                                <div
                                    v-if="form.errors.site_description"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.site_description }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('website_currency') }} </label
                                >
                                <select
                                    v-model="form.website_currency"
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
                                    v-if="form.errors.website_currency"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.website_currency }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    > {{ $t('preview_price') }} </label
                                >
                                <div
                                    class="mt-2 flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3"
                                >
                                    <div class="text-sm text-slate-600">
                                        Example
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-slate-900"
                                    >
                                        {{ currencySymbol }} 1,250.00
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-slate-500">
                                    This is a preview only. Currency formatting
                                    on the website will use this selection.
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="text-sm text-slate-500">
                                <span v-if="form.processing"> {{ $t('saving') }} </span>
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

                <!-- Live preview card -->
                <div
                    class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 p-1 ring-1 ring-slate-200/60"
                >
                    <div
                        class="rounded-[22px] bg-slate-950/70 p-6 text-slate-100 backdrop-blur"
                    >
                        <div class="text-xs font-semibold text-white/70">
                            Live preview
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/10 flex items-center justify-center"
                                >
                                    <img
                                        v-if="websiteLogoUrl"
                                        :src="websiteLogoUrl"
                                        alt="Website logo"
                                        class="h-full w-full object-contain"
                                    />
                                    <div
                                        v-else
                                        class="text-[10px] font-semibold text-white/60"
                                    >
                                        Logo
                                    </div>
                                </div>

                                <div class="text-lg font-semibold text-white">
                                    {{
                                        form.site_title || "Your website title"
                                    }}
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                {{
                                    form.site_description ||
                                    "Your website description will appear here."
                                }}
                            </div>
                        </div>

                        <div
                            class="mt-6 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10"
                        >
                            <div class="text-xs text-white/60">
                                Example plan price
                            </div>
                            <div class="mt-2 text-2xl font-semibold text-white">
                                {{ currencySymbol }} 29.00
                                <span class="text-sm text-white/60"
                                    > {{ $t('month') }} </span
                                >
                            </div>
                        </div>

                        <div class="mt-6 text-xs text-white/50">
                            Tip: keep the title short and the description under
                            ~160 characters for best SEO.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
