<script setup>
import {ref, watch} from "vue";
import debounce from "../../../../helpers/debounce";

const props = defineProps({
  interval: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(["update:hour", "remove:hour"]);

const innerHour = ref(props.interval)

const handleChangeStart = debounce((event) => {
  let newValue = Number(event.target.value);

  if (newValue < 0) {
    newValue = 0;
  } else if (newValue > innerHour.value.end) {
    newValue = innerHour.value.end;
  }

  event.target.value = newValue;
  innerHour.value.start = newValue;

  emit('update:hour', props.index, innerHour.value);
}, 200)

const handleChangeEnd = debounce((event) => {
  let newValue = Number(event.target.value);

  if (newValue < innerHour.value.start) {
    newValue = innerHour.value.start;
  } else if (newValue > 23) {
    newValue = 23;
  }

  event.target.value = newValue;
  innerHour.value.end = newValue;

  emit('update:hour', props.index, innerHour.value);
}, 200)

const handleChangePrice = (event) => {
  innerHour.value.price = Number(event.target.value);
  emit('update:hour', props.index, innerHour.value);
}

const handleRemoveHour = () => {
    emit('remove:hour', props.index);
}

watch(props.interval, () => {
  innerHour.value = props.interval
})
</script>

<template>
  <div class="interval-container">
    <div class="overflow-hidden backdrop-blur-sm bg-white/90 border-border/50 shadow-sm rounded-lg border">
      <div class="pb-2 p-6 pt-4">
        <div class="text-lg flex items-center justify-between">
          <span>Интервал {{ props.index + 1 }}</span>
          <span class="fa fa-clock-o"></span>
        </div>
      </div>
      <div class="px-6 pb-4">
        <div class="space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-muted-foreground">С:</span>
            <span class="font-medium">
                            <input :value="props.interval.start" @input.prevent="handleChangeStart" type="number"
                                   min="0" max="24" step="1">:00
                        </span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-muted-foreground">По:</span>
            <span class="font-medium">
                            <input :value="props.interval.end" @input.prevent="handleChangeEnd" type="number" min="0"
                                   max="24" step="1">:00
                        </span>
          </div>
          <div class="flex justify-between text-sm mt-1 pt-2 border-t">
            <span class="text-muted-foreground">Цена часа:</span>
            <span class="font-medium"><input :value="props.interval.price"
                                             @input.prevent="handleChangePrice" type="number"
                                             step="100"></span>
          </div>
            <div class="d-flex justify-content-center align-items-center">
                <button class="btn btn-sm btn-outline-danger" @click.prevent="handleRemoveHour"><span class="fa fa-trash"></span> Удалить интервал</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.interval-container {
  box-sizing: border-box;
  flex-grow: 0;
  flex-shrink: 0;
  flex-basis: 23% !important;
  width: 23% !important;
}

.overflow-hidden {
  overflow: hidden;
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px); // Пример значения для blur-sm
}

.bg-white-90 {
  background-color: rgba(255, 255, 255, 0.9); // Белый цвет с прозрачностью 90%
}

.border-border-50 {
  border-color: rgba(0, 0, 0, 0.5); // Пример цвета границы с прозрачностью 50%
}

.shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); // Пример тени sm
}

.rounded-lg {
  border-radius: 0.5rem; // 8px
}

.border {
  border-width: 1px;
}

.pb-2 {
  padding-bottom: 0.5rem; // 8px
}

.p-6 {
  padding: 1.5rem; // 24px
}

.pt-4 {
  padding-top: 1rem; // 16px
}

.px-6 {
  padding-left: 1.5rem; // 24px
  padding-right: 1.5rem; // 24px
}

.pb-4 {
  padding-bottom: 1rem; // 16px
}

.mt-1 {
  margin-top: 0.25rem; // 4px
}

.pt-2 {
  padding-top: 0.5rem; // 8px
}

.text-lg {
  font-size: 1.125rem; // 18px
}

.text-sm {
  font-size: 0.875rem; // 14px
}

.text-muted-foreground {
  color: #6b7280; // Пример серого цвета для muted-foreground
}

.font-medium {
  font-weight: 500;
}

.flex {
  display: flex;
}

.justify-between {
  justify-content: space-between;
}

.items-center {
  align-items: center;
}

.space-y-2 > * + * {
  margin-top: 0.5rem; // 8px
}

.border-t {
  border-top-width: 1px;
}
</style>
