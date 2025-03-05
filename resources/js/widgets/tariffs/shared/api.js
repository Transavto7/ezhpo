export const fetchTownsForSelect = ({search}) => {
    return axios.get(`/employees/tariffs/towns/select`, {
        params: {search}
    });
}
export const fetchPointsForSelectWithTown = (search, townId) => {
    return axios.get(`/employees/tariffs/points/select`, {
        params: {search, town_id: townId}
    });
}

export const fetchPointsForSelect = ({search}) => {
    return axios.get(`/employees/tariffs/points/select`, {
        params: {search}
    });
}

export const fetchRolesForSelect = ({search}) => {
    return axios.get(`/employees/tariffs/roles/select`, {
        params: {search}
    });
}
