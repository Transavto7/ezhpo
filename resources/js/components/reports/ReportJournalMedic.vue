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
                        <th class="text-center" width="150">Предрейсовый/Предсменный</th>
                        <th class="text-center" width="150">Послерейсовый/Послесменный</th>
                        <th class="text-center" width="150">Несогласованные ПЛ</th>

                        <th class="text-center" width="150">БДД</th>
                        <th class="text-center" width="150">Отчёты с карт</th>
                        <th class="text-center" width="150">Печать ПЛ</th>
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
                            <div class="font-weight-bold" v-else>
                                {{ getSum(item, 'Предрейсовый', 'Предсменный') }}₽
                                <span class="text-red" v-if="getDiscount(item, 'Предрейсовый', 'Предсменный')">
                                    ({{ getDiscount(item, 'Предрейсовый', 'Предсменный') }}%)
                                </span>
                                <i v-if="isSync(item, 'Предрейсовый', 'Предсменный')" class="fa fa-refresh text-success"></i>
                                <i v-else class="fa fa-refresh text-red"></i>
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'Послерейсовый', 'Послесменный') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'Послерейсовый', 'Послесменный') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else>
                                {{ getSum(item, 'Послерейсовый', 'Послесменный') }}₽
                                <span class="text-red" v-if="getDiscount(item, 'Послерейсовый', 'Послесменный')">
                                    ({{ getDiscount(item, 'Послерейсовый', 'Послесменный') }}%)
                                </span>
                                <i v-if="isSync(item, 'Послерейсовый', 'Послесменный')" class="fa fa-refresh text-success"></i>
                                <i v-else class="fa fa-refresh text-red"></i>
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'is_dop') }}
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'bdd') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'bdd') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else>
                                {{ getSum(item, 'bdd') }}₽
                                <span class="text-red" v-if="getDiscount(item, 'bdd')">
                                    ({{ getDiscount(item, 'bdd') }}%)
                                </span>
                                <i v-if="isSync(item, 'bdd')" class="fa fa-refresh text-success"></i>
                                <i v-else class="fa fa-refresh text-red"></i>
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'report_cart') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'report_cart') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else>
                                {{ getSum(item, 'report_cart') }}₽
                                <span class="text-red" v-if="getDiscount(item, 'report_cart')">
                                    ({{ getDiscount(item, 'report_cart') }}%)
                                </span>
                                <i v-if="isSync(item, 'report_cart')" class="fa fa-refresh text-success"></i>
                                <i v-else class="fa fa-refresh text-red"></i>
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotal(item, 'pechat_pl') }}

                            <div class="text-red font-weight-bold" v-if="getSum(item, 'pechat_pl') == null">
                                Услуги не указаны
                            </div>
                            <div class="font-weight-bold" v-else>
                                {{ getSum(item, 'pechat_pl') }}₽
                                <span class="text-red" v-if="getDiscount(item, 'pechat_pl')">
                                    ({{ getDiscount(item, 'pechat_pl') }}%)
                                </span>
                                <i v-if="isSync(item, 'pechat_pl')" class="fa fa-refresh text-success"></i>
                                <i v-else class="fa fa-refresh text-red"></i>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="reports">
                        <td width="100">
                        </td>

                        <td width="250">
                            <b>Всего</b>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'Предрейсовый', 'Предсменный') }}

                            <div class="font-weight-bold" v-if="getSumAll(reports, 'Предрейсовый', 'Предсменный') != null">
                                {{ getSumAll(reports, 'Предрейсовый', 'Предсменный') }}₽
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'Послерейсовый', 'Послесменный') }}


                            <div class="font-weight-bold" v-if="getSumAll(reports, 'Послерейсовый', 'Послесменный') != null">
                                {{ getSumAll(reports, 'Послерейсовый', 'Послесменный') }}₽
                            </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'is_dop') }}
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'bdd') }}

                            <div class="font-weight-bold" v-if="getSumAll(reports, 'bdd') != null"> {{ getSumAll(reports, 'bdd') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'report_cart') }}

                            <div class="font-weight-bold" v-if="getSumAll(reports, 'report_cart') != null"> {{ getSumAll(reports, 'report_cart') }}₽ </div>
                        </td>

                        <td class="text-center" width="150">
                            {{ getTotalAll(reports, 'pechat_pl') }}

                            <div class="font-weight-bold" v-if="getSumAll(reports, 'pechat_pl') != null"> {{ getSumAll(reports, 'pechat_pl') }}₽ </div>
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
import {getTotalAll, getTotal, getSum, getSumAll, getDiscount, isSync} from "../const/reportsAmount";

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
        getTotalAll,
        getTotal,
        getSumAll,
        getSum,
        getDiscount,
        isSync
    }
}
</script>

<style scoped>

</style>
