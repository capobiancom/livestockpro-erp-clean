<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center shadow-md"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M4 3a2 2 0 00-2 2v2a2 2 0 002 2h1v6H4a2 2 0 00-2 2v1h16v-1a2 2 0 00-2-2h-1V9h1a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 6h6v6H7V9z"
                        />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-900">
                        Plan & Features
                    </div>
                    <div class="text-xs text-gray-500">
                        Review your current plan and what’s included
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Hero / Summary -->
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50"
                ></div>
                <div class="relative p-6 md:p-8">
                    <div
                        class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                    >
                        <div class="max-w-2xl">
                            <div
                                class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-indigo-600"
                                ></span>
                                Farm Owner View
                            </div>

                            <h1
                                class="mt-3 text-2xl md:text-3xl font-bold tracking-tight text-gray-900"
                            >
                                Your subscription plan at a glance
                            </h1>
                            <p class="mt-2 text-sm md:text-base text-gray-600">
                                See your current plan, renewal date, and the
                                features enabled for your farm. You can compare
                                with other plans anytime.
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-gray-200 bg-white/70 backdrop-blur px-5 py-4 shadow-sm"
                        >
                            <div class="text-xs font-medium text-gray-500">
                                Current Plan
                            </div>
                            <div
                                class="mt-1 flex items-center gap-2 text-lg font-semibold text-gray-900"
                            >
                                <span v-if="currentPlan">{{
                                    currentPlan.name
                                }}</span>
                                <span v-else class="text-gray-700"
                                    >No active plan</span
                                >
                                <span
                                    v-if="currentPlan"
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200"
                                    >Active</span
                                >
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200"
                                    >Inactive</span
                                >
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div
                                    class="rounded-xl border border-gray-200 bg-white px-3 py-2"
                                >
                                    <div class="text-[11px] text-gray-500">
                                        {{
                                            billingPeriod === "yearly"
                                                ? "Yearly"
                                                : "Monthly"
                                        }}
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        <span v-if="currentPlan">
                                            <span
                                                v-if="
                                                    billingPeriod ===
                                                        'yearly' &&
                                                    currentPlan.yearly_price_cents !==
                                                        undefined
                                                "
                                            >
                                                {{
                                                    formatMoney(
                                                        currentPlan.yearly_price_cents,
                                                    )
                                                }}
                                            </span>
                                            <span v-else>
                                                {{
                                                    formatMoney(
                                                        currentPlan.monthly_price_cents,
                                                    )
                                                }}
                                            </span>
                                        </span>
                                        <span v-else>-</span>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-200 bg-white px-3 py-2"
                                >
                                    <div class="text-[11px] text-gray-500">
                                        Renews on
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        <span v-if="subscriptionEndsOn">{{
                                            subscriptionEndsOn
                                        }}</span>
                                        <span v-else>-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <Link
                                    :href="route('billing.index')"
                                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Manage Billing
                                </Link>
                                <a
                                    href="#compare"
                                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    Compare Plans
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center ring-1 ring-indigo-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3a1 1 0 00.293.707l2 2a1 1 0 101.414-1.414L11 9.586V7z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <div
                                        class="text-xs font-medium text-gray-500"
                                    >
                                        Status
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        <span v-if="currentPlan"
                                            >Subscription active</span
                                        >
                                        <span v-else
                                            >No active subscription</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center ring-1 ring-emerald-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <div
                                        class="text-xs font-medium text-gray-500"
                                    >
                                        Enabled features
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ currentFeatures.length }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center ring-1 ring-blue-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2 0h12v2H4V5zm0 4h12v6H4V9z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <div
                                        class="text-xs font-medium text-gray-500"
                                    >
                                        Compare plans
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ allPlans.length }} available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current features -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900">
                                Included features
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                Features currently enabled for your farm.
                            </div>
                        </div>

                        <div class="w-full max-w-xs">
                            <div class="relative">
                                <input
                                    v-model="featureQuery"
                                    type="text"
                                    placeholder="Search features..."
                                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12.9 14.32a8 8 0 111.414-1.414l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="!currentPlan" class="rounded-xl bg-amber-50 p-4">
                        <div class="text-sm font-semibold text-amber-800">
                            No active subscription
                        </div>
                        <div class="mt-1 text-sm text-amber-700">
                            Subscribe to a plan to unlock features for your
                            farm.
                        </div>
                        <div class="mt-3">
                            <Link
                                :href="route('billing.index')"
                                class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700"
                            >
                                Go to Billing
                            </Link>
                        </div>
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"
                    >
                        <div
                            v-for="f in filteredCurrentFeatures"
                            :key="f.key"
                            class="group rounded-2xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div
                                        class="flex items-center gap-2 text-sm font-semibold text-gray-900"
                                    >
                                        <span
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </span>
                                        <span class="truncate">{{
                                            f.name || f.key
                                        }}</span>
                                    </div>
                                    <div
                                        class="mt-2 text-sm text-gray-600 line-clamp-3"
                                    >
                                        {{
                                            f.description ||
                                            "This feature is included in your current plan."
                                        }}
                                    </div>
                                </div>

                                <span
                                    class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-[11px] font-semibold text-gray-700 ring-1 ring-gray-200"
                                >
                                    {{ f.key }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="filteredCurrentFeatures.length === 0"
                            class="col-span-full rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center"
                        >
                            <div class="text-sm font-semibold text-gray-900">
                                No matching features
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                Try a different search term.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compare plans -->
            <div
                id="compare"
                class="rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900">
                                Compare plans
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                Explore what each plan includes.
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                @click="billingPeriod = 'monthly'"
                                :class="[
                                    'rounded-xl px-4 py-2 text-sm font-semibold ring-1 transition',
                                    billingPeriod === 'monthly'
                                        ? 'bg-indigo-600 text-white ring-indigo-600'
                                        : 'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50',
                                ]"
                            >
                                Monthly
                            </button>
                            <button
                                @click="billingPeriod = 'yearly'"
                                :class="[
                                    'rounded-xl px-4 py-2 text-sm font-semibold ring-1 transition',
                                    billingPeriod === 'yearly'
                                        ? 'bg-indigo-600 text-white ring-indigo-600'
                                        : 'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50',
                                ]"
                            >
                                Yearly
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div
                        class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-stretch"
                    >
                        <div
                            v-for="p in allPlans"
                            :key="p.slug"
                            class="relative rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition"
                            :class="[
                                currentPlan?.slug === p.slug
                                    ? 'ring-2 ring-indigo-600'
                                    : '',
                            ]"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ p.name }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Best for growing farms that need
                                        reliable tools.
                                    </div>
                                </div>

                                <span
                                    v-if="currentPlan?.slug === p.slug"
                                    class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-200"
                                >
                                    Current
                                </span>
                            </div>

                            <div class="mt-4">
                                <div class="text-3xl font-bold text-gray-900">
                                    <span v-if="billingPeriod === 'monthly'">
                                        {{ formatMoney(p.monthly_price_cents) }}
                                    </span>
                                    <span v-else>
                                        {{ formatMoney(p.yearly_price_cents) }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    <span v-if="billingPeriod === 'monthly'"
                                        >/ month</span
                                    >
                                    <span v-else>/ year</span>
                                    <span
                                        v-if="
                                            billingPeriod === 'yearly' &&
                                            p.yearly_discount_percent
                                        "
                                        class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200"
                                    >
                                        Save {{ p.yearly_discount_percent }}%
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 space-y-2">
                                <div
                                    v-for="f in p.features.slice(0, 6)"
                                    :key="f.key"
                                    class="flex items-start gap-2 text-sm text-gray-700"
                                >
                                    <span
                                        class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3.5 w-3.5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-medium truncate">
                                            {{ f.name || f.key }}
                                        </div>
                                        <div
                                            v-if="f.description"
                                            class="text-xs text-gray-500 line-clamp-2"
                                        >
                                            {{ f.description }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="p.features.length > 6"
                                    class="text-xs text-gray-500"
                                >
                                    + {{ p.features.length - 6 }} more features
                                </div>
                            </div>

                            <div class="mt-6">
                                <Link
                                    :href="route('billing.index')"
                                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50"
                                >
                                    View in Billing
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl bg-gray-50 p-4">
                        <div class="text-sm font-semibold text-gray-900">
                            Need help choosing?
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            Start with the plan that matches your farm size and
                            upgrade anytime from the Billing page.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    currentPlan: { type: Object, default: null },
    currentFeatures: { type: Array, default: () => [] },
    allPlans: { type: Array, default: () => [] },
    subscriptionEndsOn: { type: String, default: null },
    currentBillingPeriod: { type: String, default: null }, // 'monthly' | 'yearly'
});

const page = usePage();
const currencySymbol = computed(() => {
    // Prefer super-admin website currency for public-facing plan/pricing UI
    return (
        page.props.value?.website_currency_symbol ??
        page.props.value?.app_currency_symbol ??
        "$"
    );
});

const billingPeriod = ref(props.currentBillingPeriod || "monthly");
const featureQuery = ref("");

const filteredCurrentFeatures = computed(() => {
    const q = featureQuery.value.trim().toLowerCase();
    if (!q) return props.currentFeatures;

    return props.currentFeatures.filter((f) => {
        const name = (f.name ?? "").toLowerCase();
        const key = (f.key ?? "").toLowerCase();
        const desc = (f.description ?? "").toLowerCase();
        return name.includes(q) || key.includes(q) || desc.includes(q);
    });
});

function formatMoney(cents) {
    if (cents === null || cents === undefined) return "-";
    const amount = Number(cents) / 100;

    // Keep it simple and consistent; avoid Intl currency code mismatch (BDT vs $)
    return `${currencySymbol.value}${amount.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    })}`;
}
</script>
