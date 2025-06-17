<script setup lang="ts">
import { VueSelect } from 'vue-select'
import { PropType, computed, ref } from 'vue'
import debounce from '@/helpers/debounce'
import { SelectOption } from '@/ui/select/types'
import { Deselect, OpenIndicator } from './conf'

const props = defineProps({
  value: {
    type: [Object, Array, null] as PropType<SelectOption | SelectOption[] | null>,
    default: null,
  },
  fetchOptionsAction: {
    type: Function as PropType<
      (params: { search: string | null }) => Promise<{ data: SelectOption[] }>
    >,
    required: true,
  },
  label: {
    type: String as PropType<string | null>,
    default: null,
  },
  placeholder: {
    type: String as PropType<string | null>,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  clearable: {
    type: Boolean,
    default: false,
  },
  closeOnSelect: {
    type: Boolean,
    default: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  limit: {
    type: Number,
    default: 10,
  },
})

const emit = defineEmits<{
  (e: 'input', value: SelectOption | SelectOption[] | null): void
}>()

const loading = ref(false)
const options = ref<SelectOption[]>([])

const displayedPlaceholder = computed(() => {
  if (props.placeholder) {
    return props.placeholder
  }

  return props.multiple ? 'Выберите одно или несколько значений' : 'Выберите значение'
})

const handleSelect = (value: SelectOption | SelectOption[] | null) => {
  emit('input', value)
}

const fuseSearch = (o: SelectOption[]) => {
  return o
}

const fetchOptions = debounce(async function fetchOptions(search: string | null) {
  loading.value = true
  const { data } = await props.fetchOptionsAction({
    search: (search ?? '').trim(),
  })

  options.value = []
  options.value = data.slice(0, props.limit)
  loading.value = false
}, 300)

const handleOpen = () => {
  options.value = []
  fetchOptions('')
}
</script>

<template>
  <div class="form-control-wrapper">
    <span
      v-if="label"
      class="form-control-label"
      :class="{ required }"
    >
      {{ label }}:
    </span>
    <vue-select
      :clearable="clearable"
      :close-on-select="closeOnSelect"
      :components="{ Deselect, OpenIndicator }"
      :disabled="disabled"
      :filter="fuseSearch"
      label="name"
      max-height="100px"
      :multiple="multiple"
      :options="options"
      :placeholder="displayedPlaceholder"
      :value="props.value"
      @input="handleSelect"
      @open="handleOpen"
      @search="fetchOptions"
    >
      <template #no-options="{ search }">
        <span v-if="loading">Загрузка...</span>
        <span v-else-if="search.length < 1"> Введите текст для поиска </span>
        <span v-else>Ничего не найдено</span>
      </template>
    </vue-select>
  </div>
</template>

<style lang="scss">
@import '../../../sass/libs/vue-select';
</style>
