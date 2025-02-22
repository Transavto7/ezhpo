export const fetchEmployeesTableItems = async (params) => {
  return await axios.get('/settings/employees/table-items' + window.location.search, {
    params: {
      sortBy: params.sortBy,
      sortDesc: params.sortDesc,
      page: params.page,
      take: params.perPage,
    },
  })
}

export const fetchEmployee = async (id) => {
  return await axios.get(`/settings/employees/${id}`)
}

export const deleteEmployee = async (id) => {
  return await axios.delete(`/settings/employees/${id}`)
}

export const restoreEmployee = async (id) => {
  return await axios.post(`/settings/employees/${id}/restore`)
}

export const fetchPermissionsByRoles = async (roleIds) => {
  return await axios.post('/settings/employees/permissions-by-roles', {
    role_ids: roleIds
  })
}

export const createEmployee = async (params) => {
  return await axios.post('/settings/employees', params)
}

export const updateEmployee = async (id, params) => {
  return await axios.put(`/settings/employees/${id}`, params)
}