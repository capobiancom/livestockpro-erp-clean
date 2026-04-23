<template>
  <div class="relative inline-block text-left" ref="dropdownRef">
    <button 
      @click="isOpen = !isOpen" 
      class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200 focus:outline-none"
    >
      <!-- Globe Icon -->
      <svg class="h-4 w-4 text-gray-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="uppercase tracking-wider font-semibold">{{ currentLocale }}</span>
      <!-- Chevron Icon -->
      <svg :class="['h-4 w-4 text-gray-400 transition-transform duration-200', isOpen ? 'rotate-180' : '']" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
        class="absolute right-0 mt-2 w-36 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none z-[100]"
      >
        <div class="py-1">
          <button 
            @click="changeLanguage('es')"
            :class="['block w-full text-left px-4 py-2 text-sm transition-colors duration-150', currentLocale === 'es' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900']"
          >
            Español (ES)
          </button>
          <button 
            @click="changeLanguage('en')"
            :class="['block w-full text-left px-4 py-2 text-sm transition-colors duration-150', currentLocale === 'en' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900']"
          >
            English (EN)
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const currentLocale = ref(locale.value || 'es');
const isOpen = ref(false);
const dropdownRef = ref(null);

// Close dropdown when clicking outside
const closeDropdown = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  const savedLocale = localStorage.getItem('vacaliza_locale');
  if (savedLocale) {
    currentLocale.value = savedLocale;
    locale.value = savedLocale;
  }
  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});

const changeLanguage = (selectedLocale) => {
  locale.value = selectedLocale;
  currentLocale.value = selectedLocale;
  localStorage.setItem('vacaliza_locale', selectedLocale);
  isOpen.value = false;
};
</script>
