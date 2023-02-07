<template>
    <div class="report-group">
        <div class="report__title">
            Услуги без реестров {{ data ? '' : 'не найдены'}}
        </div>

        <div class="report__item" v-if="data">
            <div class="report__cards">
                <div class="report__card" v-if="data.company">
                    <div class="report__card-title">
                        Услуги компании
                    </div>

                    <div class="report__card-item" v-for="(price, name, index) in data.company" :key="index">
                        <div class="report__card-item-name">
                            {{ name }}
                        </div>

                        <div class="report__card-item-price">
                            {{ price }}₽
                        </div>
                    </div>

                    <div class="report__footer">
                        <span>Всего: {{ getTotal(data.company) || 0 }}₽</span>
                    </div>
                </div>

                <div class="report__card" v-if="data.drivers">
                    <div class="report__card-title">
                        Услуги водителей
                    </div>

                    <div class="report__card-item" v-for="(price, name, index) in data.drivers" :key="index">
                        <div class="report__card-item-name">
                            {{ name }}
                        </div>

                        <div class="report__card-item-price">
                            {{ price }}₽
                        </div>
                    </div>

                    <div class="report__footer">
                        <span>Всего: {{ getTotal(data.company) || 0 }}₽</span>
                    </div>
                </div>

                <div class="report__card" v-if="data.cars">
                    <div class="report__card-title">
                        Услуги автомобилей
                    </div>

                    <div class="report__card-item" v-for="(price, name, index) in data.cars" :key="index">
                        <div class="report__card-item-name">
                            {{ name }}
                        </div>

                        <div class="report__card-item-price">
                            {{ price }}₽
                        </div>
                    </div>

                    <div class="report__footer">
                        <span>Всего: {{ getTotal(data.cars) || 0 }}₽</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ReportJournalOther",
    props:['data'],
    data() {
        return {
            show: false,
            // data: false
        }
    },
    methods: {
        // visible(data, show = true) {
        //     if (data.length === undefined || data.length > 0) {
        //         this.data = data;
        //     }
        //     this.show = show;
        // },
        // hide() {
        //     this.data = false;
        //     this.show = false;
        // },
        getTotal(item) {
            let sum = 0;
            for (let key in item) {
                if (typeof item[key] === 'number') {
                    sum += item[key];
                } else if (item[key].sum) {
                    sum += item[key].sum;
                } else {
                    sum += Number.parseInt(item[key]);
                }
            }
            return sum;
        }
    }
}
</script>
