<script setup lang="ts">
import { ChevronDown, Clock, Calendar, FileType } from 'lucide-vue-next'
import type { SortOption } from '@/types/files'


defineProps<{
  modelValue: SortOption
}>()

const emit = defineEmits(['update:modelValue'])

const options = [
  { label: 'Recent', value: 'recent', icon: Clock },
  { label: 'Year', value: 'year', icon: Calendar },
  { label: 'Month', value: 'month', icon: Calendar },
  { label: 'File Type', value: 'type', icon: FileType },
]
</script>

<template>
  <div class="relative inline-block text-left">
    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
      <component 
        :is="options.find(o => o.value === modelValue)?.icon" 
        class="w-4 h-4" 
      />
    </div>

    <select
      :value="modelValue"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
      class="appearance-none border rounded-lg pl-9 pr-10 py-2 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer transition-all border-gray-200 text-gray-700 font-medium"
    >
      <option v-for="opt in options" :key="opt.value" :value="opt.value">
        Sort by: {{ opt.label }}
      </option>
    </select>

    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
      <ChevronDown class="w-4 h-4" />
    </div>
  </div>
</template>