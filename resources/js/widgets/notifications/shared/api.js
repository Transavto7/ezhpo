export const fetchActionsForSelect = ({search}) => {
    return axios.get(`/notifications/actions/select`, {
        params: {search}
    });
}

export const fetchNotificationsForSelect = ({search}) => {
    return axios.get(`/notifications/select/items`, {
        params: {search}
    });
}

export const fetchUsersForSelect = ({search}) => {
    return axios.get(`/notifications/users/select`, {
        params: {search}
    });
}

export const fetchRemindersForSelect = ({search}) => {
    return axios.get(`/notifications/reminders/select`, {
        params: {search}
    });
}