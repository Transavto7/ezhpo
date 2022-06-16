<template>
    <div class="ReportJournal">
        <p v-if="!medic.init">Ожидайте, данные формируются...</p>

        <table v-if="medic.init" id="reports-table-1" class="table table-striped table-sm">
            <thead>
            <tr>
                <th width="100">ID</th>
                <th width="250">Водители</th>
                <th width="150">Предрейсовые</th>
                <th width="150">Послерейсовые</th>

                <th width="150">Предсменные</th>
                <th width="150">Послесменные</th>

                <th width="150">БДД</th>
                <th width="150">Отчёты с карт</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in medic.data" :key="i">
                    <td width="100">
                        {{ item.driver_id }}
                    </td>

                    <td width="250">
                        {{ item.driver_fio }}

                        <div v-if="item.syncFields">
                            <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>

                            <span v-for="syncField in item.syncFields" class="text-bold text-success">{{ syncField }}</span>
                        </div>
                    </td>

                    <td width="150">
                        {{ item.predr }}

                        <div v-html="item.predr_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.posler }}
                        <div v-html="item.posler_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.predsmenniy }}
                        <div v-html="item.predsmenniy_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.poslesmenniy }}

                        <div v-html="item.poslesmenniy_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.bdd }}

                        <div v-html="item.bdd_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.report_cart }}

                        <div v-html="item.report_cart_sum"></div>
                    </td>

                </tr>
            </tbody>
        </table>

        <table v-if="tech.init" id="reports-table-2" class="table table-striped table-sm">
            <thead>
            <tr>
                <th width="100">ID</th>
                <th width="250">Автомобили</th>
                <th width="150">Предрейсовые</th>
                <th width="150">Послерейсовые</th>

                <th width="150">Предсменные</th>
                <th width="150">Послесменные</th>

                <th width="150">БДД</th>
                <th width="150">Отчёты с карт</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in tech.data" :key="i">
                    <td width="100">
                        {{ item.car_id }}
                    </td>

                    <td width="250">
                        {{ item.car_gos_number }}

                        <div v-if="item.syncFields">
                            <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>

                            <span v-for="syncField in item.syncFields" class="text-bold text-success">{{ syncField }}</span>
                        </div>
                    </td>

                    <td width="150">
                        {{ item.predr }}

                        <div v-html="item.predr_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.posler }}
                        <div v-html="item.posler_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.predsmenniy }}
                        <div v-html="item.predsmenniy_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.poslesmenniy }}

                        <div v-html="item.poslesmenniy_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.bdd }}

                        <div v-html="item.bdd_sum"></div>
                    </td>

                    <td width="150">
                        {{ item.report_cart }}

                        <div v-html="item.report_cart_sum"></div>
                    </td>

                </tr>
            </tbody>
        </table>

        <table v-if="pechat_pl.init" id="reports-table-3" class="table table-striped table-sm">
            <thead>
            <tr>
                <th>Печать ПЛ</th>
            </tr>
            </thead>
            <tbody>
                <td>{{ pechat_pl.data }}</td>
            </tbody>
        </table>

        <div v-if="medic.init && tech.init">
            <p v-if="medic.init"><b>Таблица "Медосмотры за другие периоды"</b></p>
            <table v-if="medic.init && medic.dopData && medic.dopData.length != medic.hiddenMonths" id="reports-table-4" class="table table-responsive">
                <thead>
                <tr>
                    <th v-for="(month,i) in medic.dopData" v-if="!month.hidden" style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="7">
                        {{ month.name }} {{ month.year }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <td v-for="(month, i) in medic.dopData" colspan="7" class="p-0">
                    <table v-if="!month.hidden" class="w-100 table">
                        <thead>
                        <th>Водитель</th>
                        <th>Предрейсовые</th>
                        <th>Послерейсовые</th>

                        <th>Предсменные</th>
                        <th>Послесменные</th>

                        <th>БДД</th>
                        <th>Отчёты с карт</th>
                        </thead>

                        <tbody>
                        <tr v-for="(report, j) in month.reports">
                            <td>{{ report.driver_fio }} / {{ report.driver_id }}</td>
                            <td>{{ report.predr }}</td>
                            <td>{{ report.posler }}</td>
                            <td>{{ report.predsmenniy }}</td>
                            <td>{{ report.poslesmenniy }}</td>
                            <td>{{ report.bdd }}</td>
                            <td>{{ report.report_cart }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                </tbody>
            </table>
            <p v-else>Осмотры за другие месяцы не создавались (МО)</p>


            <p v-if="tech.init"><b>Таблица "Техосмотры за другие периоды"</b></p>
            <table v-if="tech.init && tech.dopData && tech.dopData.length != tech.hiddenMonths" id="reports-table-tech-months" class="table table-responsive">
                <thead>
                <tr>
                    <th v-for="(month,i) in tech.dopData" v-if="!month.hidden" style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="7">
                        {{ month.name }} {{ month.year }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <td v-for="(month, i) in tech.dopData" colspan="7" class="p-0">
                    <table v-if="!month.hidden" class="w-100 table">
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
                        <tr v-for="(report, j) in month.reports">
                            <td>{{ report.car_gos_number }} / {{ report.car_id }}</td>
                            <td>{{ report.predr }}</td>
                            <td>{{ report.posler }}</td>
                            <td>{{ report.predsmenniy }}</td>
                            <td>{{ report.poslesmenniy }}</td>
                            <td>{{ report.bdd }}</td>
                            <td>{{ report.report_cart }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                </tbody>
            </table>
            <p v-else>Осмотры за другие месяцы не создавались (ТО)</p>


            <p v-if="dop.init"><b>Таблица "Режим ввода ПЛ"</b></p>
            <table v-if="dop.init && dop.dopData && dop.dopData.length != dop.hiddenMonths" id="reports-table-5" class="table table-responsive">
                <thead>
                <tr>
                    <th v-for="(month,i) in dop.dopData" v-if="!month.hidden" style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="5">
                        {{ month.name }} {{ month.year }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <td v-for="(month, i) in dop.dopData" v-if="!month.hidden" colspan="5" class="p-0">
                    <table class="w-100 table">
                        <thead>
                        <th>Автомобиль/Водитель</th>
                        <th>Предрейсовые</th>
                        <th>Послерейсовые</th>

                        <th>Предсменные</th>
                        <th>Послесменные</th>
                        </thead>

                        <tbody>
                        <tr v-for="(report, j) in month.reports">
                            <td>{{ report.driver_fio }} / {{ report.car_gos_number }}</td>
                            <td>{{ report.predr }}</td>
                            <td>{{ report.posler }}</td>
                            <td>{{ report.predsmenniy }}</td>
                            <td>{{ report.poslesmenniy }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                </tbody>
            </table>
            <p v-if>Осмотры за другие месяцы не создавались (Режим ввода ПЛ)</p>

            <hr v-if="dop.init">
            <div v-if="dop.init" class="alert alert-success">
                Отчет успешно сформирован!
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: 'ReportJournal',
        props: [
            'date_from', 'date_to', 'company_id'
        ],

        data () {
            return {
                medic: {
                    init: false,
                    data: [],
                    dopData: [],

                    hiddenMonths: 0
                },

                tech: {
                    init: false,
                    data: [],
                    dopData: [],

                    hiddenMonths: 0
                },

                dop: {
                    init: false,
                    data: [],
                    dopData: [],
                    hiddenMonths: 0
                },

                pechat_pl: {
                    init: false,
                    data: 0
                }
            }
        },

        methods: {
            async getReportsRows () {
                await $.get(location.href + '&api=1').done(async data => {

                    let defaultFilterData = { date_from: this.date_from, date_to: this.date_to, company_id: this.company_id }
                    /**
                     * <МО>
                     */
                    this.medic.data = data.reportsMedic
                    this.medic.dopData = data.months;
                    this.medic.hiddenMonths = data.hiddenMonths;

                    for(let i in this.medic.data) {
                        let driver = this.medic.data[i]

                        let tempFilterData = defaultFilterData
                            tempFilterData.driver_id = driver.driver_id

                        await $.post(`/api/report-data/Driver/${driver.driver_id}`, tempFilterData).done(rData => {
                            this.medic.data[i] = {...rData, ...driver}
                        })

                        await $.get(`/api/sync-fields/Driver/${driver.driver_id}`).done(responseData => {
                            this.medic.data[i].syncFields = responseData
                        })
                    }

                    for(let i in this.medic.dopData) {
                        for(let j in this.medic.dopData[i].reports) {
                            let report = this.medic.dopData[i].reports[j]
                            let { month } = this.medic.dopData[i]

                            let tempFilterData = defaultFilterData
                            tempFilterData.month = this.medic.dopData[i].month
                            tempFilterData.driver_id = report.driver_id

                            await $.post(`/api/report-data/Driver_months/${month}`, tempFilterData).done(rData => {
                                this.medic.dopData[i].reports[j] = {...report, ...rData}
                            })
                        }
                    }

                    this.medic.init = true

                    /**
                     * <ТО>
                     */

                    this.tech.data = data.reportsTech
                    this.tech.dopData = data.monthsTech;
                    this.tech.hiddenMonths = data.hiddenMonthsTech;

                    for(let i in this.tech.data) {
                        let car = this.tech.data[i]

                        let tempFilterData = defaultFilterData
                        tempFilterData.car_id = car.car_id
                        tempFilterData.car_gos_number = car.car_gos_number

                        await $.post(`/api/report-data/Car/${car.car_id}`, tempFilterData).done(rData => {
                            this.tech.data[i] = {...rData, ...car}
                        })

                        await $.get(`/api/sync-fields/Car/${car.car_id}`).done(responseData => {
                            this.tech.data[i].syncFields = responseData
                        })
                    }

                    for(let i in this.tech.dopData) {
                        for(let j in this.tech.dopData[i].reports) {
                            let report = this.tech.dopData[i].reports[j]
                            let { month } = this.tech.dopData[i]

                            let tempFilterData = defaultFilterData
                            tempFilterData.month = this.tech.dopData[i].month

                            await $.post(`/api/report-data/Car_months/${month}`, tempFilterData).done(rData => {
                                this.tech.dopData[i].reports[j] = {...report, ...rData}
                            })
                        }
                    }

                    this.tech.init = true

                    /**
                     * Печать ПЛ
                     */
                    await $.post(`/api/report-data/pechat_pl/${defaultFilterData.company_id}`, defaultFilterData).done(rData => {
                        this.pechat_pl.data = rData
                        this.pechat_pl.init = true
                    })

                    /**
                     * Режим ввода ПЛ
                     */
                    this.dop.hiddenMonths = data.hiddenMonthsDop;
                    this.dop.dopData = data.monthsDop;

                    for(let i in this.dop.dopData) {
                        for(let j in this.dop.dopData[i].reports) {
                            let report = this.dop.dopData[i].reports[j]
                            let { month } = this.dop.dopData[i]

                            let tempFilterData = defaultFilterData
                                tempFilterData.month = this.dop.dopData[i].month

                            await $.post(`/api/report-data/Dop/${month}`, tempFilterData).done(rData => {
                                this.dop.dopData[i].reports[j] = {...report, ...rData}
                            })
                        }
                    }

                    this.dop.init = true;

                })
            }
        },

        async mounted () {
            await this.getReportsRows()
        }
    }
</script>
