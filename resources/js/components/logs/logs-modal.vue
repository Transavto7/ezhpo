<script>
import LogsModalTable from "./logs-modal-table.vue";
import ModelSearcher from "../searcher/model-searcher";
import swal from "sweetalert2";

export default {
    name: "logs-modal",
    components: {ModelSearcher, LogsModalTable},
    data() {
        return {
            pageSetup: window.PAGE_SETUP.LOGS_MODAL,
            maps: null,
            items: []
        }
    },
    methods: {
        async loadData(modelId) {
            try {
                this.items = []

                if (this.maps === null) {
                    const {data} = await axios.post(this.pageSetup.mapDataUrl, {
                        model: this.pageSetup.model
                    })

                    this.maps = data;
                }

                const {data} = await axios.post(this.pageSetup.tableDataUrl, {
                    id: modelId,
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
    mounted() {
        document.addEventListener("loadLogsModalData", (e) => {
            this.$refs.modelSearcher.reset()
            this.loadData(e.detail.modelId);
        });
    },
    computed: {
        searchAvailable: function () {
            return this.items.length > 0
        }
    }
}
</script>

<template>
    <div class="col-md-12 mt-2">
        <logs-modal-table
            :items="items"
        />
        <model-searcher
            v-show="searchAvailable"
            ref="modelSearcher"
        />
    </div>
</template>

<style scoped></style>
