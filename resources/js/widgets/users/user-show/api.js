export const fetchUser = async (id) => {
  return await axios.get(`/users/management/${id}/item`)
}