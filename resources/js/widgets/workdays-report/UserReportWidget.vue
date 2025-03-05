<script setup>

import {ref} from "vue";
import {useUserReportPageSetup} from "./useUserReportPageSetup";
import {fetchReport} from "./api";

const {
    years,
    months,
    selectedMonth,
    selectedYear,
    townList,
    selectedTown,
    roleList,
    selectedRole,
    pointList,
    selectedPoints,
    employeeList,
    selectedEmployees
} = useUserReportPageSetup();

let isDisabledSearchButton = ref(false);

let reportList = ref([]);
let errorReportList = ref([]);

const runSearch = () => {
    isDisabledSearchButton.value = true;
    reportList.value = [];
    errorReportList.value = [];

    fetchReport({
        year: selectedYear.value,
        month: selectedMonth.value,
        town: selectedTown.value,
        role: selectedRole.value,
        pointList: selectedPoints.value.map(p => p.id),
        employeeList: selectedEmployees.value.map(e => e.id),
    }).then(response => {
        isDisabledSearchButton.value = false;
        const data = response.data;
        console.log(data);

        if (data.errorReport) {
            for (let report in data.errorReport) {
                errorReportList.value.push(...data.errorReport[report]);
            }
        }
        if (data.employees) {
            reportList.value = data.employees;
        }
    })
}

</script>

<template>
    <div>
        <b-form>
            <b-card-group>
                <b-card>
                    <b-card-text>
                        <label for="year-selector">Год:</label>
                        <b-select id="year-selector" :options="years" v-model="selectedYear"></b-select>
                    </b-card-text>
                </b-card>

                <b-card>
                    <b-card-text>
                        <label id="month-selector">Месяц:</label>
                        <b-select id="month-selector" :options="months" v-model="selectedMonth"></b-select>
                    </b-card-text>
                </b-card>

                <b-card>
                    <b-card-text>
                        <label id="town-selector">Город:</label>
                        <b-select id="town-selector" :options="townList" v-model="selectedTown"></b-select>
                    </b-card-text>
                </b-card>

                <b-card>
                    <b-card-text>
                        <label id="role-selector">Роль:</label>
                        <b-select id="role-selector" :options="roleList" v-model="selectedRole"></b-select>
                    </b-card-text>
                </b-card>
            </b-card-group>
            <b-card-group>
                <b-card style="overflow: visible">
                    <b-card-text>
                        <label for="point-selector">Пункт:</label>
                        <multiselect
                            id="point-selector"
                            v-model="selectedPoints"
                            :options="pointList"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            placeholder=""
                            label="name"
                            track-by="id"
                            select-label="Не выбран"
                            deselect-label="Выбран"
                            selected-label="Выбран"
                        >
                            <template #selection="{ values, search, isOpen }">
                                <span v-if="values.length" v-show="!isOpen">Пунктов выбрано: {{ values.length }}</span>
                                <span v-else v-show="!isOpen">Выбраны все пункты</span>
                            </template>
                            <template #noResult>
                                <span>Не найдено</span>
                            </template>
                        </multiselect>
                    </b-card-text>
                </b-card>

                <b-card style="overflow: visible">
                    <b-card-text>
                        <label for="point-selector">Сотрудник:</label>
                        <multiselect
                            id="point-selector"
                            v-model="selectedEmployees"
                            :options="employeeList"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            placeholder=""
                            label="name"
                            track-by="id"
                            select-label="Не выбран"
                            deselect-label="Выбран"
                            selected-label="Выбран"
                        >
                            <template #selection="{ values, search, isOpen }">
                                <span v-if="values.length" v-show="!isOpen">Сотрудников выбрано: {{ values.length }}</span>
                                <span v-else v-show="!isOpen">Выбраны все сотрудники</span>
                            </template>
                            <template #noResult>
                                <span>Не найдено</span>
                            </template>
                        </multiselect>
                    </b-card-text>
                </b-card>

                <div class="card">
                    <div class="card-body d-flex justify-content-end align-items-end">
                        <b-button variant="info" @click="runSearch" :disabled="isDisabledSearchButton">Поиск</b-button>
                    </div>
                </div>
            </b-card-group>
        </b-form>

        <table class="table mt-2 caption-top" v-if="reportList.length">
            <caption>Отчёт</caption>
            <thead>
                <tr>
                    <th scope="col">Сотрудник</th>
                    <th scope="col">Кол-во часов</th>
                    <th scope="col">з/п</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="employee in reportList" :key="employee.employeeId">
                    <td>{{employeeList.find(e => e.id == employee.employeeId).name}}</td>
                    <td>{{(employee.minute / 60).toFixed(2)}}</td>
                    <td>{{employee.salary}}</td>
                </tr>
            </tbody>
        </table>

        <table class="table mt-2 caption-top" v-if="errorReportList.length">
            <caption>Ошибки</caption>
            <thead>
                <tr>
                    <th scope="col">Сотрудник</th>
                    <th scope="col">Открытие</th>
                    <th scope="col">Закрытие</th>
                    <th scope="col">Час</th>
                    <th scope="col">ПВ</th>
                    <th scope="col">Город</th>
                    <th scope="col">Роль</th>
                    <th scope="col">Ошибка</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(error, key) in errorReportList" :key="key">
                    <td>{{error.employeeId ? employeeList.find(e => e.id == error.employeeId).name : ''}}</td>
                    <td>{{error.dateTimeOpen}}</td>
                    <td>{{error.dateTimeClose}}</td>
                    <td>{{error.hour}}</td>
                    <td>{{error.pointId ? pointList.find(p => p.id == error.pointId).name : ''}}</td>
                    <td>{{error.townId ? townList.find(t => t.value == error.townId).text : ''}}</td>
                    <td>{{error.roleId ? roleList.find(r => r.value == error.roleId).text : ''}}</td>
                    <td>{{error.errorMessage}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style lang="scss" scoped>
table {
    background-color: white;

    &.caption-top caption {
        caption-side: top;
        text-align: center;
        background-color: white;
        font-weight: bold;
    }
}
</style>
