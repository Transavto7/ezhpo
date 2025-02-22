export const unblockUser = async (id) => {
  return await axios.post(`/users/management/${id}/unblock`)
}

export const blockUser = async (id) => {
  return await axios.post(`/users/management/${id}/block`)
}