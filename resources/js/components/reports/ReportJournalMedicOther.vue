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
                            <th class="text-center">Водитель</th>
                            <th class="text-center">Предрейсовые/Предсменный</th>
                            <th class="text-center">Послерейсовые/Послесменный</th>
                            <th class="text-center">режим ПЛ</th>

                            <th class="text-center">БДД</th>
                            <th class="text-center">Отчёты с карт</th>
                            </thead>

                            <tbody>
                            <tr v-for="(report, name, index) in item.reports">
                                <td class="text-center">{{ report.driver_fio }} / {{ name }}</td>
                                <td class="text-center">{{ getTotal(report, 'Предрейсовый', 'Предсменный') }}</td>
                                <td class="text-center">{{ getTotal(report, 'Послерейсовый', 'Послесменный') }}</td>
                                <td class="text-center">{{ getTotal(report, 'is_dop') }}</td>
                                <td class="text-center">{{ getTotal(report, 'bdd')}} </td>
                                <td class="text-center">{{ getTotal(report, 'report_cart') }}</td>
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
    }
}
</script>

<style scoped>

</style>
