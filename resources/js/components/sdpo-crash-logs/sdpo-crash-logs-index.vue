<script>
import LogsFilter from "./sdpo-crash-logs-filter.vue";
import LogsTable from "./sdpo-crash-logs-table.vue";
import swal from "sweetalert2";

export default {
    name: "logs-index",
    components: {LogsTable, LogsFilter},
    data() {
        return {
            pageSetup: window.PAGE_SETUP,
            showCrashDataModal: false,
            crashData: null,
            filter: {
                terminals: [],
                points: [],
                types: [],
                versions: [],
                uuid: null,
                happened_at_start: null,
                happened_at_end: null,
                created_at_start: null,
                created_at_end: null,
            },
            items: [],
            page: 1,
            total: 1,
            limit: 100,
        }
    },
    methods: {
        async reload() {
            try {
                const {data} = await axios.post(this.pageSetup.tableDataUrl, {
                    limit: this.limit,
                    page: this.page,
                    filter: {
                        uuid: this.filter.uuid ?? null,
                        happened_at_start: this.filter.happened_at_start ?? null,
                        happened_at_end: this.filter.happened_at_end ?? null,
                        created_at_start: this.filter.created_at_start ?? null,
                        created_at_end: this.filter.created_at_end ?? null,
                        terminals: this.filter.terminals.map(item => item.id),
                        points: this.filter.points.map(item => item.id),
                        types: this.filter.types.map(item => item.id),
                        versions: this.filter.versions.map(item => item.id),
                    },
                })

                this.total = data.total
                this.items = data.data.map((item) => {

                    item.type = this.pageSetup.typesMap[item.type] ?? item.type;

                    return item
                })
            } catch (e) {
                console.error(e.message);

                swal.fire({
                    title: 'Ошибка',
                    text: 'Ошибка при загрузке данных',
                    icon: 'error'
                });
            }
        },

        async handleApply() {
            await this.resetPageOrReload()
        },

        async handleReset() {
            this.filter = {
                terminals: [],
                points: [],
                types: [],
                versions: [],
                uuid: null,
                happened_at_start: null,
                happened_at_end: null,
                created_at_start: null,
                created_at_end: null,
            }
            await this.resetPageOrReload()
        },

        async resetPageOrReload() {
            if (this.page === 1) {
                await this.reload()
            } else {
                this.page = 1
            }
        },

        showCrashData(data) {
            this.showCrashDataModal = true;
            this.crashData = data;
        },

        resetCrashDataModal() {
            this.showCrashDataModal = false;
            this.crashData = null;
        }
    },
    async mounted() {
        await this.reload();
    },
    watch: {
        page() {
            this.reload()
        },

        limit() {
            this.resetPageOrReload()
        },
    }
}
</script>

<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <logs-filter
                            :filter.sync="filter"
                            :terminals-options="pageSetup.terminalsOptions"
                            :types-options="pageSetup.typesOptions"
                            :points-options="pageSetup.pointsOptions"
                            :versions-options="pageSetup.versionsOptions"
                            style="margin-bottom: 25px"
                            @apply="handleApply"
                            @reset="handleReset"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-body">
                        <logs-table
                            :items="items"
                            :page.sync="page"
                            :limit.sync="limit"
                            :total="total"
                            style="margin-top: 25px"
                            @showCrashData="showCrashData"
                        />
                    </div>
                </div>
            </div>
        </div>

        <b-modal v-model="showCrashDataModal"
                 title="Детали ошибки"
                 size="xl"
                 @hidden="resetCrashDataModal"
                 scrollable
                 hide-footer>
            <p>
                {{ crashData }}
            </p>
        </b-modal>
    </div>
</template>

<style scoped>
.table-card {
    max-height: 80vh;
    overflow: hidden;
}

.table-card > .card-body {
    overflow: scroll;
    padding: 0 !important;
    margin: 15px !important;
    overscroll-behavior: contain;
}
</style>
