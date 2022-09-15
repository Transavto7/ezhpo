<template>
    <b-modal v-model="show" hide-footer>
        <template #modal-title>
            Добавить подсказку
        </template>
        <div class="d-block">
            <div class="form-group mb-3">
                <label class="mb-1">Название поля</label>
                <b-form-input
                    v-model="prompt.name"
                    size="sm"
                    placeholder="Введите название поля"
                />
            </div>
            <div class="form-group mb-3">
                <label class="mb-1">Содержимое подсказки</label>
                <vue-editor v-model="prompt.content" :editor-toolbar="customToolbar" />
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <b-button variant="danger" @click="show = false">Отмена</b-button>
                <b-button class="ml-2" variant="success" :disabled="saving" @click="saveElement">
                    <b-spinner v-if="saving" small type="grow"></b-spinner>
                    Сохранить
                </b-button>
            </div>
        </div>
    </b-modal>
</template>

<script>
import { VueEditor, Quill } from "vue2-editor";

export default {
    components: { VueEditor },
    props: ['types', 'fields'],
    data() {
        return {
            show: false,
            saving: false,
            prompt: {},
            customToolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ size: [ 'small', false, 'large']}],
                ['link', { 'color': [] }],

            ]
        }
    },
    mounted() {
        const ColorClass = Quill.import('attributors/class/color');
        Quill.register(ColorClass, true);
    },
    methods: {
        validate() {
            if (!this.prompt) {
                return false;
            }

            if (!this.prompt.name) {
                this.$toast('Укажите название поля', {type: 'error'});
                return false;
            }

            return true;
        },
        open(prompt) {
            this.show = true;
            this.prompt = Object.assign({}, prompt);
        },
        async saveElement() {
            if (!this.validate()) {
                return;
            }

            this.saving = true;
            await axios.put('/field/prompt/' + this.prompt.id, {
                name: this.prompt.name,
                content: this.prompt.content,
            }).then(({ data }) => {
                this.show = false;
                this.$toast('Изменения соханены');
                this.$emit('success', this.prompt);
            }).catch((error) => {

            });
            this.saving = false;
        }
    }
}
</script>

<style scoped>

</style>
