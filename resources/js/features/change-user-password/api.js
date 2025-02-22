export const changeUserPassword = async (id, params) => {
  return await axios.post(`/users/management/${id}/change-password`, {
    password: params.password,
    confirm_password: params.confirmPassword,
  })
}