import axios from 'axios'
import { Classifier } from '@/widgets/reminders/types'

export const fetchActionsForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/actions/select`, {
    params: { search },
  })
}

export const fetchTownsForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/cities/select`, {
    params: { search },
  })
}

export const fetchPointsForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/points/select`, {
    params: { search },
  })
}

export const fetchUsersForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/users/select`, {
    params: { search },
  })
}

export const fetchRolesForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/roles/select`, {
    params: { search },
  })
}

export const fetchCompanyForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/companies/select`, {
    params: { search },
  })
}

export const fetchSubjectTypesForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/subject-types/select`, {
    params: { search },
  })
}

export const fetchSubjectsForSelect = async ({
  search,
  subjectType,
}: {
  search: string | null
  subjectType: string | number | null
}) => {
  return axios.get<Classifier[]>(`/reminders/subjects/select`, {
    params: { search, subject_type: subjectType },
  })
}

export const fetchRemindersForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/reminders/select`, {
    params: { search },
  })
}

export const fetchReminderTypesForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/types/select`, {
    params: { search },
  })
}

export const fetchReminderStatusesForSelect = async ({ search }: { search: string | null }) => {
  return axios.get<Classifier[]>(`/reminders/statuses/select`, {
    params: { search },
  })
}
