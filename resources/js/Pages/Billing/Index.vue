<script setup>
import Layout from "@/Layouts/AppLayout.vue";
import { computed, onMounted, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link, usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    currentSubscription: { type: Object, default: null },
    plans: { type: Array, default: () => [] },
    enabledGateways: { type: Array, default: () => [] },
    defaultGateway: { type: String, default: null },
});

const page = usePage();

const isSubscriptionInactivatedByAdmin = computed(() => {
    // Backend blocks billing actions when there exists a cancelled subscription record.
    // We infer this state on the UI by checking if the current subscription has cancelled_at.
    // (If you later add an explicit flag from backend, switch to that.)
    return !!props.currentSubscription?.cancelled_at;
});

const form = ref({
    // Prefer current subscription values; fallback to first available plan.
    subscription_plan_id:
        props.currentSubscription?.subscription_plan_id ??
        props.plans?.[0]?.id ??
        null,
    billing_period: props.currentSubscription?.billing_period ?? "monthly",
    gateway: props.defaultGateway ?? props.enabledGateways?.[0] ?? null,
});

const selectedPlan = computed(() =>
    props.plans.find((p) => p.id === form.value.subscription_plan_id),
);

const currentPlan = computed(() =>
    props.plans.find(
        (p) => p.id === props.currentSubscription?.subscription_plan_id,
    ),
);

const isDowngradeOrSamePlan = computed(() => {
    // Only enforce when current subscription is active (not expired) and not admin-inactivated.
    if (isSubscriptionInactivatedByAdmin.value) return false;

    const hasActive =
        !!props.currentSubscription?.ends_on &&
        new Date(props.currentSubscription.ends_on) >= new Date();

    if (!hasActive) return false;

    const currentValue = Number(currentPlan.value?.monthly_price_cents ?? 0);
    const requestedValue = Number(selectedPlan.value?.monthly_price_cents ?? 0);

    // Block if requested plan value is same or less than current plan value.
    return requestedValue <= currentValue;
});

function formatDate(d) {
    if (!d) return "-";
    try {
        return new Date(d).toLocaleDateString();
    } catch {
        return d;
    }
}

function formatMoney(cents) {
    if (cents === null || cents === undefined) return "-";
    const amount = Number(cents) / 100;

    const symbol =
        page.props.value?.website_currency_symbol ??
        page.props.value?.app_currency_symbol ??
        "$";

    return `${symbol}${amount.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    })}`;
}

function submit() {
    if (isSubscriptionInactivatedByAdmin.value) return;
    if (isDowngradeOrSamePlan.value) return;

    Inertia.post(route("billing.subscribe"), form.value, {
        preserveScroll: true,
    });
}

async function initiatePayment(invoiceId, gateway) {
    try {
        const res = await fetch(route("payments.initiate"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": page.props?.csrf_token,
                Accept: "application/json",
            },
            body: JSON.stringify({
                invoice_id: invoiceId,
                gateway: gateway,
            }),
        });

        const contentType = res.headers.get("content-type") || "";
        const isJson = contentType.includes("application/json");

        const data = isJson ? await res.json() : null;

        if (!res.ok) {
            // If backend returned HTML (e.g. login page / 419 / 500), provide a useful error.
            const fallbackText = !isJson
                ? await res.text().catch(() => "")
                : "";
            throw new Error(
                data?.message ||
                    (fallbackText
                        ? "Payment initiation failed (non-JSON response)."
                        : "") ||
                    "Payment initiation failed.",
            );
        }

        if (data?.redirect_url) {
            // If redirecting to an external gateway, Inertia won't handle it; use full page navigation.
            window.location.href = data.redirect_url;
            return;
        }

        throw new Error("Payment initiation did not return a redirect URL.");
    } catch (e) {
        Inertia.reload({
            preserveScroll: true,
            onSuccess: () => {
                // show error via flash if backend sets it; otherwise fallback alert
                // eslint-disable-next-line no-alert
                alert(e?.message || "Payment initiation failed.");
            },
        });
    }
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const invoice = params.get("invoice");
    const gateway = params.get("gateway");

    if (invoice) {
        initiatePayment(Number(invoice), gateway || form.value.gateway);
    }
});
</script>

<template>
    <Layout title="Billing">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header / Overview -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6"
            >
                <div
                    class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Subscription & Billing
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 max-w-2xl">
                            Manage your plan, billing period, and payment method
                            for your farm subscription.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('settings.payment-gateways.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Payment gateways
                        </Link>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Status
                        </div>
                        <div class="mt-1 font-semibold text-gray-900">
                            <span
                                v-if="isSubscriptionInactivatedByAdmin"
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200"
                            >
                                Inactivated
                            </span>
                            <span
                                v-else-if="
                                    currentSubscription &&
                                    currentSubscription.ends_on &&
                                    new Date(currentSubscription.ends_on) >=
                                        new Date()
                                "
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-green-50 text-green-700 border border-green-200"
                            >
                                Active
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200"
                            >
                                Expired / Not active
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Access may be limited if subscription is inactive.
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Next billing date
                        </div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            {{
                                formatDate(currentSubscription?.next_billing_on)
                            }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Based on your current plan and billing period.
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Current plan
                        </div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            {{
                                plans.find(
                                    (p) =>
                                        p.id ===
                                        currentSubscription?.subscription_plan_id,
                                )?.name ??
                                currentSubscription?.subscription_plan_id ??
                                "-"
                            }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{
                                currentSubscription?.billing_period
                                    ? `Billing period: ${currentSubscription.billing_period}`
                                    : "Billing period: -"
                            }}
                        </div>
                    </div>
                </div>

                <div
                    v-if="isSubscriptionInactivatedByAdmin"
                    class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 text-sm"
                >
                    Your subscription has been inactivated by an administrator.
                    Billing changes are disabled until it is activated again.
                </div>

                <div
                    v-if="page.props.flash?.error"
                    class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm"
                >
                    {{ page.props.flash.error }}
                </div>
                <div
                    v-if="page.props.flash?.success"
                    class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm"
                >
                    {{ page.props.flash.success }}
                </div>
            </div>

            <!-- Plan selection -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6"
            >
                <div
                    class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Choose a plan
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Select a plan, billing period, and payment gateway
                            to continue.
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form -->
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Plan</label
                                >
                                <select
                                    v-model="form.subscription_plan_id"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option
                                        v-for="p in plans"
                                        :key="p.id"
                                        :value="p.id"
                                    >
                                        {{ p.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Billing period</label
                                >
                                <select
                                    v-model="form.billing_period"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">
                                        Yearly (15% discount)
                                    </option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Payment gateway</label
                                >
                                <select
                                    v-model="form.gateway"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option
                                        v-for="gw in enabledGateways"
                                        :key="gw"
                                        :value="gw"
                                    >
                                        {{
                                            gw === "cod"
                                                ? "Cash on delivery"
                                                : gw
                                        }}
                                    </option>
                                </select>
                                <div class="mt-1 text-xs text-gray-500">
                                    Only enabled gateways are shown here. Enable
                                    more in Settings → Payment gateways.
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div
                                v-if="isDowngradeOrSamePlan"
                                class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 text-sm"
                            >
                                You already have this plan (or a higher plan).
                                Please choose a higher plan to upgrade.
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @click="submit"
                                    :disabled="
                                        isSubscriptionInactivatedByAdmin ||
                                        isDowngradeOrSamePlan ||
                                        !form.subscription_plan_id ||
                                        !form.gateway
                                    "
                                >
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-gray-200 p-5">
                            <div class="text-sm font-semibold text-gray-900">
                                Price summary
                            </div>

                            <div v-if="selectedPlan" class="mt-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Monthly</span
                                    >
                                    <span
                                        class="text-sm font-semibold text-gray-900"
                                        >{{
                                            formatMoney(
                                                selectedPlan.monthly_price_cents,
                                            )
                                        }}</span
                                    >
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Yearly</span
                                    >
                                    <span
                                        class="text-sm font-semibold text-gray-900"
                                        >{{
                                            formatMoney(
                                                selectedPlan.yearly_price_cents,
                                            )
                                        }}</span
                                    >
                                </div>

                                <div
                                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600"
                                >
                                    Yearly price includes
                                    {{ selectedPlan.yearly_discount_percent }}%
                                    discount.
                                </div>
                            </div>

                            <div v-else class="mt-4 text-sm text-gray-500">
                                Select a plan to see pricing.
                            </div>
                        </div>

                        <div
                            class="mt-4 rounded-2xl border border-gray-200 bg-white p-5"
                        >
                            <div class="text-sm font-semibold text-gray-900">
                                Notes
                            </div>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li>
                                    • You can change plan later from this page.
                                </li>
                                <li>
                                    • If your subscription is expired, you can
                                    still access billing to renew.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
