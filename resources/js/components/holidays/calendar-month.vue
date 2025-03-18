<script>
import moment from 'moment';

export default {
    props: {
        month: {
            type: Number,
            required: true,
        },
        year: {
            type: Number,
            required: true,
        },
        selectedDays: {
            type: Array,
            required: true,
        }
    },
    data: () => {
        return {
            days: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
        }
    },
    computed: {
        dateContext() {
            return moment().month(this.month).year(this.year).date(1);
        },
        yearName: function () {
            return this.dateContext.format("Y");
        },

        monthName: function () {
            return this.dateContext.format("MMMM");
        },

        daysInMonth: function () {
            return this.dateContext.daysInMonth();
        },

        currentDate: function () {
            return this.dateContext.get("date");
        },

        firstDayOfMonth: function () {
            let firstDay = moment(this.dateContext).subtract(this.currentDate, "days");
            return firstDay.day();
        },

        previousMonth: function () {
            return moment(this.dateContext).subtract(1, "month");
        },
        previousMonthAsString: function () {
            return this.previousMonth.format("MMMM");
        },
        nextMonth: function () {
            return moment(this.dateContext).add(1, "month");
        },
        nextMonthAsString: function () {
            return this.nextMonth.format("MMMM");
        },

        daysInPreviousMonth: function () {
            return this.previousMonth.daysInMonth();
        },
        daysFromPreviousMonth: function () {
            let daysList = [];
            let count = this.daysInPreviousMonth - this.firstDayOfMonth;
            while (count < this.daysInPreviousMonth) {
                count++;
                daysList[count] = count;
            }
            return daysList.filter(function () {
                return true;
            });
        },

        dateList: function () {
            let $this = this;

            let dateList = [];

            let previousMonth = this.previousMonth;
            let nextMonth = this.nextMonth;

            //dates for display
            let formattedCurrentMonth = this.dateContext.format("MM");
            let formattedCurrentYear = this.yearName;
            let formattedPreviousMonth = previousMonth.format("MM");
            let formattedPreviousYear = previousMonth.format("Y");
            let formattedNextMonth = nextMonth.format("MM");
            let formattedNextYear = nextMonth.format("Y");

            //counters
            let countDayInCurrentMonth = 0;
            let countDayInPreviousMonth = 0;

            //filling in dates from the previous month
            this.daysFromPreviousMonth.forEach(function (dayFromPreviousMonth) {
                countDayInCurrentMonth++;
                countDayInPreviousMonth++;
                let formattedDay = $this.formattingDay(dayFromPreviousMonth);

                dateList[countDayInCurrentMonth] = {
                    key: countDayInCurrentMonth,
                    dayNumber: formattedDay,
                    date: `${formattedPreviousYear}-${formattedPreviousMonth}-${formattedDay}`,
                    blank: true,
                    weekDay: false,
                    moment: moment(
                        formattedPreviousYear +
                        formattedPreviousMonth +
                        formattedDay
                    )
                };
            });

            //filling in dates from the current month
            while (countDayInCurrentMonth < this.firstDayOfMonth + this.daysInMonth) {
                countDayInCurrentMonth++;

                let day = countDayInCurrentMonth - countDayInPreviousMonth;
                let weekDay = this.getWeekDay(countDayInCurrentMonth);
                let formattedDay = this.formattingDay(day);

                dateList[countDayInCurrentMonth] = {
                    key: countDayInCurrentMonth,
                    dayNumber: formattedDay,
                    date: `${formattedCurrentYear}-${formattedCurrentMonth}-${formattedDay}`,
                    blank: false,
                    selected: false,
                    weekDay: weekDay,
                    moment: moment(
                        formattedCurrentYear +
                        formattedCurrentMonth +
                        formattedDay
                    )
                };
            }

            let daysInNextMonth = 7 - (countDayInCurrentMonth % 7);
            let countDayInCurrentMonthSaved = countDayInCurrentMonth;
            let day = 0;

            //filling in dates from the next month
            if (daysInNextMonth < 7) {
                while (
                    countDayInCurrentMonth <
                    countDayInCurrentMonthSaved + daysInNextMonth
                    ) {
                    countDayInCurrentMonth++;
                    day++;

                    let formattedDay = this.formattingDay(day);

                    dateList[countDayInCurrentMonth] = {
                        key: countDayInCurrentMonth,
                        dayNumber: formattedDay,
                        date: `${formattedNextYear}-${formattedNextMonth}-${formattedDay}`,
                        blank: true,
                        weekDay: false,
                        moment: moment(
                            formattedNextYear +
                            formattedNextMonth +
                            formattedDay
                        )
                    };
                }
            }

            return dateList.filter(function () {
                return true;
            });
        },
    },
    methods: {
        updateSelectedDays: function (date, isBlank) {
            if (!isBlank) {
                this.$emit('update-selected-days', date);
            }
        },
        formattingDay(day) {
            return ("0" + day).slice(-2);
        },
        getWeekDay(day) {
            let index = day;
            if (index > 7) {
                index %= 7;
            }
            index = index === 0 ? 6 : index - 1;
            return this.days[index];
        }
    },
}
</script>

<template>
    <b-col md="3">
        <div class="b-calendar__calendar">
            <div class="b-calendar__header">
                <b-row align-v="center">
                    <b-col class="text-center" align-h="center">
                        <span class="month">{{ monthName }}</span>
                    </b-col>
                </b-row>
            </div>
            <div class="b-calendar__weekdays">
                <div class="weekday" v-for="(day, index) in days" :key="index">
                    <strong>{{ day }}</strong>
                </div>
            </div>
            <div class="b-calendar__dates">
                <div class="date text-right" :class="{
                                'blank': date.blank,
                                'selected': selectedDays.includes(date.date),
                                'no-border-right': date.key % 7 === 0,
                             }" v-for="date in dateList" :key="date.key" :data-date="date.date"
                     @click="updateSelectedDays(date.date, date.blank)">
                    <span class="day">{{ date.dayNumber }}</span>
                    <span class="weekday">{{ date.weekDay }}</span>
                </div>
            </div>
        </div>
    </b-col>
</template>

<style scoped lang="scss">
.b-calendar {
    display: flex;
    align-items: center;
    margin: 2.5em 0;

    &__calendar {
        padding: 2rem;
    }

    &__header {
        margin-bottom: 2rem;

        .month {
            font-size: 1.25em;
            font-weight: 200;
            text-transform: capitalize;
        }
    }

    &__weekdays {
        display: flex;
        margin-bottom: 1.25rem;

        .weekday {
            width: calc(100% / 7);
            padding: 0.25rem 0.5rem;
        }
    }

    &__dates {
        display: flex;
        flex-wrap: wrap;
        position: relative;

        &:after {
            content: "";
            position: absolute;
            bottom: 0;
            background-color: #fff;
            height: 1px;
            width: 100%;
            z-index: 1;
        }

        .date {
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 200;
            min-height: 3rem;
            padding: 0.25rem 0.5rem;
            position: relative;
            width: calc(100% / 7);
            cursor: pointer;

            &:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            &.blank {
                background-color: rgba(0, 0, 0, 0.02);
                color: rgba(0, 0, 0, 0.2);
            }

            &.no-border-right {
                border-right: none;
            }

            &.selected {
                background-color: rgba(0, 123, 255, 0.2);

                &:hover {
                    background-color: rgba(0, 123, 255, 0.1);
                }
            }

            .weekday {
                display: none;
            }

            .additional {
                font-size: 0.75em;
                position: absolute;
                bottom: 0.25rem;
                left: 0.5rem;

                .year {
                    padding-right: 0.25rem;
                    font-size: 0.75em;
                }
            }
        }
    }
}
</style>
