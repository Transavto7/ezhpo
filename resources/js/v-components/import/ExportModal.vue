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
        };
    },
    methods: {
        handleExportClick() {
            const companyId = '';
            this.pending = true;
            axios.post(this.exportUrl)
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

</style>
