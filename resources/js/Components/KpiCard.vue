<template>
    <div
        class="bg-white/70 backdrop-blur-sm border border-slate-200/70 rounded-2xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.04)] hover:shadow-[0_4px_16px_rgba(15,23,42,0.06)] transition-shadow"
    >
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p
                    class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.08em] truncate"
                >
                    {{ label }}
                </p>
                <p
                    class="mt-3 text-3xl md:text-4xl font-bold text-slate-900 font-mono tabular-nums leading-none"
                >
                    <slot name="value">{{ value }}</slot>
                </p>
                <p
                    v-if="subtitle"
                    class="mt-2 text-xs text-slate-500 truncate"
                >
                    {{ subtitle }}
                </p>
            </div>
            <div
                v-if="$slots.icon"
                :class="[
                    'shrink-0 rounded-xl w-11 h-11 flex items-center justify-center ring-1',
                    chipClass,
                ]"
                aria-hidden="true"
            >
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], default: null },
    subtitle: { type: String, default: "" },
    intent: {
        type: String,
        default: "neutral",
        validator: (v) =>
            ["neutral", "primary", "warning", "critical"].includes(v),
    },
});

const chipMap = {
    neutral: "bg-slate-50 text-slate-600 ring-slate-200/60",
    primary: "bg-green-50 text-green-700 ring-green-200/60",
    warning: "bg-amber-50 text-amber-700 ring-amber-200/60",
    critical: "bg-red-50 text-red-700 ring-red-200/60",
};

const chipClass = computed(() => chipMap[props.intent]);
</script>
