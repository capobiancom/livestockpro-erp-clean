<script setup>
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "Confirm action" },
    message: { type: String, default: "" },
    confirmText: { type: String, default: "Confirm" },
    cancelText: { type: String, default: "Cancel" },
    variant: { type: String, default: "primary" }, // 'primary' | 'danger'
    loading: { type: Boolean, default: false },
});

const emit = defineEmits(["close", "confirm"]);
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div
                    class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full"
                    :class="
                        variant === 'danger'
                            ? 'bg-red-50 text-red-600'
                            : 'bg-blue-50 text-blue-600'
                    "
                >
                    <svg
                        v-if="variant === 'danger'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.743-2.99l6.52-11.59zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 102 0V7a1 1 0 00-1-1zm0 8a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ title }}
                    </h2>
                    <p v-if="message" class="mt-1 text-sm text-gray-600">
                        {{ message }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <SecondaryButton
                    :disabled="loading"
                    type="button"
                    @click="emit('close')"
                >
                    {{ cancelText }}
                </SecondaryButton>

                <DangerButton
                    v-if="variant === 'danger'"
                    :disabled="loading"
                    type="button"
                    @click="emit('confirm')"
                >
                    <span v-if="loading"> {{ $t('please_wait') }} </span>
                    <span v-else>{{ confirmText }}</span>
                </DangerButton>

                <PrimaryButton
                    v-else
                    :disabled="loading"
                    type="button"
                    @click="emit('confirm')"
                >
                    <span v-if="loading"> {{ $t('please_wait') }} </span>
                    <span v-else>{{ confirmText }}</span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
