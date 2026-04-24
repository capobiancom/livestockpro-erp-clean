<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";

const page = usePage();
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

const currencySymbol = computed(() => page.props.website_currency_symbol ?? "$");

function formatMoney(cents, currency) {
    const amount = (Number(cents || 0) / 100) * 1;
    try {
        return new Intl.NumberFormat(undefined, {
            style: "currency",
            currency: currency || "USD",
            maximumFractionDigits: 2,
        }).format(amount);
    } catch (e) {
        return `${currencySymbol.value} ${amount.toFixed(2)}`;
    }
}

function scrollToPlans() {
    const el = document.getElementById("plans");
    if (!el) return;
    el.scrollIntoView({ behavior: "smooth", block: "start" });
}

const modules = [
    {
        title: "Ganadería",
        description: "Animales, razas, rebaños, reproducción, crías y seguimiento completo del ciclo de vida.",
        items: ["Animales y Rebaños", "Razas", "Registros de Reproducción", "Inseminación y Preñeces", "Partos y Crías"],
        href: "/animals",
        accent: "bg-[#58b32b]",
    },
    {
        title: "Alimentación",
        description: "Planifique tipos de alimento, registre la alimentación y analice el costo por vaca y tendencias de consumo.",
        items: ["Registros de Alimentación", "Tipos de Alimento", "Análisis de Costos", "Consumo"],
        href: "/feedings",
        accent: "bg-[#0089f7]",
    },
    {
        title: "Salud",
        description: "Vacunaciones, problemas de salud, tratamientos y seguimiento de enfermedades con informes de vencimientos.",
        items: ["Vacunaciones", "Eventos de Salud", "Tratamientos", "Próximos a Vencer"],
        href: "/vaccinations",
        accent: "bg-[#a5c72e]",
    },
    {
        title: "Inventario",
        description: "Medicamentos, movimientos de stock, proveedores y alertas de bajo stock/caducidad.",
        items: ["Stock", "Medicamentos", "Proveedores", "Alertas"],
        href: "/inventory",
        accent: "bg-amber-500",
    },
    {
        title: "Finanzas",
        description: "Ventas, compras, gastos, contabilidad y estados financieros en un solo lugar.",
        items: ["Ventas y Compras", "Gastos", "Diario", "Pérdidas y Ganancias / Flujo de Caja"],
        href: "/sales",
        accent: "bg-[#58b32b]",
    },
    {
        title: "RRHH y Operaciones",
        description: "Personal, asistencia, nómina, logística y operaciones diarias de la granja.",
        items: ["Empleados", "Asistencia", "Nómina", "Logística"],
        href: "/employees",
        accent: "bg-[#161922]",
    },
];

const billingPeriod = ref("monthly");
const currency = computed(() => props.websiteSettings?.currency || "USD");

const stats = [
    { label: "Módulos", value: "12+" },
    { label: "Informes", value: "20+" },
    { label: "Control de Acceso", value: "Integrado" },
    { label: "Módulos bajo Suscripción", value: "Soportado" },
];

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const showDemoModal = ref(false);

const demoForm = useForm({
    name: "",
    email: "",
    phone: "",
    company: "",
    country: "",
    preferred_date: "",
    preferred_time: "",
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || "America/Mexico_City",
    message: "Hola equipo de Vacaliza,\n\nMe gustaría solicitar una demostración del producto. Por favor contáctenme con los horarios disponibles.\n\nGracias,\n",
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
        q: "¿Es Vacaliza adecuado para granjas pequeñas?",
        a: "Sí. Comience con los módulos que necesita y escale a medida que su operación crezca.",
    },
    {
        q: "¿Puedo controlar qué módulos están disponibles?",
        a: "Sí. Los módulos se pueden habilitar o deshabilitar a través de las funciones de suscripción y los roles.",
    },
    {
        q: "¿Proporcionan informes y análisis?",
        a: "Sí. La plataforma incluye paneles interactivos y un conjunto creciente de informes operativos y financieros.",
    },
];
</script>

<template>
    <Head :title="`${props.websiteSettings?.site_title || 'Vacaliza'} — Plataforma de Gestión Agrícola`" />

    <div class="min-h-screen bg-gray-50 text-[#444444] font-sans selection:bg-[#a5c72e] selection:text-white">
        <!-- Top Nav -->
        <header class="sticky top-0 z-50 bg-white shadow-md border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-6 py-4 lg:px-8">
                <div class="flex items-center justify-between">
                    <Link href="/" class="group flex items-center gap-3">
                        <div class="relative flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
                            <img v-if="props.websiteSettings?.logo_url" :src="props.websiteSettings.logo_url" alt="Logo" class="h-6 w-6 object-contain" />
                            <ApplicationLogo v-else class="h-6 w-auto text-[#58b32b]" />
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-[#161922]">
                            {{ props.websiteSettings?.site_title || "Vacaliza" }}
                        </span>
                    </Link>

                    <nav v-if="canLogin" class="flex items-center gap-4">
                        <template v-if="$page.props.auth?.user">
                            <Link
                                :href="($page.props.auth.user.roles?.includes('Super Admin') || $page.props.auth.user.roles?.includes('admin')) ? route('admin.dashboard') : route('dashboard')"
                                class="inline-flex h-10 items-center justify-center rounded bg-[#58b32b] px-6 text-sm font-bold text-white transition hover:bg-[#a5c72e] shadow-sm"
                            >
                                Ir al Panel
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-[#444444] transition hover:text-[#58b32b]">
                                Iniciar sesión
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="inline-flex h-10 items-center justify-center rounded bg-[#58b32b] px-6 text-sm font-bold text-white shadow-md transition hover:bg-[#a5c72e] hover:shadow-lg">
                                Registrarse
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main class="relative z-10">
            <div v-if="flashSuccess" class="mx-auto max-w-7xl px-6 pt-6 lg:px-8">
                <div class="rounded-lg border-l-4 border-[#58b32b] bg-green-50 px-4 py-3 text-green-900 shadow-sm font-bold">
                    {{ flashSuccess }}
                </div>
            </div>
            <div v-if="flashError" class="mx-auto max-w-7xl px-6 pt-6 lg:px-8">
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 text-red-900 shadow-sm font-bold">
                    {{ flashError }}
                </div>
            </div>

            <section class="mx-auto max-w-7xl px-6 pt-16 lg:px-8 lg:pt-24 pb-16">
                <div class="text-center max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-xs font-bold text-[#444444] shadow-sm ring-1 ring-gray-200 mb-8 uppercase tracking-wide">
                        <span class="inline-block h-2 w-2 rounded-full bg-[#58b32b] animate-pulse" />
                        Suite moderna de operaciones agrícolas
                    </div>

                    <h1 class="text-5xl font-extrabold tracking-tight text-[#161922] sm:text-7xl mb-6">
                        Gestión agrícola profesional para
                        <span class="text-[#58b32b]">
                            ganadería, finanzas y operaciones.
                        </span>
                    </h1>

                    <p class="text-xl leading-relaxed text-[#444444] max-w-2xl mx-auto font-medium">
                        {{ props.websiteSettings?.site_description || "Vacaliza le ayuda a administrar su granja con claridad: ciclo de vida del ganado, alimentación, salud, inventario, finanzas, recursos humanos e informes — construido para velocidad y control." }}
                    </p>

                    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                        <button
                            type="button"
                            @click="showDemoModal = true"
                            class="rounded bg-[#58b32b] px-8 py-4 text-base font-bold text-white shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl hover:bg-[#a5c72e]"
                        >
                            Solicitar una demo
                        </button>
                        <Link
                            :href="route('register')"
                            class="rounded bg-white px-8 py-4 text-base font-bold text-[#161922] shadow-md ring-1 ring-gray-200 transition-all hover:bg-gray-50 hover:shadow-lg hover:-translate-y-1"
                        >
                            Comenzar ahora
                        </Link>
                    </div>

                    <dl class="mt-16 grid grid-cols-2 gap-6 sm:grid-cols-4 max-w-4xl mx-auto">
                        <div v-for="s in stats" :key="s.label" class="flex flex-col items-center justify-center rounded-lg bg-white px-4 py-8 shadow-sm border border-gray-200 transition hover:-translate-y-1 hover:shadow-md hover:border-[#58b32b]">
                            <dt class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ s.label }}</dt>
                            <dd class="mt-3 text-3xl font-extrabold text-[#161922]">{{ s.value }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- Dashboard Preview (High Contrast) -->
            <section class="bg-[#161922] py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">Por qué los equipos eligen Vacaliza</h2>
                        <p class="mt-4 text-lg text-gray-300 font-medium">Una interfaz limpia, flujos de trabajo consistentes y los controles que necesita para administrar las operaciones con confianza.</p>
                    </div>
                    <div class="relative rounded-2xl bg-[#444444] p-3 shadow-2xl overflow-hidden border border-gray-600">
                        <div class="relative z-10 rounded-xl bg-white overflow-hidden flex flex-col">
                            <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-200 bg-gray-100">
                                <div class="h-3 w-3 rounded-full bg-red-400"></div>
                                <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                                <div class="h-3 w-3 rounded-full bg-[#58b32b]"></div>
                            </div>
                            <div class="p-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-4 bg-gray-50">
                                <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                                    <div class="h-10 w-10 rounded bg-green-100 flex items-center justify-center mb-4">
                                        <div class="h-4 w-4 rounded-full bg-[#58b32b]"></div>
                                    </div>
                                    <div class="text-base font-bold text-[#161922]">Paneles de Control</div>
                                    <div class="mt-2 text-sm text-gray-600">Productividad y métricas clave en un vistazo.</div>
                                </div>
                                <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                                    <div class="h-10 w-10 rounded bg-blue-100 flex items-center justify-center mb-4">
                                        <div class="h-4 w-4 rounded-full bg-[#0089f7]"></div>
                                    </div>
                                    <div class="text-base font-bold text-[#161922]">Informes</div>
                                    <div class="mt-2 text-sm text-gray-600">Informes operativos y financieros detallados.</div>
                                </div>
                                <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                                    <div class="h-10 w-10 rounded bg-yellow-100 flex items-center justify-center mb-4">
                                        <div class="h-4 w-4 rounded-full bg-[#a5c72e]"></div>
                                    </div>
                                    <div class="text-base font-bold text-[#161922]">Roles y Permisos</div>
                                    <div class="mt-2 text-sm text-gray-600">Flujos de trabajo seguros y granulares.</div>
                                </div>
                                <div class="rounded-lg bg-white p-6 shadow border border-gray-200">
                                    <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mb-4">
                                        <div class="h-4 w-4 rounded-full bg-[#161922]"></div>
                                    </div>
                                    <div class="text-base font-bold text-[#161922]">Suscripciones</div>
                                    <div class="mt-2 text-sm text-gray-600">Habilite módulos específicos por plan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Modules -->
            <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-4xl font-extrabold tracking-tight text-[#161922]">Módulos diseñados para su operación</h2>
                    <p class="mt-4 text-xl text-[#444444] font-medium">Cada módulo está diseñado para ser rápido, consistente y fácil de usar, para que su equipo pueda enfocarse en la granja.</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="m in modules" :key="m.title" class="group relative overflow-hidden rounded-lg bg-white p-8 shadow-sm border border-gray-200 transition-all hover:-translate-y-1 hover:shadow-lg hover:border-[#58b32b]">
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg shadow-inner text-white" :class="m.accent">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-[#161922]">{{ m.title }}</h3>
                            </div>
                            <p class="text-base leading-relaxed text-gray-600 mb-8 font-medium">{{ m.description }}</p>
                            <ul class="space-y-4 text-sm text-gray-700 mb-8 font-medium">
                                <li v-for="it in m.items" :key="it" class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-[#58b32b]" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span>{{ it }}</span>
                                </li>
                            </ul>
                            <Link :href="m.href" class="inline-flex items-center gap-2 text-sm font-bold text-[#0089f7] transition-colors hover:text-[#58b32b]">
                                Explorar módulo
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Plans -->
            <section id="plans" class="bg-gray-100 border-t border-gray-200 py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <h2 class="text-4xl font-extrabold tracking-tight text-[#161922]">Planes para granjas de todos los tamaños</h2>
                        <p class="mt-4 text-xl text-[#444444] font-medium">Empiece pequeño y escale en cualquier momento. Elija un plan que se adapte a su operación y desbloquee módulos a medida que crece.</p>
                    </div>

                    <div class="flex justify-center mb-12">
                        <div class="inline-flex items-center rounded-lg bg-white p-1 border border-gray-300 shadow-sm">
                            <button type="button" @click="billingPeriod = 'monthly'" class="rounded px-8 py-2.5 text-sm font-bold transition-all duration-200" :class="billingPeriod === 'monthly' ? 'bg-[#58b32b] text-white shadow' : 'text-gray-600 hover:text-[#161922]'">Mensual</button>
                            <button type="button" @click="billingPeriod = 'yearly'" class="rounded px-8 py-2.5 text-sm font-bold transition-all duration-200" :class="billingPeriod === 'yearly' ? 'bg-[#58b32b] text-white shadow' : 'text-gray-600 hover:text-[#161922]'">Anual</button>
                        </div>
                    </div>

                    <div v-if="props.allPlans.length" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="p in props.allPlans" :key="p.slug" class="group relative flex flex-col overflow-hidden rounded-xl bg-white p-8 shadow border border-gray-200 transition-all hover:-translate-y-2 hover:shadow-xl hover:border-[#58b32b]">
                            <div class="mb-8 border-b border-gray-100 pb-6">
                                <h3 class="text-2xl font-bold text-[#161922]">{{ p.name }}</h3>
                                <p class="mt-2 text-sm text-gray-500 font-medium">{{ (p.features || []).length }} funciones incluidas.</p>
                            </div>

                            <div class="mb-8">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-5xl font-extrabold text-[#58b32b]">
                                        {{ billingPeriod === "yearly" ? (p.yearly_price_cents ? formatMoney(p.yearly_price_cents, currency) : "—") : (p.monthly_price_cents ? formatMoney(p.monthly_price_cents, currency) : "Gratis") }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-500">/ {{ billingPeriod === "yearly" ? "año" : "mes" }}</span>
                                </div>
                                <div v-if="p.yearly_discount_percent && billingPeriod === 'yearly'" class="mt-4 inline-flex items-center rounded bg-yellow-100 px-3 py-1.5 text-xs font-bold text-yellow-800">
                                    Ahorre {{ p.yearly_discount_percent }}% anual
                                </div>
                            </div>

                            <ul class="mb-10 flex-1 space-y-4">
                                <li v-for="f in p.features || []" :key="f.key" class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-[#58b32b]" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span class="text-sm text-gray-700 font-medium">{{ f.name || f.key }}</span>
                                </li>
                            </ul>

                            <Link :href="route('plan.index')" class="mt-auto block w-full rounded bg-[#58b32b] px-4 py-4 text-center text-sm font-bold text-white shadow transition-all hover:bg-[#a5c72e]">
                                Seleccionar plan
                            </Link>
                        </div>
                    </div>

                    <div v-else class="rounded-xl bg-white p-12 text-center shadow border border-gray-200 max-w-2xl mx-auto">
                        <h3 class="text-xl font-bold text-[#161922]">Los planes aparecerán aquí</h3>
                        <p class="mt-3 text-base text-gray-500 font-medium">Aún no se han encontrado planes activos en la base de datos.</p>
                    </div>
                </div>
            </section>

            <!-- Quick Access / CTA Bottom -->
            <section class="bg-white border-t border-gray-200 py-16">
                <div class="mx-auto max-w-4xl px-6 lg:px-8 text-center">
                    <h2 class="text-3xl font-extrabold text-[#161922] mb-6">¿Listo para transformar su granja?</h2>
                    <p class="text-lg text-[#444444] mb-8">Descubra nuestros planes o regístrese ahora para acceder inmediatamente al panel de control y comenzar a gestionar sus recursos de manera eficiente.</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <button type="button" @click="scrollToPlans" class="rounded bg-[#0089f7] px-8 py-4 text-base font-bold text-white shadow-md transition-all hover:bg-blue-600 hover:shadow-lg">
                            Ver Planes de Suscripción
                        </button>
                        <Link :href="route('register')" class="rounded bg-[#58b32b] px-8 py-4 text-base font-bold text-white shadow-md transition-all hover:bg-[#a5c72e] hover:shadow-lg">
                            Crear una Cuenta
                        </Link>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 items-start">
                    <div class="lg:col-span-5">
                        <h2 class="text-4xl font-extrabold tracking-tight text-[#161922]">Preguntas frecuentes</h2>
                        <p class="mt-4 text-xl text-gray-600 font-medium">Respuestas rápidas sobre módulos, acceso e informes para que pueda tomar una decisión informada.</p>
                    </div>
                    <div class="lg:col-span-7 space-y-6">
                        <div v-for="f in faqs" :key="f.q" class="rounded-lg bg-white p-8 shadow-sm border border-gray-200 transition-all hover:shadow-md hover:border-[#58b32b]">
                            <h3 class="text-lg font-bold text-[#161922]">{{ f.q }}</h3>
                            <p class="mt-3 text-base leading-relaxed text-gray-600 font-medium">{{ f.a }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-[#161922]">
                <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex justify-center md:justify-start">
                            <Link href="/" class="flex items-center gap-3">
                                <div class="relative flex h-8 w-8 items-center justify-center rounded bg-white shadow">
                                    <img v-if="props.websiteSettings?.logo_url" :src="props.websiteSettings.logo_url" alt="Logo" class="h-5 w-5 object-contain" />
                                    <ApplicationLogo v-else class="h-5 w-auto text-[#58b32b]" />
                                </div>
                                <span class="text-base font-extrabold text-white">{{ props.websiteSettings?.site_title || "Vacaliza" }}</span>
                            </Link>
                        </div>
                        <div class="mt-8 md:mt-0 flex gap-4 justify-center md:justify-end">
                            <Link :href="route('login')" class="text-sm font-medium text-gray-400 hover:text-white">Iniciar sesión</Link>
                            <Link v-if="canRegister" :href="route('register')" class="text-sm font-medium text-gray-400 hover:text-white">Registrarse</Link>
                            <button @click="scrollToPlans" class="text-sm font-medium text-gray-400 hover:text-white">Planes</button>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-gray-700 pt-8">
                        <p class="text-center text-sm text-gray-400">
                            &copy; {{ new Date().getFullYear() }} {{ props.websiteSettings?.site_title || "Vacaliza" }}. Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Demo Modal -->
    <div v-if="showDemoModal" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-[#161922]/60 backdrop-blur-sm transition-opacity" @click="showDemoModal = false"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    <form @submit.prevent="submitDemoRequest" class="flex flex-col h-full">
                        <div class="bg-white px-6 pb-6 pt-8 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded bg-green-100 sm:mx-0 sm:h-12 sm:w-12">
                                    <svg class="h-6 w-6 text-[#58b32b]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <div class="mt-4 text-center sm:ml-5 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-xl font-bold leading-6 text-[#161922]" id="modal-title">Solicitar Demostración</h3>
                                    <p class="mt-2 text-sm text-gray-500 font-medium">Responderemos por correo electrónico con los horarios disponibles para la demostración.</p>
                                    <div class="mt-6 space-y-5">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700">Nombre</label>
                                            <input type="text" v-model="demoForm.name" required class="mt-1.5 block w-full rounded border-gray-300 shadow-sm focus:border-[#58b32b] focus:ring-[#58b32b] sm:text-sm" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700">Email</label>
                                            <input type="email" v-model="demoForm.email" required class="mt-1.5 block w-full rounded border-gray-300 shadow-sm focus:border-[#58b32b] focus:ring-[#58b32b] sm:text-sm" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700">Teléfono</label>
                                                <input type="text" v-model="demoForm.phone" class="mt-1.5 block w-full rounded border-gray-300 shadow-sm focus:border-[#58b32b] focus:ring-[#58b32b] sm:text-sm" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700">Empresa/Granja</label>
                                                <input type="text" v-model="demoForm.company" class="mt-1.5 block w-full rounded border-gray-300 shadow-sm focus:border-[#58b32b] focus:ring-[#58b32b] sm:text-sm" />
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700">Mensaje</label>
                                            <textarea v-model="demoForm.message" rows="3" class="mt-1.5 block w-full rounded border-gray-300 shadow-sm focus:border-[#58b32b] focus:ring-[#58b32b] sm:text-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-8 border-t border-gray-200">
                            <button type="submit" :disabled="demoForm.processing" class="inline-flex w-full justify-center rounded bg-[#58b32b] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#a5c72e] sm:ml-3 sm:w-auto disabled:opacity-50">
                                Enviar Solicitud
                            </button>
                            <button type="button" @click="showDemoModal = false" class="mt-3 inline-flex w-full justify-center rounded bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
