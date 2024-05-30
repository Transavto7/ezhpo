<script>
import LogsModalTable from "./logs-modal-table.vue";
import swal from "sweetalert2";

export default {
    name: "logs-modal",
    components: {LogsModalTable},
    data() {
        return {
            pageSetup: window.PAGE_SETUP.LOGS_MODAL,
            maps: null,
            items: []
        }
    },
    methods: {
        async reload() {
            try {
                this.items = []
                if (this.maps === null) {
                    const {data} = await axios.post(this.pageSetup.mapDataUrl, {
                        model: this.pageSetup.model
                    })

                    this.maps = data;
                }

                const {data} = await axios.post(this.pageSetup.tableDataUrl, {
                    id: window.PAGE_SETUP.LOGS_MODAL.id,
                    model: this.pageSetup.model
                })

                this.items = data.map((item) => {
                    item.data = (item.data ?? []).map((field) => {
                        field.name = this.maps.fieldPrompts[field.name] ?? field.name

                        return field
                    })

                    item.type = this.maps.actionTypes[item.type];

                    return item
                })
            } catch (e) {
                swal.fire({
                    title: 'Ошибка',
                    text: 'Ошибка при загрузке данных',
                    icon: 'error'
                });
            }
        },
    },
}
</script>

<template>
    <div>
        <div class="col-md-12">
            <div class="card mt-3 mb-0">
                <div class="card-body">
                    <logs-modal-table
                        :items="items"
                    />
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <button class="btn btn-success my-2" @click="reload">Обновить</button>
        </div>
    </div>
</template>

<style scoped></style>
