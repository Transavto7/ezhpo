export const fetchUsersTableItems = async (params) => {
  return await axios.post('/users/management/table-items', {
    filter: {
      search: params.filter.search,
      user_ids: params.filter.userIds,
      status: params.filter.status,
      entity_type: params.filter.entityType,
    },
    sort_by: params.sortBy,
    sort_desc: params.sortDesc,
    page: params.page,
    per_page: params.perPage,
  })
}

export const fetchUsersSelect = async (params) => {
  return await axios.post('/users/management/select/users', {
    search: params.search,
    entity_type: params.entityType,
  })
}