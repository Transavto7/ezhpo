export const formatCreatedAt = (createdAt) => {
  const createdDate = new Date(createdAt)
  const now = new Date()

  const diffMs = now.getTime() - createdDate.getTime()
  const diffSec = Math.floor(diffMs / 1000)
  const diffMin = Math.floor(diffSec / 60)
  const diffHour = Math.floor(diffMin / 60)

  if (diffHour < 1) {
    if (diffMin < 1) {
      return `${diffSec} сек. назад`
    } else {
      return `${diffMin} мин. назад`
    }
  }

  const d = createdDate
  const pad = (n) => n.toString().padStart(2, '0')

  return `${pad(d.getHours())}:${pad(d.getMinutes())} ${pad(d.getDate())}.${pad(d.getMonth() + 1)}.${d.getFullYear()}`
}
