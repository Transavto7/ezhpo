<template>
    <div>
        <div v-if="data" class="card">
            <h5 class="card-header">Медосмотры за другие периоды</h5>
            <div class="card-body">
                <table id="reports-table-4" class="table table-responsive">
                    <thead>
                    <tr>
                        <th v-for="(item, name, index) in data" :key="index" style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="7">
                            {{ months[item.month - 1] }} {{ item.year }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <td v-for="(item, name, index) in data" colspan="7" class="p-0">
                        <table class="w-100 table">
                            <thead>
                            <th>Водитель</th>
                            <th>Предрейсовый/Предсменный</th>
                            <th>Послерейсовые/Послесменные</th>

<!--                            <th>Предсменные</th>-->
<!--                            <th>Послесменные</th>-->

                            <th>БДД</th>
                            <th>Отчёты с карт</th>
                            </thead>

                            <tbody>
                            <tr v-for="(report, name, index) in item.reports">
                                <td>{{ report.driver_fio }} / {{ name }}</td>
                                <td>{{ getTotal(report, 'Предрейсовый') }}</td>
                                <td>{{ getTotal(report, 'Послерейсовый') }}</td>
<!--                                <td>{{ getTotal(report, 'Предсменный') }}</td>-->
<!--                                <td>{{ getTotal(report, 'Послесменный') }}</td>-->
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

        <div v-else-if="show" class="alert alert-secondary" role="alert">
            Медосмотры за другие периоды не найдены
        </div>
    </div>
</template>

<script>
import { months } from '../const/local';

export default {
    name: "ReportJournalMedicOther",
    data() {
        return {
            data: false,
            show: false,
            months,
        }
    },
    methods: {
        visible(data, show = true) {
            if (data.length === undefined || data.length > 0) {
                this.data = data;
            }
            this.show = true;
        },
        hide() {
            this.data = false;
            this.show = false;
        },
        getTotal(item, name) {
            let total = 0;

            if (item.types) {
                for (let key in item.types) {
                    if (key.split('/')[0].trim() === name) {
                        total += parseInt(item.types[key]?.total);
                    }
                }
            }

            if (total > 0) {
                return total;
            }

            return 'отсутствует';
        },
        getSum(item, name) {
            let sum = 0;

            if (item.types) {
                for (let key in item.types) {
                    if (key.split('/')[0].trim() === name) {
                        sum += parseInt(item.types[key]?.sum);
                    }
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
