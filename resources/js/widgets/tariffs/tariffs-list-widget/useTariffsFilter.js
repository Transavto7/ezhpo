import {fetchPointsForSelect, fetchRolesForSelect, fetchTownsForSelect} from "../shared/api";

export const useTariffsFilter = () => {
    const fetchTowns = async (params) => {
        try {
            return fetchTownsForSelect({
                ...params,
            })
        } catch (e) {
            console.log(e)
        }
    }

    const fetchPoints = async (params) => {
        try {
            return fetchPointsForSelect({
                ...params,
            })
        } catch (e) {
            console.log(e)
        }
    }

    const fetchRoles = async (params) => {
        try {
            return fetchRolesForSelect({
                ...params,
            })
        } catch (e) {
            console.log(e)
        }
    }

    return {
        fetchTowns,
        fetchPoints,
        fetchRoles,
    }
}
