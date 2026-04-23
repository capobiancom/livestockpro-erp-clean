<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    plan: { type: Object, required: true },
    features: { type: Array, default: () => [] },
    selectedFeatureIds: { type: Array, default: () => [] },
});

const page = usePage();

const form = ref({
    name: props.plan.name ?? "",
    slug: props.plan.slug ?? "",
    monthly_price_cents: props.plan.monthly_price_cents ?? 0,
    yearly_discount_percent: props.plan.yearly_discount_percent ?? 15,
    is_active: !!props.plan.is_active,
    sort_order: props.plan.sort_order ?? 0,
    feature_ids: props.selectedFeatureIds ?? [],
});

function submit() {
    Inertia.put(
        route("admin.subscription-plans.update", props.plan.id),
        form.value,
        {
            preserveScroll: true,
        },
    );
}

function destroyPlan() {
    if (!confirm(`Delete plan "${props.plan.name}"? This cannot be undone.`))
        return;
    Inertia.delete(route("admin.subscription-plans.destroy", props.plan.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout title="Edit Subscription Plan">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="py-6 sm:py-8">
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 border-b border-gray-100 p-6 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="h-5 w-5"
                                    >
                                        <path
                                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.75 5.25a.75.75 0 0 0-1.5 0v4.19c0 .2.08.39.22.53l2.72 2.72a.75.75 0 1 0 1.06-1.06l-2.5-2.5V7.5Z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h1
                                        class="text-xl font-semibold text-gray-900 sm:text-2xl"
                                    >
                                        Edit subscription plan
                                    </h1>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Update pricing, availability, and
                                        included features.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('admin.subscription-plans.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                Back
                            </Link>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50"
                                @click="destroyPlan"
                            >
                                Delete
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div
                            v-if="page.props.flash?.error"
                            class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                        >
                            {{ page.props.flash.error }}
                        </div>

                        <div
                            v-if="
                                page.props.errors &&
                                Object.keys(page.props.errors).length
                            "
                            class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                        >
                            Please fix the highlighted fields.
                        </div>

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div class="lg:col-span-2">
                                <div class="space-y-6">
                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div class="mb-4">
                                            <h2
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                Plan details
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-gray-600"
                                            >
                                                Basic information used to
                                                identify this plan.
                                            </p>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <div class="sm:col-span-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    > {{ $t('name') }} </label
                                                >
                                                <input
                                                    v-model="form.name"
                                                    type="text"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="Starter"
                                                />
                                                <div
                                                    v-if="
                                                        page.props.errors?.name
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{ page.props.errors.name }}
                                                </div>
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    > {{ $t('slug_optional') }} </label
                                                >
                                                <input
                                                    v-model="form.slug"
                                                    type="text"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="starter"
                                                />
                                                <div
                                                    class="mt-1 text-xs text-gray-500"
                                                >
                                                    Leave empty to keep current
                                                    slug or update it.
                                                </div>
                                                <div
                                                    v-if="
                                                        page.props.errors?.slug
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{ page.props.errors.slug }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div class="mb-4">
                                            <h2
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                Pricing
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-gray-600"
                                            >
                                                Set monthly price and yearly
                                                discount.
                                            </p>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    >Monthly price
                                                    (cents)</label
                                                >
                                                <input
                                                    v-model.number="
                                                        form.monthly_price_cents
                                                    "
                                                    type="number"
                                                    min="0"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="999"
                                                />
                                                <div
                                                    v-if="
                                                        page.props.errors
                                                            ?.monthly_price_cents
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors
                                                            .monthly_price_cents
                                                    }}
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    > {{ $t('yearly_discount') }} </label
                                                >
                                                <input
                                                    v-model.number="
                                                        form.yearly_discount_percent
                                                    "
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="15"
                                                />
                                                <div
                                                    v-if="
                                                        page.props.errors
                                                            ?.yearly_discount_percent
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors
                                                            .yearly_discount_percent
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-4 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-700"
                                        >
                                            <div class="flex items-start gap-2">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="currentColor"
                                                    class="mt-0.5 h-4 w-4 text-gray-500"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm10.28-3.53a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 1 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5h-4.19l1.72-1.72a.75.75 0 0 0 0-1.06Z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                <p>
                                                    Tip: store prices in cents
                                                    to avoid floating point
                                                    rounding issues.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div class="mb-4">
                                            <h2
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                Included features
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-gray-600"
                                            >
                                                Select the features that are
                                                available in this plan.
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl border border-gray-200 bg-white"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3"
                                            >
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    Features
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        form.feature_ids.length
                                                    }}
                                                    selected
                                                </div>
                                            </div>

                                            <div
                                                class="max-h-64 overflow-auto px-4 py-3"
                                            >
                                                <div
                                                    v-if="
                                                        !props.features ||
                                                        !props.features.length
                                                    "
                                                    class="text-sm text-gray-500"
                                                >
                                                    No features available.
                                                </div>

                                                <label
                                                    v-for="f in props.features"
                                                    :key="f.id"
                                                    class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-2 hover:bg-gray-50"
                                                >
                                                    <input
                                                        v-model="
                                                            form.feature_ids
                                                        "
                                                        :value="f.id"
                                                        type="checkbox"
                                                        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    />
                                                    <span class="min-w-0">
                                                        <span
                                                            class="block text-sm font-medium text-gray-900"
                                                        >
                                                            {{ f.name }}
                                                        </span>
                                                        <span
                                                            class="block text-xs text-gray-500"
                                                        >
                                                            Key: {{ f.key }}
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>

                                            <div
                                                class="border-t border-gray-100 bg-gray-50 px-4 py-3"
                                            >
                                                <div
                                                    class="flex flex-wrap items-center justify-between gap-3"
                                                >
                                                    <div
                                                        class="text-xs text-gray-600"
                                                    >
                                                        Click to check/uncheck.
                                                    </div>
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                                            @click="
                                                                form.feature_ids =
                                                                    []
                                                            "
                                                        >
                                                            Clear
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                                            @click="
                                                                form.feature_ids =
                                                                    props.features.map(
                                                                        (x) =>
                                                                            x.id,
                                                                    )
                                                            "
                                                        >
                                                            Select all
                                                        </button>
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="
                                                        page.props.errors
                                                            ?.feature_ids
                                                    "
                                                    class="mt-2 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors
                                                            .feature_ids
                                                    }}
                                                </div>
                                                <div
                                                    v-if="
                                                        page.props.errors?.[
                                                            'feature_ids.0'
                                                        ]
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors[
                                                            "feature_ids.0"
                                                        ]
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-1">
                                <div class="space-y-6">
                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div class="mb-4">
                                            <h2
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                Settings
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-gray-600"
                                            >
                                                Control visibility and ordering.
                                            </p>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    > {{ $t('status') }} </label
                                                >
                                                <select
                                                    v-model="form.is_active"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                                >
                                                    <option :value="true">
                                                        Active
                                                    </option>
                                                    <option :value="false">
                                                        Inactive
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="
                                                        page.props.errors
                                                            ?.is_active
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors
                                                            .is_active
                                                    }}
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700"
                                                    > {{ $t('sort_order') }} </label
                                                >
                                                <input
                                                    v-model.number="
                                                        form.sort_order
                                                    "
                                                    type="number"
                                                    min="0"
                                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0"
                                                />
                                                <div
                                                    v-if="
                                                        page.props.errors
                                                            ?.sort_order
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        page.props.errors
                                                            .sort_order
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div class="mb-4">
                                            <h2
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                Actions
                                            </h2>
                                        </div>

                                        <div class="flex flex-col gap-3">
                                            <button
                                                type="button"
                                                class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                                                @click="submit"
                                            >
                                                Save changes
                                            </button>

                                            <Link
                                                :href="
                                                    route(
                                                        'admin.subscription-plans.index',
                                                    )
                                                "
                                                class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                            >
                                                Cancel
                                            </Link>

                                            <button
                                                type="button"
                                                class="inline-flex w-full items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50"
                                                @click="destroyPlan"
                                            >
                                                Delete plan
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        class="rounded-2xl border border-gray-100 bg-gray-50 p-5 text-sm text-gray-700"
                                    >
                                        <div class="flex items-start gap-2">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                class="mt-0.5 h-4 w-4 text-gray-500"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm9.75-4.5a.75.75 0 0 0-.75.75v3.75a.75.75 0 0 0 .75.75h.008a.75.75 0 0 0 .75-.75V8.25A.75.75 0 0 0 12 7.5Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <p>
                                                Changes are saved immediately
                                                after you click “Save changes”.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
