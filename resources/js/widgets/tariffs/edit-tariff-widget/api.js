export const updateTariff = (tariff) => {
    return axios.post(`/employees/tariffs/${tariff.id}`, tariff);
}
