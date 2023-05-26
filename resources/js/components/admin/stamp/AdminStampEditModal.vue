<template>
    <b-modal v-model="show" hide-footer>
        <template #modal-title>
            Добавить подсказку
        </template>
        <div class="d-block">
            <div class="form-group mb-3">
                <label class="mb-1">Название</label>
                <b-form-input
                    v-model="stamp.name"
                    size="sm"
                    placeholder="Введите название"
                />
            </div>
            <div class="form-group mb-3">
                <label class="mb-1">Заголовок</label>
                <b-form-input
                    v-model="stamp.company_name"
                    size="sm"
                    placeholder="Введите заголовок"
                />
            </div>
            <div class="form-group mb-3">
                <label class="mb-1">Лицензия</label>
                <b-form-input
                    v-model="stamp.licence"
                    size="sm"
                    placeholder="Введите лицензию"
                />
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
export default {
    data() {
        return {
            show: false,
            saving: false,
            stamp: {}
        }
    },
    methods: {
        validate() {
            if (!this.stamp.name) {
                this.$toast('Укажите название', {type: 'error'});
                return false;
            }

            if (!this.stamp.company_name) {
                this.$toast('Укажите заголовок', {type: 'error'});
                return false;
            }

            if (!this.stamp.licence) {
                this.$toast('Укажите лицензию', {type: 'error'});
                return false;
            }

            return true;
        },
        open(stamp) {
            this.show = true;
            this.stamp = { ...stamp };
        },
        async saveElement() {
            if (!this.validate()) {
                return;
            }

            this.saving = true;
            await axios.put('/stamp/' + this.stamp.id, {
                name: this.stamp.name,
                company_name: this.stamp.company_name,
                licence: this.stamp.licence
            }).then(({ data }) => {
                this.show = false;
                this.$toast('Штамп успешно добавлен');
                this.$emit('success', this.stamp);
            }).catch((error) => {
                this.$toast('Ошибка добавления', {type: 'error'});
            });
            this.saving = false;
        }
    }
}
</script>
