import {fetchPointsForSelect, fetchRolesForSelect, fetchTownsForSelect} from "../shared/api";

export const useTariffsFilter = () => {
    const fetchTowns = async (params) => {
        try {
            return fetchTownsForSelect({
                ...params,
            })
        } catch (e) {
            console.error(e)
        }
    }

    const fetchPoints = async (params) => {
        try {
            return fetchPointsForSelect({
                ...params,
            })
        } catch (e) {
            console.error(e)
        }
    }

    const fetchRoles = async (params) => {
        try {
            return fetchRolesForSelect({
                ...params,
            })
        } catch (e) {
            console.error(e)
        }
    }

    return {
        fetchTowns,
        fetchPoints,
        fetchRoles,
    }
}
