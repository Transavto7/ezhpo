import {ref} from "vue";

export const useUserReportPageSetup = () => {
    const months = [{value: null, text: 'Не выбрано', disabled: true}];
    const years = [{value: null, text: 'Не выбрано', disabled: true}];
    const townList = [{value: null, text: 'Все'}];
    const roleList = [{value: null, text: 'Все'}];
    const pointList = window.PAGE_SETUP.pointList;
    const employeeList = window.PAGE_SETUP.employeeList;

    const selectedMonth = ref(null);
    const selectedYear = ref(null);
    const selectedTown = ref(null);
    const selectedRole = ref(null);
    const selectedPoints = ref([]);
    const selectedEmployees = ref([]);

    for (let monthNumber in window.PAGE_SETUP.months) {
        months.push({
            value: monthNumber,
            text: window.PAGE_SETUP.months[monthNumber]
        });
    }

    window.PAGE_SETUP.years.forEach((y) => {
        years.push({value: y, text: y});
    });

    window.PAGE_SETUP.townList.forEach((t) => {
        townList.push({value: t.id, text: t.name});
    });

    window.PAGE_SETUP.roleList.forEach((r) => {
        roleList.push({value: r.id, text: r.name});
    });

    const month = months.find(m => m.value == window.PAGE_SETUP.selectedMonth)
    if (month) {
        selectedMonth.value = month.value;
    }
    const yearIndex = window.PAGE_SETUP.years.indexOf(window.PAGE_SETUP.selectedYear);
    if (yearIndex > -1) {
        selectedYear.value = window.PAGE_SETUP.years[yearIndex];
    }

    return {
        months,
        years,
        townList,
        roleList,
        pointList,
        employeeList,
        selectedMonth,
        selectedYear,
        selectedTown,
        selectedRole,
        selectedPoints,
        selectedEmployees
    }
}
