export const fetchActionsForSelect = ({search}) => {
    return axios.get(`/reminders/actions/select`, {
        params: {search}
    });
}

export const fetchTownsForSelect = ({search}) => {
    return axios.get(`/reminders/cities/select`, {
        params: {search}
    });
}

export const fetchPointsForSelect = ({search}) => {
    return axios.get(`/reminders/points/select`, {
        params: {search}
    });
}

export const fetchUsersForSelect = ({search}) => {
    return axios.get(`/reminders/users/select`, {
        params: {search}
    });
}

export const fetchRolesForSelect = ({search}) => {
    return axios.get(`/reminders/roles/select`, {
        params: {search}
    });
}

export const fetchCompanyForSelect = ({search}) => {
    return axios.get(`/reminders/companies/select`, {
        params: {search}
    });
}

export const fetchSubjectTypesForSelect = ({search}) => {
    return axios.get(`/reminders/subject-types/select`, {
        params: {search}
    });
}

export const fetchSubjectsForSelect = (search, subjectType) => {
    return axios.get(`/reminders/subjects/select`, {
        params: {search, subject_type: subjectType}
    });
}

export const fetchRemindersForSelect = ({search}) => {
    return axios.get(`/reminders/reminders/select`, {
        params: {search}
    });
}

export const fetchReminderTypesForSelect = ({search}) => {
    return axios.get(`/reminders/types/select`, {
        params: {search}
    });
}

export const fetchReminderStatusesForSelect = ({search}) => {
  return axios.get(`/reminders/statuses/select`, {
    params: {search}
  });
}
