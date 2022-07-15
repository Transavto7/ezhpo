<template>
    <div>
        <div v-if="data" class="card">
            <h5 class="card-header">Режим ввода ПЛ</h5>
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
                            <th class="text-center">Автомобиль/Водитель</th>
                            <th class="text-center">Предрейсовый/Предсменный</th>
                            <th class="text-center">Послерейсовый/Послесменный</th>
                            </thead>

                            <tbody>
                            <tr v-for="(report, name, index) in item.reports">
                                <td class="text-center">{{ getStringMark(report) }}</td>
                                <td class="text-center">{{ getTotal(report, 'Предрейсовый', 'Предсменный') }}</td>
                                <td class="text-center">{{ getTotal(report, 'Послерейсовый', 'Послесменный') }}</td>
                            </tr>
                            <tr v-if="item.reports">
                                <td class="text-center">
                                    <b>Всего</b>
                                </td>
                                <td class="text-center">{{ getTotalAll(item.reports, 'Предрейсовый', 'Предсменный') }}</td>
                                <td class="text-center">{{ getTotalAll(item.reports, 'Послерейсовый', 'Послесменный') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else-if="show" class="alert alert-secondary" role="alert">
            Осмотры с режимом ПЛ не найдены
        </div>
    </div>
</template>

<script>
import { months } from '../const/local';

export default {
    name: "ReportJournalPL",
    data() {
        return {
            data: false,
            show: false,
            months
        }
    },
    methods: {
        getStringMark(item) {
            let str = '';
            if (item.car_gos_number) {
                str += item.car_gos_number + ' ';

            }

            if (item.type_auto) {
                str +=  `(${item.type_auto})`;
            }

           if (item.driver_fio) {
               if (str !== '') {
                    str += ' / '
               }
            str += item.driver_fio;
           }

           return str;
        },
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
        getTotalAll(reports, ...names) {
            let total = 0;
            for (let key in reports) {
                const totalDriver = this.getTotal(reports[key], ...names);

                if (typeof totalDriver === 'number') {
                    total += totalDriver;
                }
            }

            if (total > 0) {
                return total;
            }

            return 'отсутствует';
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
