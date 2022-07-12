<template>
    <div>
        <div v-if="reports" class="card">
            <h5 class="card-header">Медосмотры за период</h5>
            <div class="card-body">
                <table id="reports-table-1" class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th width="250">Водитель</th>
                        <th class="text-center" width="150">Предрейсовые/Предсменный</th>
                        <th class="text-center" width="150">Послерейсовые/Послесменный</th>
                        <th class="text-center" width="150">режим ПЛ</th>

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
                            {{ getTotal(item, 'Предрейсовый', 'Предсменный') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Предрейсовый', 'Предсменный') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Предрейсовый', 'Предсменный') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Послерейсовый', 'Послесменный') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Послерейсовый', 'Послесменный') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'Послерейсовый', 'Послесменный') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'is_dop') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'is_dop') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else> {{ getSum(item, 'is_dop') }}₽ </div>
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
        hide() {
          this.reports = false;
          this.show = false;
        },
        getTotal(item, ...names) {
            let total = 0;

            if (item.types) {
                for (let key in item.types) {
                    names.forEach(name => {
                        if (key.split('/')[0].trim().toLowerCase() === name.toLowerCase()) {
                            total += parseInt(item.types[key]?.total);
                        }
                    });
                }
            }

            if (total > 0) {
                return total;
            }

            return 'отсутствует';
        },
        getSum(item, ...names) {
            let sum = 0;

            if (item.types) {
                for (let key in item.types) {
                    names.forEach(name => {
                        if (key.split('/')[0].trim().toLowerCase() === name.toLowerCase()) {
                            sum += parseInt(item.types[key]?.sum);
                        }
                    });
                }
            }

            if (sum > 0) {
                return sum;
            }

            return null
        },
    }
}
</script>

<style scoped>

</style>
