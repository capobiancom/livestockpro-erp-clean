<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    gateways: { type: Array, default: () => [] },
});

const page = usePage();

const form = ref({
    default_gateway:
        props.gateways.find((g) => g.is_default)?.gateway ??
        props.gateways[0]?.gateway ??
        null,
    gateways: props.gateways.map((g) => ({
        gateway: g.gateway,
        is_enabled: !!g.is_enabled,
        config: g.config ?? {},
    })),
});

const enabledGateways = computed(() =>
    form.value.gateways.filter((g) => g.is_enabled).map((g) => g.gateway),
);

function save() {
    Inertia.post(route("settings.payment-gateways.update"), form.value, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout title="Payment Gateways">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6"
            >
                <div
                    class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Payment Gateways
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 max-w-2xl">
                            Enable/disable gateways, set a default provider, and
                            securely store credentials used for subscription
                            payments.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                            @click="save"
                        >
                            Save changes
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Enabled gateways
                        </div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            {{ enabledGateways.length }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Only enabled gateways can be selected as default.
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Default gateway
                        </div>
                        <div class="mt-2">
                            <select
                                v-model="form.default_gateway"
                                class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                            >
                                <option
                                    v-for="gw in enabledGateways"
                                    :key="gw"
                                    :value="gw"
                                >
                                    {{ gw }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Used for new subscription payments by default.
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="text-xs font-medium text-gray-500">
                            Security
                        </div>
                        <div class="mt-1 text-sm text-gray-700">
                            Secrets are masked in the UI. Update only when
                            rotating keys.
                        </div>
                    </div>
                </div>

                <div
                    v-if="page.props.flash?.success"
                    class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.flash?.error"
                    class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm"
                >
                    {{ page.props.flash.error }}
                </div>
            </div>

            <!-- Gateways -->
            <div class="grid grid-cols-1 gap-6">
                <div
                    v-for="(gw, idx) in form.gateways"
                    :key="gw.gateway"
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                    <div
                        class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between border-b border-gray-100 p-5"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold uppercase"
                            >
                                {{ gw.gateway?.slice(0, 2) }}
                            </div>
                            <div>
                                <div
                                    class="text-base font-semibold text-gray-900 capitalize"
                                >
                                    {{ gw.gateway }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{
                                        gw.gateway === "cod"
                                            ? "Cash on delivery (manual collection). No credentials required."
                                            : "Configure credentials and endpoints for this provider."
                                    }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="
                                    gw.is_enabled
                                        ? 'bg-green-50 text-green-700 border border-green-200'
                                        : 'bg-gray-50 text-gray-600 border border-gray-200'
                                "
                            >
                                {{ gw.is_enabled ? "Enabled" : "Disabled" }}
                            </span>

                            <label
                                class="inline-flex items-center gap-2 text-sm text-gray-700"
                            >
                                <input
                                    type="checkbox"
                                    v-model="gw.is_enabled"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span> {{ $t('enable') }} </span>
                            </label>
                        </div>
                    </div>

                    <div class="p-5">
                        <div
                            v-if="gw.gateway === 'bkash'"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('mode') }} </label
                                >
                                <select
                                    v-model="gw.config.mode"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <option value="sandbox"> {{ $t('sandbox') }} </option>
                                    <option value="live"> {{ $t('live') }} </option>
                                </select>
                                <div class="mt-1 text-xs text-gray-500">
                                    If set to sandbox, bKash will use sandbox
                                    credentials/URLs.
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('sandbox_base_url') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.sandbox_base_url"
                                    placeholder="https://tokenized.sandbox.bka.sh/v1.2.0-beta"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('live_base_url') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.live_base_url"
                                    placeholder="https://tokenized.pay.bka.sh/v1.2.0-beta"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('app_key') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.app_key"
                                    placeholder="bKash app_key"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('app_secret') }} </label
                                >
                                <input
                                    type="password"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.app_secret"
                                    placeholder="••••••••"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('username') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.username"
                                    placeholder="bKash username"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('password') }} </label
                                >
                                <input
                                    type="password"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.password"
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>

                        <div
                            v-else-if="gw.gateway !== 'cod'"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('api_base_url') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.base_url"
                                    placeholder="https://api.example.com"
                                />
                                <div class="mt-1 text-xs text-gray-500">
                                    Provider API endpoint (if applicable).
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('merchant_app_key') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.key"
                                    placeholder="Key / App Key"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('secret') }} </label
                                >
                                <input
                                    type="password"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.secret"
                                    placeholder="••••••••"
                                />
                                <div class="mt-1 text-xs text-gray-500">
                                    Keep this value private.
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    > {{ $t('webhook_callback_url_optional') }} </label
                                >
                                <input
                                    type="text"
                                    class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="gw.config.webhook_url"
                                    placeholder="https://yourapp.com/webhook"
                                />
                            </div>
                        </div>

                        <div
                            v-if="gw.gateway !== 'cod'"
                            class="mt-4 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600"
                        >
                            {{
                                gw.gateway === "bkash"
                                    ? "bKash Checkout v2 requires base URLs + app credentials. Use sandbox for testing and live for production."
                                    : "These fields are generic. Map them to each provider’s required parameters when implementing the gateway API integration."
                            }}
                        </div>

                        <div
                            v-else
                            class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-700"
                        >
                            COD does not require API Base URL / keys / secrets /
                            webhooks. Payment will be collected manually later.
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                                @click="save"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
