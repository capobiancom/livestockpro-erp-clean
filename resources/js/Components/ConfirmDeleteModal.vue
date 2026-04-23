<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900"> {{ $t('confirm_deletion') }} </h2>

            <p class="mt-1 text-sm text-gray-600">
                Are you sure you want to delete this item? This action cannot be
                undone.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="close"> {{ $t('cancel') }} </SecondaryButton>

                <DangerButton class="ml-3" @click="confirm">
                    Delete
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "./Modal.vue";
import DangerButton from "./DangerButton.vue";
import SecondaryButton from "./SecondaryButton.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close", "confirm"]);

const close = () => {
    emit("close");
};

const confirm = () => {
    emit("confirm");
};
</script>
