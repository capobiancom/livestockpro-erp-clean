<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/inertia-vue3";
</script>

<template>
    <div
        class="relative min-h-screen overflow-hidden bg-slate-50 px-4 py-10 sm:px-6 lg:px-8"
    >
        <!-- subtle background decoration -->
        <div class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-emerald-200/60 blur-3xl"
            />
            <div
                class="absolute -bottom-24 right-0 h-72 w-72 rounded-full bg-teal-200/60 blur-3xl"
            />
        </div>

        <div
            class="relative mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-6xl items-center justify-center"
        >
            <div
                class="flex w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_20px_60px_-30px_rgba(15,23,42,0.35)]"
            >
                <!-- Left Column: Branding -->
                <div
                    class="relative hidden w-1/2 items-center justify-center bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-700 px-14 py-16 text-white lg:flex"
                >
                    <div
                        class="pointer-events-none absolute inset-0 opacity-20"
                        style="
                            background-image: radial-gradient(
                                rgba(255, 255, 255, 0.35) 1px,
                                transparent 1px
                            );
                            background-size: 18px 18px;
                        "
                    />
                    <div class="relative z-10 max-w-md">
                        <Link href="/" class="inline-flex items-center gap-3">
                            <ApplicationLogo
                                class="h-14 w-auto text-white drop-shadow mt-5"
                            />
                            <span class="text-lg font-semibold tracking-wide"
                                >Vacaliza ERP SaaS</span
                            >
                        </Link>

                        <h2
                            class="mt-10 text-4xl font-semibold leading-tight tracking-tight"
                        >
                            Gestión agrícola, simplificada.
                        </h2>
                        <p class="mt-4 text-base/7 text-white/85">
                            Rastree el ganado, gestione las operaciones y optimice
                            los rendimientos con una plataforma moderna y segura.
                        </p>

                        <div class="mt-10 grid grid-cols-2 gap-4 text-sm">
                            <div
                                class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15"
                            >
                                <div class="font-medium">Seguro</div>
                                <div class="mt-1 text-white/80">
                                    Mejores prácticas integradas
                                </div>
                            </div>
                            <div
                                class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15"
                            >
                                <div class="font-medium">Rápido</div>
                                <div class="mt-1 text-white/80">
                                    Flujos de trabajo fluidos
                                </div>
                            </div>
                            <div
                                class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15"
                            >
                                <div class="font-medium">Informativo</div>
                                <div class="mt-1 text-white/80">
                                    Informes claros
                                </div>
                            </div>
                            <div
                                class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15"
                            >
                                <div class="font-medium">Escalable</div>
                                <div class="mt-1 text-white/80">
                                    Crece con su granja
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div
                    class="flex w-full items-center justify-center px-6 py-10 sm:px-10 lg:w-1/2"
                >
                    <div class="w-full max-w-md">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
