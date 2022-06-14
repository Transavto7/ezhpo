<template>
    <div class="chart">
        <line-chart :options="{maintainAspectRatio:false}" v-if="chartInit" :chartData="datacollection"></line-chart>
        <div v-else class="col-md-12">Ожидайте, график формируется... (если данных много, загрузка может занять до 5 минут)</div>
    </div>
</template>

<script>
import LineChart from './LineChart'
import {ApiController} from "../components/ApiController";

let API = new ApiController();

let totalLabels = [
    '00:00:00',
    '00:30:00',
    '01:00:00',
    '01:30:00',
    '02:00:00',
    '02:30:00',
    '03:00:00',
    '03:30:00',
    '04:00:00',
    '04:30:00',
    '05:00:00',
    '05:30:00',
    '06:00:00',
    '06:30:00',
    '07:00:00',
    '07:30:00',
    '08:00:00',
    '08:30:00',
    '09:00:00',
    '09:30:00',
    '10:00:00',
    '10:30:00',
    '11:00:00',
    '11:30:00',
    '12:00:00',
    '12:30:00',
    '13:00:00',
    '13:30:00',
    '14:00:00',
    '14:30:00',
    '15:00:00',
    '15:30:00',
    '16:00:00',
    '16:30:00',
    '17:00:00',
    '17:30:00',
    '18:00:00',
    '18:30:00',
    '19:00:00',
    '19:30:00',
    '20:00:00',
    '20:30:00',
    '21:00:00',
    '21:30:00',
    '22:00:00',
    '22:30:00',
    '23:00:00',
    '23:30:00',
]

export default {
    components: {
        LineChart
    },

    props: [
        'pv_id', 'date_from', 'date_to'
    ],

    data () {
        return {
            data: [],
            data2: [],
            chartInit: false,
            datacollection: {
                labels: totalLabels,
                datasets: [
                    {
                        label: 'Сумма по дате осмотра',
                        borderColor: '#e30000',
                        backgroundColor: 'transparent',
                        data: new Array(totalLabels.length).fill(0)
                    },
                    {
                        label: 'Сумма по дате создания',
                        borderColor: '#4545db',
                        backgroundColor: 'transparent',
                        data: new Array(totalLabels.length).fill(0)
                    }
                ]
            }
        }
    },

    async mounted () {
        let labels = this.datacollection.labels

        let dataFromLoad = {
            data: [],
            data2: []
        }

        let $reports = await API.getGraphReport({
            pv_id: this.pv_id,
            date_from: this.date_from,
            date_to: this.date_to
        })

        this.data = $reports.reports;
        this.data2 = $reports.reports2;

        // ДАТА ОСМОТРА
        this.data.forEach((dataItem, dataIndex) => {
            let date = new Date( '2021-11-25 ' + dataItem.date.replace(/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/g, '').trim() )

            labels.forEach((item, index) => {
                let dateCheck = new Date('2021-11-25 ' + item),
                    nextDateCheck = new Date('2021-11-25 ' + (labels[index+1] ? labels[index+1] : labels[0]))

                // Дата осмотра
                if(date >= dateCheck && date < nextDateCheck) {
                    this.datacollection.datasets[0].data[index]++
                }
            })
        })

        // ДАТА СОЗДАНИЯ
        this.data2.forEach((dataItem, dataIndex) => {
            let created_at = new Date( '2021-11-25 ' + dataItem.created_at.replace(/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/g, '').trim() )

            labels.forEach((item, index) => {
                let dateCheck = new Date('2021-11-25 ' + item),
                    nextDateCheck = new Date('2021-11-25 ' + (labels[index+1] ? labels[index+1] : labels[0]))

                // Дата создания
                if(created_at >= dateCheck && created_at < nextDateCheck) {
                    this.datacollection.datasets[1].data[index]++
                }
            })
        })

        this.chartInit = true
    }
}
</script>
