<template>
    <div>
        <div v-if="reports" class="card">
            <h5 class="card-header">Медосмотры за период</h5>
            <div class="card-body">
                <table id="reports-table-1" class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th width="250">Водители</th>
                        <th class="text-center" width="150">Предрейсовые</th>
                        <th class="text-center" width="150">Послерейсовые</th>

                        <th class="text-center" width="150">Предсменные</th>
                        <th class="text-center" width="150">Послесменные</th>

                        <th class="text-center" width="150">БДД</th>
                        <th class="text-center" width="150">Отчёты с карт</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, name, index) in reports" :key="index">
                        <td width="100">
                            {{ name }}
                        </td>

                        <td width="250">
                            {{ item.driver_fio }}
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Предрейсовый') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Предрейсовый') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Предрейсовый') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Послерейсовый') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Послерейсовый') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Послерейсовый') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Предсменный') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Предсменный') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Предсменный') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Послесменный') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Послесменный') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Послесменный') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'bdd') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'bdd') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'bdd') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Отчёт') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'report_cart') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'report_cart') }}₽ </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else-if="show" class="alert alert-secondary" role="alert">
            Медосмотры за период не найдены
        </div>
    </div>
</template>

<script>
export default {
    name: "ReportJournalMedic",
    data() {
        return {
            reports: false,
            show: false,
        }
    },
    methods: {
        visible(reports, show = true) {
            if (reports.length === undefined || reports.length > 0) {
                this.reports = reports;
            }
            this.show = show;
        },
        getTotal(item, name) {
            if (item.types && item.types[name] && item.types[name].total) {
                return item.types[name].total;
            }

            return 'отсутствует';
        },
        getSum(item, name) {
            if (item.types && item.types[name] && item.types[name].sum) {
                return item.types[name].sum;
            }

            return null
        },
    }
}
</script>

<style scoped>

</style>
