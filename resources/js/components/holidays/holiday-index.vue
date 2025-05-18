<script>
import {range} from "lodash";
import CalendarMonth from "./calendar-month.vue";
import Notify from "../notify";
import Preloader from "@/ui/Preloader.vue";

export default {
    components: {Preloader, CalendarMonth},
    data: () => {
        return {
            selectedDays: [],
            weekendsFromApi: [],
            deletedDays: [],
            addedDays: [],
            year: 2025,
            loading: false,
        }
    },
    computed: {
        months() {
            return range(0, 12)
        },
        daysArray() {
            const enableDates = [...new Set([...this.selectedDays, ...this.addedDays])];
            return enableDates.filter(item => !this.deletedDays.includes(item));
        },
        hasUnsavedChanges() {
            return this.deletedDays.length > 0 || this.addedDays.length > 0;
        }
    },
    methods: {
        handleUpdateSelectedDay(date) {
            const isInSelectedDays = this.selectedDays.includes(date);

            if (isInSelectedDays) {
                const indexInDeleted = this.deletedDays.indexOf(date);
                if (indexInDeleted !== -1) {
                    this.deletedDays.splice(indexInDeleted, 1);
                } else {
                    this.deletedDays.push(date);
                }
            } else {
                const indexInAdded = this.addedDays.indexOf(date);
                if (indexInAdded !== -1) {
                    this.addedDays.splice(indexInAdded, 1);
                } else {
                    this.addedDays.push(date);
                }
            }
        },
        async handleSaveClick() {
            await axios.post('/employees/holidays', {
                deleted_days: this.deletedDays,
                added_days: this.addedDays,
            }).then(() => {
                this.deletedDays = [];
                this.addedDays = [];
                this.fetchDates();
                Notify.success('Данные успешно сохранены!');
            }).catch(error => {
                Notify.error('Ошибка сервера!');
            });
        },
        addYear() {
            this.year++;
            this.fetchDates();
        },
        subYear() {
            this.year--;
            this.fetchDates();
        },
        handleBeforeUnload(event) {
            if (this.hasUnsavedChanges) {
                event.preventDefault();
                event.returnValue = true;
            }
        },
        async fetchDates() {
            this.loading = true;
            await axios.get('/employees/holidays/by-year', {params: {year: this.year}})
                .then(({data}) => {
                    this.selectedDays = data;
                }).catch(error => {
                    Notify.error('Ошибка сервера!');
                }).finally(() => {
                    this.loading = false;
                });
        },
        async handleClickGetDates() {
            this.loading = true;
            await axios.get('/employees/holidays/from-api', {params: {year: this.year}})
                .then(({data}) => {
                    this.weekendsFromApi = this.getWeekendDatesFromBinary(data);
                    this.weekendsFromApi.forEach((item) => {
                        this.handleUpdateSelectedDay(item);
                    })
                }).catch(error => {
                    Notify.error('Ошибка сервера!');
                }).finally(() => {
                    this.loading = false;
                });
        },
        getWeekendDatesFromBinary(binaryString) {
            const weekendDates = [];
            let currentDate = new Date(this.year, 0, 1);

            for (let i = 0; i < binaryString.length; i++) {
                const char = binaryString[i];

                if (currentDate.getFullYear() > this.year) break;

                if (char === '1' || (currentDate.getDay() === 0 || currentDate.getDay() === 6)) {
                    const formattedDate = this.formatDate(currentDate);
                    if (!this.addedDays.includes(formattedDate) && !this.selectedDays.includes(formattedDate)) {
                        weekendDates.push(formattedDate);
                    }
                }

                currentDate.setDate(currentDate.getDate() + 1);
            }

            return weekendDates;
        },
        formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        handleDateClick() {
            this.weekendsFromApi.forEach((item) => {
                this.handleUpdateSelectedDay(item);
            })
        }
    },
    mounted() {
        this.fetchDates();
        this.beforeUnloadHandler = this.handleBeforeUnload.bind(this);
        window.addEventListener('beforeunload', this.beforeUnloadHandler);
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.beforeUnloadHandler);
    },
}
</script>

<template>
    <div class="card-body">
        <preloader :loading="loading"/>
        <b-row>
            <b-col class="d-flex justify-content-between">
                <div>
                    <button class="btn btn-outline-success" @click="handleClickGetDates">Получить выходные</button>
                    <button class="btn btn-outline-danger" @click="handleDateClick">Сбросить выходные</button>
                </div>
                <div class="text-right">
                    <button class="btn btn-outline-secondary" @click="subYear"><span class="fa fa-arrow-left"></span>
                    </button>
                </div>
            </b-col>
            <b-col class="text-center"><h1>{{ year }}</h1></b-col>
            <b-col class="text-left">
                <button class="btn btn-outline-secondary" @click="addYear"><span class="fa fa-arrow-right"></span>
                </button>
            </b-col>
        </b-row>
        <b-row>
            <calendar-month v-for="month in months" :key="month" :month="month" :year="year" :selected-days="daysArray"
                            @update-selected-days="handleUpdateSelectedDay"/>
        </b-row>
        <b-row class="justify-content-center">
            <b-col class="text-center">
                <button class="btn btn-primary" @click="handleSaveClick" :disabled="!hasUnsavedChanges">Сохранить
                </button>
            </b-col>
        </b-row>
    </div>
</template>

<style scoped lang="scss">
</style>
