<template>
    <div class="row">
        <div class="col-12">
            <b-input-group prepend="ID" class="mt-3">
                <b-form-input
                    :value="identifier"
                    @input="updateIdentifier"
                />
                <b-input-group-append>
                    <b-button variant="outline-success"
                              @click="loadData"
                              :disabled="!searchAvailable">Найти</b-button>
                </b-input-group-append>
            </b-input-group>
        </div>
        <div class="col-12">
            <model-searcher-table
                v-show="items"
                :items="items">
            </model-searcher-table>
        </div>
    </div>
</template>

<script>
import ModelSearcherTable from "./model-searcher-table";
import swal from "sweetalert2";

export default {
    name: "model-searcher",
    components: {ModelSearcherTable},
    data() {
        return {
            pageSetup: window.PAGE_SETUP.MODEL_SEARCHER,
            identifier: '',
            loading: false,
            items: []
        }
    },
    methods: {
        updateIdentifier(value) {
            this.identifier = value
        },
        async loadData() {
            this.loading = true;

            try {
                this.items = []

                const {data} = await axios.post(this.pageSetup.tableDataUrl, {
                    identifier: this.identifier
                })

                this.items = data;
            } catch (e) {
                swal.fire({
                    title: 'Ошибка',
                    text: 'Ошибка при загрузке данных',
                    icon: 'error'
                });
            } finally {
                this.loading = false
            }
        }
    },
    computed: {
        searchAvailable: function () {
            return !this.loading && (this.identifier.trim().length !== 0)
        }
    }
}
</script>

<style scoped>

</style>
