export const createTariff = (tariff) => {
    return axios.post('/employees/tariffs/create', tariff);
}
