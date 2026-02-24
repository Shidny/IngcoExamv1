<script setup lang="ts">
const emit = defineEmits<{
  'update:modelValue': [value: string];
}>();

const props = defineProps<{
  modelValue?: string;
  placeholder?: string;
  type?: string;
}>();
</script>

<template>
<input 
  :type="type || 'text'" 
  :value="modelValue" 
  :placeholder="placeholder || 'Enter text'" 
  @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
  class="form-control"
/>
</template>
