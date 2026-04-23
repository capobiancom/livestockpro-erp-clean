<template>
  <div class="relative w-full text-left" ref="dropdownRef">
    <!-- Trigger Button -->
    <button 
      type="button"
      @click="isOpen = !isOpen" 
      class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors duration-200"
      :class="{'ring-2 ring-amber-500 border-transparent': isOpen}"
    >
      <span class="block truncate" :class="{'text-gray-400': !selectedOption}">
        {{ selectedOption ? selectedOption.label : placeholder }}
      </span>
      <!-- Chevron Icon -->
      <svg :class="['h-5 w-5 text-gray-400 shrink-0 transition-transform duration-200', isOpen ? 'rotate-180' : '']" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Dropdown menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-if="isOpen" 
        class="absolute mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none z-[100] max-h-60 overflow-y-auto"
      >
        <div class="py-1">
          <!-- Placeholder Option (Clear selection) -->
          <button 
            v-if="placeholder"
            type="button"
            @click="selectOption('')"
            class="block w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 transition-colors duration-150"
          >
            {{ placeholder }}
          </button>
          
          <!-- Options -->
          <button 
            v-for="option in options" 
            :key="option.value"
            type="button"
            @click="selectOption(option.value)"
            :class="[
              'block w-full text-left px-4 py-2 text-sm transition-colors duration-150', 
              modelValue === option.value ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
            ]"
          >
            {{ option.label }}
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: ''
    },
    options: {
        type: Array,
        required: true,
        // Array of objects: { value: '...', label: '...' }
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const selectedOption = computed(() => {
    return props.options.find(opt => opt.value === props.modelValue);
});

// Close dropdown when clicking outside
const closeDropdown = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});

const selectOption = (value) => {
  emit('update:modelValue', value);
  emit('change', value);
  isOpen.value = false;
};
</script>
