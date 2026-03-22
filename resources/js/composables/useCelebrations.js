import { computed } from 'vue'
import { DateTime } from 'luxon'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

/**
 * Fixed-date US holidays for MVP.
 *
 * TODO: Variable-date holidays (Mother's Day, Father's Day, Thanksgiving, Easter)
 * are intentionally skipped — they require date calculation logic and differ
 * between US/UK/EU. Could be added later with a locale-aware holiday library
 * or manual nth-weekday-of-month helpers.
 */
const FIXED_HOLIDAYS = [
  { month: 1, day: 1, name: "New Year's Day", emoji: '🎆', message: 'Happy New Year!' },
  { month: 2, day: 14, name: "Valentine's Day", emoji: '💝', message: "Happy Valentine's Day!" },
  { month: 7, day: 4, name: 'Independence Day', emoji: '🇺🇸', message: 'Happy 4th of July!' },
  { month: 10, day: 31, name: 'Halloween', emoji: '🎃', message: 'Happy Halloween!' },
  { month: 12, day: 25, name: 'Christmas', emoji: '🎄', message: 'Merry Christmas!' },
]

/**
 * Composable that detects birthdays and holidays for the current day.
 *
 * Returns reactive data about:
 * - Which family members have a birthday today
 * - Whether today is the current user's birthday
 * - Whether today is a known holiday
 */
export function useCelebrations() {
  const authStore = useAuthStore()
  const { currentUser, familyMembers } = storeToRefs(authStore)

  const today = DateTime.now()
  const todayMonth = today.month
  const todayDay = today.day

  /**
   * Family members whose birthday is today (month+day match only).
   */
  const birthdayMembers = computed(() => {
    if (!familyMembers.value || familyMembers.value.length === 0) return []

    return familyMembers.value.filter((member) => {
      if (!member.date_of_birth) return false
      const dob = DateTime.fromISO(member.date_of_birth)
      return dob.month === todayMonth && dob.day === todayDay
    })
  })

  /**
   * Whether any family member has a birthday today.
   */
  const hasBirthdays = computed(() => birthdayMembers.value.length > 0)

  /**
   * Whether today is the current user's own birthday.
   */
  const isMyBirthday = computed(() => {
    if (!currentUser.value?.date_of_birth) return false
    const dob = DateTime.fromISO(currentUser.value.date_of_birth)
    return dob.month === todayMonth && dob.day === todayDay
  })

  /**
   * Today's holiday (if any), or null.
   */
  const todayHoliday = computed(() => {
    return FIXED_HOLIDAYS.find((h) => h.month === todayMonth && h.day === todayDay) || null
  })

  /**
   * Whether today is a known holiday.
   */
  const isHoliday = computed(() => todayHoliday.value !== null)

  return {
    birthdayMembers,
    hasBirthdays,
    isMyBirthday,
    todayHoliday,
    isHoliday,
  }
}
