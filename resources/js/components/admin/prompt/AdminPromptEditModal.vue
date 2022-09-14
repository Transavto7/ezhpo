<template>
    <b-modal v-model="show" hide-footer>
        <template #modal-title>
            Добавить подсказку
        </template>
        <div class="d-block">
            <div class="form-group mb-3">
                <label class="mb-1">Журнал</label>
                <multiselect
                    v-model="type"
                    :options="types"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    @input="field = null"
                    placeholder="Выберите журнал"
                    label="name"
                    :taggable="true"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Результатов не найдено</span>
                </multiselect>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1">Название поля</label>
                <multiselect
                    v-model="field"
                    :options="fieldsType()"
                    :disabled="!type"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder="Выберите поле"
                    label="name"
                    :taggable="true"
                >
                    <span slot="noResult">Результатов не найдено</span>
                    <span slot="noOptions">Результатов не найдено</span>
                </multiselect>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1">Содержимое подсказки</label>
                <vue-editor v-model="content" :editor-toolbar="customToolbar" />
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
import { VueEditor } from "vue2-editor";

export default {
    components: { VueEditor },
    props: ['types', 'fields'],
    data() {
        return {
            show: false,
            type: null,
            field: null,
            content: '',
            saving: false,
            prompt: {},
            customToolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ size: [ 'small', false, 'large']}],
                ['link', { 'color': [] }],

            ]
        }
    },
    methods: {
        fieldsType() {
            if (!this.type) {
                return [];
            }

            return this.fields[this.type.key];
        },
        validate() {
            if (!this.type) {
                return false;
            }

            if (!this.field) {
                return false;
            }

            if (!this.content) {
                return false;
            }
            return true;
        },
        open(prompt) {
            this.show = true;
            this.prompt = prompt;
            this.type = this.types.filter(el => el.key === prompt.type)[0];
            this.field = this.fields[prompt.type].filter(el => el.key === prompt.field)[0];
            this.content = prompt.content;
        },
        async saveElement() {
            if (!this.validate()) {
                return;
            }

            this.saving = true;
            await axios.put('/field/prompt/' + this.prompt.id, {
                type: this.type.key,
                field: this.field.key,
                content: this.content,
            }).then(({ data }) => {
                this.show = false;
                this.$toast('Изменения соханены');
                this.$emit('success');
            }).catch((error) => {

            });
            this.saving = false;
        }
    }
}
</script>

<style scoped>

</style>
