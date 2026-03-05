<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";

function formatMoney(cents, currency) {
    const amount = (Number(cents || 0) / 100) * 1;
    try {
        return new Intl.NumberFormat(undefined, {
            style: "currency",
            currency: currency || "USD",
            maximumFractionDigits: 2,
        }).format(amount);
    } catch (e) {
        // Fallback to super-admin configured symbol if Intl doesn't support the code
        return `${currencySymbol.value} ${amount.toFixed(2)}`;
    }
}

function scrollToPlans() {
    const el = document.getElementById("plans");
    if (!el) return;
    el.scrollIntoView({ behavior: "smooth", block: "start" });
}

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    allPlans: {
        type: Array,
        default: () => [],
    },
    websiteSettings: {
        type: Object,
        default: () => ({}),
    },
});

const modules = [
    {
        title: "Livestock",
        description:
            "Animals, breeds, herds, reproduction, calves, and complete lifecycle tracking.",
        items: [
            "Animals & Herds",
            "Breeds",
            "Reproduction Records",
            "AI & Pregnancies",
            "Calving & Calves",
        ],
        href: "/animals",
        gradient: "from-emerald-500 to-teal-500",
    },
    {
        title: "Feeding",
        description:
            "Plan feed types, record feeding, and analyze cost per cow and consumption trends.",
        items: [
            "Feeding Records",
            "Feed Types",
            "Cost Analysis",
            "Consumption",
        ],
        href: "/feedings",
        gradient: "from-amber-500 to-orange-500",
    },
    {
        title: "Health",
        description:
            "Vaccinations, health issues, treatments, and disease tracking with due reports.",
        items: ["Vaccinations", "Health Events", "Treatments", "Due Reports"],
        href: "/vaccinations",
        gradient: "from-sky-500 to-indigo-500",
    },
    {
        title: "Inventory",
        description:
            "Medicines, stock movements, suppliers, and low-stock/expiry alerts.",
        items: ["Stock", "Medicines", "Suppliers", "Alerts"],
        href: "/inventory",
        gradient: "from-fuchsia-500 to-pink-500",
    },
    {
        title: "Finance",
        description:
            "Sales, purchases, expenses, accounting, and financial statements in one place.",
        items: ["Sales & Purchases", "Expenses", "Journal", "P&L / Cashflow"],
        href: "/sales",
        gradient: "from-violet-500 to-purple-500",
    },
    {
        title: "HR & Operations",
        description:
            "Staff, attendance, payroll, logistics, and day-to-day farm operations.",
        items: ["Employees", "Attendance", "Payroll", "Logistics"],
        href: "/employees",
        gradient: "from-slate-600 to-slate-800",
    },
];

const billingPeriod = ref("monthly");

const currency = computed(() => props.websiteSettings?.currency || "USD");
const currencySymbol = computed(
    () => usePage().props.value?.website_currency_symbol ?? "$",
);

const stats = [
    { label: "Modules", value: "12+" },
    { label: "Reports", value: "20+" },
    { label: "Role-based access", value: "Built-in" },
    { label: "Subscription features", value: "Supported" },
];

const flashSuccess = computed(() => usePage().props.flash?.success);
const flashError = computed(() => usePage().props.flash?.error);

const showDemoModal = ref(false);

const demoForm = useForm({
    name: "",
    email: "",
    phone: "",
    company: "",
    country: "",
    preferred_date: "",
    preferred_time: "",
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || "Asia/Dhaka",
    message:
        "Hello AgroSaas team,\n\nI would like to request a product demo. Please contact me with available time slots.\n\nThanks,\n",
});

function submitDemoRequest() {
    demoForm.post(route("demo-requests.store"), {
        preserveScroll: true,
        onSuccess: () => {
            demoForm.reset();
            showDemoModal.value = false;
        },
    });
}

const faqs = [
    {
        q: "Is AgroSaas suitable for small farms?",
        a: "Yes. Start with the modules you need and scale as your operation grows.",
    },
    {
        q: "Can I control which modules are available?",
        a: "Yes. Modules can be enabled/disabled via subscription features and roles.",
    },
    {
        q: "Do you provide reports and analytics?",
        a: "Yes. The platform includes dashboards and a growing set of operational and financial reports.",
    },
];
</script>

<template>
    <Head
        :title="`${props.websiteSettings?.site_title || 'LivestockPro SaaS'} — Farm Management Platform`"
    />

    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Background -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-24 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/25 blur-3xl"
            />
            <div
                class="absolute -bottom-24 right-0 h-[520px] w-[520px] rounded-full bg-emerald-500/20 blur-3xl"
            />
            <div
                class="absolute inset-0 opacity-[0.08]"
                style="
                    background-image: radial-gradient(
                        rgba(255, 255, 255, 0.35) 1px,
                        transparent 1px
                    );
                    background-size: 18px 18px;
                "
            />
        </div>

        <!-- Top Nav -->
        <header class="relative">
            <div class="mx-auto max-w-7xl px-6 py-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <Link href="/" class="inline-flex items-center gap-3">
                        <img
                            v-if="props.websiteSettings?.logo_url"
                            :src="props.websiteSettings.logo_url"
                            alt="Website logo"
                            class="h-10 w-10 rounded-xl object-contain bg-white/10 ring-1 ring-white/15"
                        />
                        <ApplicationLogo
                            v-else
                            class="h-10 w-auto text-white"
                        />
                        <span class="text-base font-semibold tracking-wide">
                            {{
                                props.websiteSettings?.site_title ||
                                "LivestockPro ERP SaaS"
                            }}
                        </span>
                    </Link>

                    <nav v-if="canLogin" class="flex items-center gap-2">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-xl bg-white/10 px-4 py-2 text-sm font-medium ring-1 ring-white/15 transition hover:bg-white/15"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-xl px-4 py-2 text-sm font-medium text-white/90 ring-1 ring-white/10 transition hover:bg-white/10"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main class="relative">
            <div
                v-if="flashSuccess"
                class="mx-auto max-w-7xl px-6 pt-6 lg:px-8"
            >
                <div
                    class="rounded-2xl border border-emerald-200/20 bg-emerald-500/10 px-4 py-3 text-emerald-100 ring-1 ring-emerald-500/20"
                >
                    {{ flashSuccess }}
                </div>
            </div>

            <div v-if="flashError" class="mx-auto max-w-7xl px-6 pt-6 lg:px-8">
                <div
                    class="rounded-2xl border border-rose-200/20 bg-rose-500/10 px-4 py-3 text-rose-100 ring-1 ring-rose-500/20"
                >
                    {{ flashError }}
                </div>
            </div>
            <section class="mx-auto max-w-7xl px-6 pt-10 lg:px-8 lg:pt-16">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/80 ring-1 ring-white/15"
                        >
                            <span
                                class="inline-block h-2 w-2 rounded-full bg-emerald-400"
                            />
                            Modern farm operations suite
                        </div>

                        <h1
                            class="mt-5 text-4xl font-semibold tracking-tight text-white sm:text-5xl"
                        >
                            Professional farm management for
                            <span
                                class="bg-gradient-to-r from-emerald-300 via-sky-300 to-indigo-300 bg-clip-text text-transparent"
                                >livestock, finance, and operations</span
                            >.
                        </h1>

                        <p class="mt-5 text-base/7 text-white/75">
                            {{
                                props.websiteSettings?.site_description ||
                                "AgroSaas helps you run your farm with clarity: livestock lifecycle, feeding, health, inventory, finance, HR, and reporting—built for speed and control."
                            }}
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                @click="showDemoModal = true"
                                class="rounded-2xl bg-gradient-to-r from-emerald-400 to-sky-400 px-5 py-3 text-sm font-semibold text-slate-950 shadow-[0_20px_60px_-30px_rgba(16,185,129,0.45)] transition hover:from-emerald-300 hover:to-sky-300"
                            >
                                Request a demo
                            </button>
                            <Link
                                :href="route('register')"
                                class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-[0_20px_60px_-30px_rgba(255,255,255,0.35)] transition hover:bg-slate-100"
                            >
                                Get started
                            </Link>
                            <Link
                                :href="route('login')"
                                class="rounded-2xl bg-white/10 px-5 py-3 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15"
                            >
                                Sign in
                            </Link>
                            <button
                                type="button"
                                @click="scrollToPlans"
                                class="rounded-2xl px-5 py-3 text-sm font-semibold text-white/90 ring-1 ring-white/10 transition hover:bg-white/10"
                            >
                                View plans
                            </button>
                        </div>

                        <dl class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div
                                v-for="s in stats"
                                :key="s.label"
                                class="rounded-2xl bg-white/5 px-4 py-3 ring-1 ring-white/10"
                            >
                                <dt class="text-xs text-white/60">
                                    {{ s.label }}
                                </dt>
                                <dd class="mt-1 text-sm font-semibold">
                                    {{ s.value }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="relative">
                        <div
                            class="rounded-3xl bg-gradient-to-br from-white/10 to-white/5 p-1 ring-1 ring-white/15"
                        >
                            <div
                                class="rounded-[22px] bg-slate-950/40 p-6 backdrop-blur"
                            >
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div
                                        class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                                    >
                                        <div
                                            class="text-sm font-semibold text-white"
                                        >
                                            Dashboards
                                        </div>
                                        <div class="mt-2 text-sm text-white/70">
                                            Productivity and key metrics at a
                                            glance.
                                        </div>
                                    </div>
                                    <div
                                        class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                                    >
                                        <div
                                            class="text-sm font-semibold text-white"
                                        >
                                            Reports
                                        </div>
                                        <div class="mt-2 text-sm text-white/70">
                                            Operational + financial reporting.
                                        </div>
                                    </div>
                                    <div
                                        class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                                    >
                                        <div
                                            class="text-sm font-semibold text-white"
                                        >
                                            Role-based access
                                        </div>
                                        <div class="mt-2 text-sm text-white/70">
                                            Secure permissions and workflows.
                                        </div>
                                    </div>
                                    <div
                                        class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                                    >
                                        <div
                                            class="text-sm font-semibold text-white"
                                        >
                                            Subscription features
                                        </div>
                                        <div class="mt-2 text-sm text-white/70">
                                            Enable modules per plan.
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="mt-6 rounded-2xl bg-gradient-to-r from-emerald-500/15 via-sky-500/10 to-indigo-500/15 p-5 ring-1 ring-white/10"
                                >
                                    <div class="text-sm font-semibold">
                                        Built for real farm workflows
                                    </div>
                                    <div class="mt-2 text-sm text-white/70">
                                        From animal health events to accounting
                                        statements—everything stays connected.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-sky-500/20 blur-3xl"
                        />
                    </div>
                </div>
            </section>

            <!-- Modules -->
            <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="flex items-end justify-between gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">
                            Modules designed for your operation
                        </h2>
                        <p class="mt-2 text-sm text-white/70">
                            Each module is built to be fast, consistent, and
                            easy to use—so your team can focus on the farm.
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="m in modules"
                        :key="m.title"
                        class="group relative overflow-hidden rounded-3xl bg-white/5 p-6 ring-1 ring-white/10 transition hover:bg-white/7"
                    >
                        <div
                            class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-gradient-to-br opacity-40 blur-2xl"
                            :class="`bg-gradient-to-br ${m.gradient}`"
                        />
                        <div class="relative">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-white"
                                    >
                                        {{ m.title }}
                                    </h3>
                                    <p class="mt-2 text-sm text-white/70">
                                        {{ m.description }}
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl bg-white/10 p-2 ring-1 ring-white/10"
                                >
                                    <svg
                                        class="h-5 w-5 text-white/80"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <ul class="mt-5 space-y-2 text-sm text-white/70">
                                <li
                                    v-for="it in m.items"
                                    :key="it"
                                    class="flex items-center gap-2"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-300/80"
                                    />
                                    <span>{{ it }}</span>
                                </li>
                            </ul>

                            <div class="mt-6">
                                <Link
                                    :href="m.href"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 transition hover:bg-white/15"
                                >
                                    Explore
                                    <svg
                                        class="h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why -->
            <section class="mx-auto max-w-7xl px-6 pb-16 lg:px-8">
                <div
                    class="grid gap-6 rounded-3xl bg-white/5 p-8 ring-1 ring-white/10 lg:grid-cols-3"
                >
                    <div>
                        <h2 class="text-2xl font-semibold text-white">
                            Why teams choose AgroSaas
                        </h2>
                        <p class="mt-2 text-sm text-white/70">
                            A clean UI, consistent workflows, and the controls
                            you need to run operations confidently.
                        </p>
                    </div>

                    <div class="grid gap-4 lg:col-span-2 sm:grid-cols-2">
                        <div
                            class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                        >
                            <div class="text-sm font-semibold">
                                Consistent workflows
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                Similar patterns across modules reduce training
                                time.
                            </div>
                        </div>
                        <div
                            class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                        >
                            <div class="text-sm font-semibold">
                                Feature gating
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                Enable modules per subscription plan and role.
                            </div>
                        </div>
                        <div
                            class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                        >
                            <div class="text-sm font-semibold">
                                Reporting built-in
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                Dashboards and reports for operations and
                                finance.
                            </div>
                        </div>
                        <div
                            class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                        >
                            <div class="text-sm font-semibold">
                                Secure by default
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                Role-based access and best practices.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Plans -->
            <section
                id="plans"
                class="mx-auto max-w-7xl scroll-mt-24 px-6 pb-16 lg:px-8"
            >
                <div class="flex items-end justify-between gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">
                            Plans for every farm size
                        </h2>
                        <p class="mt-2 text-sm text-white/70">
                            Start small, scale anytime. Choose a plan that fits
                            your operation and unlock modules as you grow.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div
                            class="inline-flex items-center rounded-2xl bg-white/5 p-1 ring-1 ring-white/10"
                        >
                            <button
                                type="button"
                                @click="billingPeriod = 'monthly'"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                                :class="
                                    billingPeriod === 'monthly'
                                        ? 'bg-white text-slate-900'
                                        : 'text-white/80 hover:bg-white/10'
                                "
                            >
                                Monthly
                            </button>
                            <button
                                type="button"
                                @click="billingPeriod = 'yearly'"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                                :class="
                                    billingPeriod === 'yearly'
                                        ? 'bg-white text-slate-900'
                                        : 'text-white/80 hover:bg-white/10'
                                "
                            >
                                Yearly
                            </button>
                        </div>

                        <Link
                            v-if="canRegister && !$page.props.auth.user"
                            :href="route('register')"
                            class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                        >
                            Get started
                        </Link>
                        <Link
                            :href="route('plan.index')"
                            class="rounded-2xl bg-white/10 px-5 py-3 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15"
                        >
                            Compare all plans
                        </Link>
                    </div>
                </div>

                <div
                    v-if="props.allPlans.length"
                    class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="p in props.allPlans"
                        :key="p.slug"
                        class="group relative overflow-hidden rounded-3xl bg-white/5 p-6 ring-1 ring-white/10 transition hover:bg-white/7"
                    >
                        <div class="relative">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <h3
                                        class="text-lg font-semibold text-white truncate"
                                    >
                                        {{ p.name }}
                                    </h3>
                                    <p class="mt-2 text-sm text-white/70">
                                        {{ (p.features || []).length }} features
                                        included.
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl bg-white/10 p-2 ring-1 ring-white/10"
                                >
                                    <svg
                                        class="h-5 w-5 text-white/80"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-5 flex items-end gap-2">
                                <div class="text-3xl font-semibold text-white">
                                    {{
                                        billingPeriod === "yearly"
                                            ? p.yearly_price_cents
                                                ? formatMoney(
                                                      p.yearly_price_cents,
                                                      currency,
                                                  )
                                                : "—"
                                            : p.monthly_price_cents
                                              ? formatMoney(
                                                    p.monthly_price_cents,
                                                    currency,
                                                )
                                              : "Free"
                                    }}
                                </div>
                                <div class="pb-1 text-sm text-white/60">
                                    /
                                    {{
                                        billingPeriod === "yearly"
                                            ? "year"
                                            : "month"
                                    }}
                                </div>
                            </div>

                            <div
                                v-if="p.yearly_discount_percent"
                                class="mt-2 inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-500/20"
                            >
                                Save {{ p.yearly_discount_percent }}% yearly
                            </div>

                            <ul class="mt-5 space-y-2 text-sm text-white/70">
                                <li
                                    v-for="f in p.features || []"
                                    :key="f.key"
                                    class="flex items-center gap-2"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-300/80"
                                    />
                                    <span class="truncate">{{
                                        f.name || f.key
                                    }}</span>
                                </li>
                            </ul>

                            <div class="mt-6">
                                <Link
                                    :href="route('plan.index')"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 transition hover:bg-white/15"
                                >
                                    View details
                                    <svg
                                        class="h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="mt-8 rounded-3xl bg-white/5 p-6 ring-1 ring-white/10"
                >
                    <div class="text-sm font-semibold text-white">
                        Plans will appear here
                    </div>
                    <div class="mt-2 text-sm text-white/70">
                        No active plans found in the database yet.
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section class="mx-auto max-w-7xl px-6 pb-20 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-2">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">
                            Frequently asked questions
                        </h2>
                        <p class="mt-2 text-sm text-white/70">
                            Quick answers about modules, access, and reporting.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="f in faqs"
                            :key="f.q"
                            class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10"
                        >
                            <div class="text-sm font-semibold text-white">
                                {{ f.q }}
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                {{ f.a }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-white/10">
                <div
                    class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-10 text-sm text-white/60 lg:flex-row lg:items-center lg:justify-between lg:px-8"
                >
                    <div class="flex items-center gap-3">
                        <img
                            v-if="props.websiteSettings?.logo_url"
                            :src="props.websiteSettings.logo_url"
                            alt="Website logo"
                            class="h-8 w-8 rounded-lg object-contain bg-white/10 ring-1 ring-white/15"
                        />
                        <ApplicationLogo
                            v-else
                            class="h-8 w-auto text-white/80"
                        />
                        <span>{{
                            props.websiteSettings?.site_title || "AgroSaas"
                        }}</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('login')"
                            class="rounded-xl px-3 py-2 ring-1 ring-white/10 transition hover:bg-white/10"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-xl px-3 py-2 ring-1 ring-white/10 transition hover:bg-white/10"
                        >
                            Register
                        </Link>
                        <Link
                            :href="route('plan.index')"
                            class="rounded-xl px-3 py-2 ring-1 ring-white/10 transition hover:bg-white/10"
                        >
                            Plans
                        </Link>
                    </div>
                </div>
            </footer>
            <!-- Request demo modal -->
            <div
                v-if="showDemoModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"
                    @click="showDemoModal = false"
                />
                <div
                    class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white text-slate-900 shadow-2xl ring-1 ring-slate-200"
                >
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-slate-50 px-6 py-5"
                    >
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                Request a demo
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Tell us a bit about your farm/organization and
                                we’ll schedule a walkthrough.
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="showDemoModal = false"
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

                    <form @submit.prevent="submitDemoRequest" class="p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Full name</label
                                >
                                <input
                                    v-model="demoForm.name"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Mohammed Minuddin"
                                />
                                <div
                                    v-if="demoForm.errors.name"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Email</label
                                >
                                <input
                                    v-model="demoForm.email"
                                    type="email"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="you@company.com"
                                />
                                <div
                                    v-if="demoForm.errors.email"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Phone (optional)</label
                                >
                                <input
                                    v-model="demoForm.phone"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="+880..."
                                />
                                <div
                                    v-if="demoForm.errors.phone"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.phone }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Company / Farm (optional)</label
                                >
                                <input
                                    v-model="demoForm.company"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. Green Valley Farm"
                                />
                                <div
                                    v-if="demoForm.errors.company"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.company }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Country (optional)</label
                                >
                                <input
                                    v-model="demoForm.country"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Bangladesh"
                                />
                                <div
                                    v-if="demoForm.errors.country"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.country }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Timezone</label
                                >
                                <input
                                    v-model="demoForm.timezone"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Asia/Dhaka"
                                />
                                <div
                                    v-if="demoForm.errors.timezone"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.timezone }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Preferred date (optional)</label
                                >
                                <input
                                    v-model="demoForm.preferred_date"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. 2026-03-01"
                                />
                                <div
                                    v-if="demoForm.errors.preferred_date"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.preferred_date }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Preferred time (optional)</label
                                >
                                <input
                                    v-model="demoForm.preferred_time"
                                    type="text"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g. 3:00 PM"
                                />
                                <div
                                    v-if="demoForm.errors.preferred_time"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.preferred_time }}
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                    >Message (optional)</label
                                >
                                <textarea
                                    v-model="demoForm.message"
                                    rows="6"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div
                                    v-if="demoForm.errors.message"
                                    class="mt-2 text-sm text-rose-600"
                                >
                                    {{ demoForm.errors.message }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="text-sm text-slate-500">
                                We’ll reply by email with available demo slots.
                            </div>

                            <button
                                type="submit"
                                :disabled="demoForm.processing"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50"
                            >
                                Submit request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>
