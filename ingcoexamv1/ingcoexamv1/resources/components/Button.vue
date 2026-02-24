<script setup lang="ts">
const emit = defineEmits<{
  click: [];
}>();

const props = defineProps<{
  text?: string;
  type?: string;
}>();
</script>

<template>
<button :type="type || 'button'" class="btn btn-primary" @click="$emit('click')">{{ text || 'Button' }}</button>
</template>
