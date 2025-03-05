export const fetchTariffsTableItems = async (params) => {
    return await axios.post(`/employees/tariffs/list`, params);
}

export const deleteTariffApi = async (id) => {
    return await axios.post(`/employees/tariffs/delete`, {tariff_id: id});
}
