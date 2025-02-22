export const fetchAccessData = async (id) => {
  return await axios.get(`/users/management/${id}/access-data`)
}

export const fetchPermissionItems = async () => {
  return await axios.get(`/users/management/dictionary/permissions`)
}

export const fetchRoleItems = async () => {
  return await axios.get(`/users/management/dictionary/roles`)
}

export const fetchPermissionsByRoles = async (roleIds) => {
  return await axios.get(`/users/management/permissions/by-roles`, {
    params: {
      role_ids: roleIds,
    }
  })
}

export const updateUserAccess = async (id, params) => {
  return await axios.post(`/users/management/${id}/update-access`, {
    permission_ids: params.permissions,
    role_ids: params.roles,
  })
}