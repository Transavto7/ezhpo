<script>
import ButtonWithPending from "./ButtonWithPending.vue";
import Notify from "../components/notify";

export default {
    name: "ExportElementButton",
    components: {ButtonWithPending},
    props: {
        exportUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            pending: false
        }
    },
    methods: {
        handleExportClick() {
            this.pending = true;
            axios.get(this.exportUrl)
                .then(({data}) => {
                    window.location.href = data.url;
                })
                .catch(() => {
                    Notify.error('Ошибка сервера!');
                })
                .finally(() => {
                    this.pending = false;
                })
        }
    }
}
</script>

<template>
    <button-with-pending
        class="btn btn-sm btn-success"
        :pending="pending"
        @click="handleExportClick"
    >
        <template v-slot:default>
            Экспортировать <i class="fa fa-file-excel-o"></i>
        </template>
        <template v-slot:loading>
            Экспортирование... <i class="fa fa-file-excel-o"></i>
        </template>
    </button-with-pending>
</template>

<style scoped>

</style>
