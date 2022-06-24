<template>
    <div v-if="data" class="card">
        <h5 class="card-header">Техосмотры за другие периоды</h5>
        <div class="card-body">
            <table id="reports-table-4" class="table table-responsive">
                <thead>
                <tr>
                    <th v-for="(item, name, index) in data" :key="index"
                        style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="7">
                      {{ months[item.month] }} {{ item.year }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <td v-for="(item, name, index) in data" colspan="7" class="p-0">
                    <table class="w-100 table">
                        <thead>
                        <th>Автомобиль</th>
                        <th>Предрейсовые</th>
                        <th>Послерейсовые</th>
                        <th>Предсменные</th>
                        <th>Послесменные</th>
                        <th>БДД</th>
                        <th>Отчёты с карт</th>
                        </thead>

                        <tbody>
                        <tr v-for="(report, name, index) in item.reports">
                            <td>{{ report.car_gos_number }} / {{ name }}</td>
                            <td>{{ getTotal(report, 'Предрейсовый') }}</td>
                            <td>{{ getTotal(report, 'Послерейсовый') }}</td>
                            <td>{{ getTotal(report, 'Предсменный') }}</td>
                            <td>{{ getTotal(report, 'Послесменный') }}</td>
                            <td>{{ getTotal(report, 'bdd')}} </td>
                            <td>{{ getTotal(report, 'report_cart') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { months } from '../const/local';

export default {
    name: "ReportJournalTechOther",
    data() {
        return {
            data: false,
            show: false,
            months,
        }
    },
    methods: {
        visible(data) {
            if (data.length === undefined || data.length > 0) {
                this.data = data;
            }
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
