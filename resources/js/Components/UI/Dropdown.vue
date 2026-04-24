<template>
  <div class="relative inline-block text-left" ref="dropdownRef">
    <!-- Trigger -->
    <div @click="toggle" class="cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-[var(--focus-ring,rgba(0,0,0,0.1))] rounded-[var(--radius-sm,0.25rem)]">
      <slot name="trigger" :is-open="isOpen" />
    </div>

    <!-- Dropdown menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95 translate-y-[-4px]"
      enter-to-class="transform opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="transform opacity-100 scale-100 translate-y-0"
      leave-to-class="transform opacity-0 scale-95 translate-y-[-4px]"
    >
      <div 
        v-if="isOpen" 
        :class="[
          'absolute z-[100] mt-2 rounded-[var(--radius-md,0.5rem)] bg-[var(--surface-color,#ffffff)] shadow-[var(--shadow-lg,0_10px_15px_-3px_rgba(0,0,0,0.1))] ring-1 ring-[var(--border-color,rgba(0,0,0,0.05))] focus:outline-none overflow-hidden',
          widthClass,
          alignmentClasses
        ]"
      >
        <div class="p-1.5 flex flex-col gap-1">
          <slot name="panel" :close="close" />
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
  align: {
    type: String,
    default: 'right', // left, right
  },
  width: {
    type: String,
    default: '40', // 40, 48, 64, auto
  }
});

const isOpen = ref(false);
const dropdownRef = ref(null);

const toggle = () => {
  isOpen.value = !isOpen.value;
};

const close = () => {
  isOpen.value = false;
};

const closeOnOutsideClick = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    close();
  }
};

const closeOnEscape = (e) => {
  if (isOpen.value && e.key === 'Escape') {
    close();
  }
};

onMounted(() => {
  document.addEventListener('click', closeOnOutsideClick);
  document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
  document.removeEventListener('click', closeOnOutsideClick);
  document.removeEventListener('keydown', closeOnEscape);
});

const widthClass = computed(() => {
  const widths = {
    '40': 'w-40',
    '48': 'w-48',
    '64': 'w-64',
    'auto': 'w-auto whitespace-nowrap',
  };
  return widths[props.width] || widths['40'];
});

const alignmentClasses = computed(() => {
  if (props.align === 'left') {
    return 'left-0 origin-top-left';
  }
  return 'right-0 origin-top-right';
});
</script>
