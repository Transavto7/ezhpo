import {onMounted} from "vue";
import {useCaseConverter} from "@/composables/useCaseConverter";

export const useQueryParamsSync = (params, conf = {}) => {
  const { snakeToCamel, camelToSnake } = useCaseConverter()

  const syncQueryParams = () => {
    const url = new URL(window.location.href)
    const searchParams = new URLSearchParams()

    for (const key in params) {
      const rawValue = conf[key] !== undefined ? conf[key](params[key]) : params[key]

      if (
        rawValue === null ||
        rawValue === undefined ||
        rawValue === '' ||
        (Array.isArray(rawValue) && rawValue.length === 0)
      ) {
        continue
      }

      const queryKey = camelToSnake(key)

      if (Array.isArray(rawValue)) {
        for (const item of rawValue) {
          if (item !== null && item !== undefined && item !== '') {
            searchParams.append(queryKey, String(item))
          }
        }
      } else {
        searchParams.set(queryKey, String(rawValue))
      }
    }

    const newUrl = `${url.pathname}?${searchParams.toString()}${url.hash}`
    window.history.replaceState(null, '', newUrl)
  }

  onMounted(() => {
    const url = new URL(window.location.href)
    const searchParams = url.searchParams

    for (const [key, value] of searchParams.entries()) {
      console.log(key, value)
    }
    return

    for (const [key, value] of searchParams.entries()) {
      const prop = snakeToCamel(key)

      if (params[prop] === undefined || conf[prop] !== undefined) {
        console.warn(`Неподдерживаемый параметр '${key}'`)
        continue
      }

      params[prop] = value
    }
  })

  return {
    syncQueryParams,
  }
}