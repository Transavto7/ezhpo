<script>
import {range} from "lodash";
import CalendarMonth from "./calendar-month.vue";
import Notify from "../notify";

export default {
  components: {CalendarMonth},
  data: () => {
    return {
      selectedDays: [],
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
    <div v-if="loading" class="preloader">
      <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
      <span class="text-white">Загрузка</span>
    </div>
    <b-row>
      <b-col class="text-right">
        <button class="btn btn-outline-secondary" @click="subYear"><span class="fa fa-arrow-left"></span></button>
      </b-col>
      <b-col class="text-center"><h1>{{ year }}</h1></b-col>
      <b-col class="text-left">
        <button class="btn btn-outline-secondary" @click="addYear"><span class="fa fa-arrow-right"></span></button>
      </b-col>
    </b-row>
    <b-row>
      <calendar-month v-for="month in months" :key="month" :month="month" :year="year" :selected-days="daysArray"
                      @update-selected-days="handleUpdateSelectedDay"/>
    </b-row>
    <b-row class="justify-content-center">
      <b-col class="text-center">
        <button class="btn btn-primary" @click="handleSaveClick" :disabled="!hasUnsavedChanges">Сохранить</button>
      </b-col>
    </b-row>
  </div>
</template>

<style scoped lang="scss">
.preloader {
  position: absolute;
  z-index: 99999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
</style>
