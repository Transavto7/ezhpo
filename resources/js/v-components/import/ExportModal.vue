<script>
import Notify from "../../components/notify";
import ButtonWithPending from "../ButtonWithPending.vue";

export default {
    name: "ExportModal",
    components: {ButtonWithPending},
    props: {
        exportUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            pending: false,
            showError: false,
        };
    },
    methods: {
        handleExportClick() {
            const companyId = $('#export_company_select').val();
            if (companyId == null) {
                this.showError = true;
                setTimeout(() => {
                    this.showError = false
                }, 5000)
                return;
            }
            this.pending = true;
            axios.post(this.exportUrl, {company_id: companyId})
                .then(({data}) => {
                    window.location.href = data.url;
                })
                .catch(({response}) => {
                    if (response.status === 400) {
                        Notify.error(response.data.message);
                        return;
                    }
                    console.error(response)
                    Notify.error('Ошибка сервера!');
                })
                .finally(() => {
                    this.pending = false;
                })
        }
    },
    mounted() {
        this.importResult = null;
    }
}
</script>

<template>
    <form @submit.prevent="handleExportClick">
        <div class="modal-body">
            <div class="alert alert-danger" v-if="showError">
                <strong>Ошибка валидации!</strong> Компания для экспорта обязательна для заполнения.
            </div>
            <slot></slot>
        </div>
        <div class="modal-footer">
            <button-with-pending
                type="submit"
                class="btn btn-sm btn-success"
                :pending="pending">
                <template v-slot:default>
                    Экспортировать
                </template>
                <template v-slot:loading>
                    Экспортирование...
                </template>
            </button-with-pending>

            <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">Закрыть</button>
        </div>
    </form>
</template>

<style scoped>
.alert-danger {
    font-size: 12px;
}
</style>
