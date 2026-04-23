<script setup>
import { computed, ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "Send notification" },
    farmName: { type: String, default: "" },
    loading: { type: Boolean, default: false },
    maxLength: { type: Number, default: 500 },
});

const emit = defineEmits(["close", "submit"]);

const message = ref("");
const touched = ref(false);

watch(
    () => props.show,
    (v) => {
        if (v) {
            message.value = "";
            touched.value = false;
        }
    },
);

const remaining = computed(
    () => props.maxLength - (message.value?.length || 0),
);

const error = computed(() => {
    if (!touched.value) return "";
    if (!message.value || !message.value.trim()) return "Message is required.";
    if ((message.value?.length || 0) > props.maxLength)
        return `Message must be ${props.maxLength} characters or less.`;
    return "";
});

function submit() {
    touched.value = true;
    if (error.value) return;
    emit("submit", message.value.trim());
}
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div
                    class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M2 5a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H7l-4 3v-3H4a2 2 0 01-2-2V5z"
                        />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Send a message to
                        <span class="font-medium text-gray-900">{{
                            farmName || "this farm"
                        }}</span
                        >.
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <InputLabel for="notify_message" value="Message" />
                <textarea
                    id="notify_message"
                    v-model="message"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    :maxlength="maxLength"
                    :disabled="loading"
                    @blur="touched = true"
                    placeholder="Write a short, clear notification…"
                />
                <div class="mt-1 flex items-center justify-between">
                    <InputError :message="error" />
                    <div
                        class="text-xs"
                        :class="
                            remaining < 0 ? 'text-red-600' : 'text-gray-400'
                        "
                    >
                        {{ remaining }} left
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <SecondaryButton
                    :disabled="loading"
                    type="button"
                    @click="emit('close')"
                >
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :disabled="loading"
                    type="button"
                    @click="submit"
                >
                    <span v-if="loading"> {{ $t('sending') }} </span>
                    <span v-else> {{ $t('send') }} </span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
