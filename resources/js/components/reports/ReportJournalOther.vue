<template>
    <div>
        <div v-if="data" class="card">
            <h5 class="card-header">Услуги без реестров</h5>
            <div class="card-body">
                <div class="row">
                    <div v-if="data.company" class="col-lg-3">
                        <p class="font-weight-bold" style="font-size: 14px">Услуги компании</p>
                        <table id="reports-table-1" class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th width="200">Услуга</th>
                                    <th class="text-center" width="100">Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(price, name, index) in data.company" :key="index">
                                    <td> {{ name }} </td>
                                    <td class="text-center"> {{ price }}₽ </td>
                                </tr>
                                <tr>
                                    <td> Всего: </td>
                                    <td class="text-center">
                                        {{ getTotal(data.company) }}₽
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="data.drivers" class="col-lg-4">
                        <p class="font-weight-bold" style="font-size: 14px">Услуги водителей</p>
                        <table id="reports-table-1" class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th width="200">Водитель</th>
                                <th width="200">Услуга</th>
                                <th class="text-center" width="100">Сумма</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(info, name, index) in data.drivers" :key="index">
                                <td> {{ info.driver_fio }} </td>
                                <td> {{ info.name }} </td>
                                <td class="text-center"> {{ info.sum }}₽ </td>
                            </tr>
                            <tr>
                                <td> Всего: </td>
                                <td></td>
                                <td class="text-center">
                                    {{ getTotal(data.drivers) }}₽
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="data.cars" class="col-lg-5">
                        <p class="font-weight-bold" style="font-size: 14px">Услуги автомобилей</p>
                        <table id="reports-table-1" class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th width="200">Водитель</th>
                                <th width="200">Услуга</th>
                                <th class="text-center" width="100">Сумма</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(info, name, index) in data.cars" :key="index">
                                    <td> {{ info.gos_number }} ({{ info.type_auto }}) </td>
                                    <td> {{ info.name }} </td>
                                    <td class="text-center"> {{ info.sum }}₽ </td>
                                </tr>
                                <tr>
                                    <td> Всего: </td>
                                    <td></td>
                                    <td class="text-center">
                                        {{ getTotal(data.cars) }}₽
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="show" class="alert alert-secondary" role="alert">
            Услуги без реестров не найдены
        </div>
    </div>
</template>

<script>
export default {
    name: "ReportJournalOther",
    data() {
        return {
            show: false,
            data: false
        }
    },
    methods: {
        visible(data, show = true) {
            if (data.length === undefined || data.length > 0) {
                this.data = data;
            }
            this.show = show;
        },
        hide() {
            this.data = false;
            this.show = false;
        },
        getTotal(item) {
            let sum = 0;
            for (let key in item) {
                if (typeof item[key] === 'number') {
                    sum += item[key];
                } else {
                    sum += item[key]?.sum;
                }
            }
            return sum;
        }
    }
}
</script>
