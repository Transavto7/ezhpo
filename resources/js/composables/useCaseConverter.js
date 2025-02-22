export const useCaseConverter = () => {
  const camelToSnake = (str) =>
    str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`)

  const snakeToCamel = (str) =>
    str.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())

  return {
    camelToSnake,
    snakeToCamel
  }
}