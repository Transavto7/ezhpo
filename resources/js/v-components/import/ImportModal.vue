<script>
import Notify from "../../components/notify";
import ButtonWithPending from "../ButtonWithPending.vue";

export default {
    name: "ImportModal",
    components: {ButtonWithPending},
    props: {
        type: {
            required: true,
            type: String
        },
        importUrl: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            file: null,
            pending: false,
            importResult: null
        };
    },
    methods: {
        handleSubmit() {
            this.pending = true
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('type', this.type);
            const headers = {'Content-Type': 'multipart/form-data'};
            axios.post(this.importUrl, formData, {headers})
                .then((res) => {
                    this.importResult = res.data;
                    Notify.success("Импорт завершен успешно!")
                })
                .catch(() => {
                    Notify.error("Ошибка сервера!")
                })
                .finally(() => {
                    this.pending = false;
                });
        }
    },
    mounted() {
        this.importResult = null;
    }
}
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <div class="modal-body">
            <div class="alert" :class="{
                'alert-success': !this.importResult.hasError,
                'alert-danger': this.importResult.hasError
            }" v-if="importResult">
                Импорт завершен! <br>
                Всего строк: <b>{{ importResult.allRows }}</b>. <br>
                Записано: <b>{{ importResult.acceptedRows}}</b>. <br>
                Ошибочных: <b>{{ importResult.errorRows }}</b>. <br>
                <a :href="this.importResult.errorFileUrl" v-if="importResult.hasError">Файл с ошибками.</a>
            </div>
            <slot></slot>
            <div class="form-group">
                <b-form-file
                    v-model="file"
                    :state="Boolean(file)"
                    placeholder="Выберите файл для импорта..."
                    drop-placeholder="Перенесите файл сюда..."
                    accept=".xls,.xlsx"
                    browse-text="Просмотр"
                    required
                ></b-form-file>
            </div>
        </div>
        <div class="modal-footer">
            <button-with-pending
                type="submit"
                class="btn btn-sm btn-success"
                :pending="pending">
                <template v-slot:default>
                    Импортировать
                </template>
                <template v-slot:loading>
                    Импортирование...
                </template>
            </button-with-pending>

            <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">Закрыть</button>
        </div>
    </form>
</template>

<style scoped>

</style>
