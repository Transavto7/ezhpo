<script setup lang="ts">
import { onMounted } from 'vue'

const props = defineProps({
  value: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: 'editor',
  },
  toolbar: {
    default:
      'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
  },
})

const emits = defineEmits(['input'])

onMounted(() => {
  // @ts-ignore
  tinymce.init({
    selector: `#${props.id}`,
    language: 'ru',
    toolbar: props.toolbar,
    menubar: true,
    resize: false,
    statusbar: false,
    branding: false,
    min_height: 100,
    code_dialog_height: 200,
    init_instance_callback: (editor: any) => {
      editor.on('KeyUp', (e: any) => {
        emits('input', editor.getContent())
      })
      editor.on('Change', (e: any) => {
        emits('input', editor.getContent())
      })
    },
  })
})
</script>

<template>
  <textarea
    :id="props.id"
    :value="props.value"
  ></textarea>
</template>

<style lang="scss">
.tox.tox-tinymce {
  border-radius: 0;
  border-width: 1px;
  border-color: #dee2e6;
}

.tox:not(.tox-tinymce-inline) .tox-editor-header {
  box-shadow: none !important;
  border-bottom: 1px solid #dee2e6 !important;
}
</style>
