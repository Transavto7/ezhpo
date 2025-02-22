<script setup>
import VChip from "@/ui/VChip.vue";
import {computed} from "vue";

const props = defineProps({
  entityType: {
    type: String,
    required: false,
    default: null
  },
  label: {
    type: String,
    required: false,
    default: null,
  },
})
</script>

<template>
  <div>
    <v-chip v-if="props.entityType === 'employee'" type="success">{{ props.label }}</v-chip>
    <v-chip v-if="props.entityType === 'terminal'" type="info">{{ props.label }}</v-chip>
    <v-chip v-if="props.entityType === 'company'" type="danger">{{ props.label }}</v-chip>
    <v-chip v-if="props.entityType === 'driver'" type="warning">{{ props.label }}</v-chip>
    <v-chip v-if="!props.entityType" type="secondary">Без связанной сущности</v-chip>
  </div>
</template>

<style scoped>

</style>