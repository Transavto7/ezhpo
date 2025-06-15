import axios from 'axios'
import { Classifier, SelectFn } from '@/widgets/reminders/types'

export const fetchActionsForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/actions/select`, {
    params: { search },
  })

  return data
}

export const fetchTownsForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/cities/select`, {
    params: { search },
  })

  return data
}

export const fetchPointsForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/points/select`, {
    params: { search },
  })

  return data
}

export const fetchUsersForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/users/select`, {
    params: { search },
  })

  return data
}

export const fetchRolesForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/roles/select`, {
    params: { search },
  })

  return data
}

export const fetchCompanyForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/companies/select`, {
    params: { search },
  })

  return data
}

export const fetchSubjectTypesForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/subject-types/select`, {
    params: { search },
  })

  return data
}

export const fetchSubjectsForSelect: SelectFn = async ({ search, subjectType }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/subjects/select`, {
    params: { search, subject_type: subjectType },
  })

  return data
}

export const fetchRemindersForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/reminders/select`, {
    params: { search },
  })

  return data
}

export const fetchReminderTypesForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/types/select`, {
    params: { search },
  })

  return data
}

export const fetchReminderStatusesForSelect: SelectFn = async ({ search }) => {
  const { data } = await axios.get<Classifier[]>(`/reminders/statuses/select`, {
    params: { search },
  })

  return data
}
