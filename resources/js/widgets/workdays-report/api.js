export const fetchReport = (data) => {
    return axios.post('/employees/workdays/salaries/calc', data);
}
