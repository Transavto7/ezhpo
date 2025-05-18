<script setup>

import {onMounted} from "vue";

const props = defineProps({
    value: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: 'editor'
    },
    toolbar: {
        default: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code'
    }
})

const emits = defineEmits(['input'])

onMounted(() => {
    tinymce.init({
        selector: '#' + props.id,
        language: 'ru',
        toolbar : props.toolbar,
        menubar : true,
        resize : false,
        statusbar : false,
        branding : false,
        min_height: 100,
        code_dialog_height: 200,
        init_instance_callback: (editor) => {
            editor.on('KeyUp', (e) => {
                emits('input', editor.getContent());
            });
            editor.on('Change', (e) => {
                emits('input', editor.getContent());
            });
        }
    })
})
</script>

<template>
    <textarea :id="props.id" :value="props.value"></textarea>
</template>

<style scoped>

</style>
