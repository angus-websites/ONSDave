<template>
    <div>
        <div class="flex items-center text-gray-900">
            <button
                type="button"
                @click="previousMonth"
                class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500"
            >
                <span class="sr-only">Previous month</span>
                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
            </button>
            <div
                class="flex-auto text-sm font-semibold text-surface-800 dark:text-surface-50"
            >
                {{ currentMonthAndYearName }}
            </div>
            <button
                type="button"
                @click="nextMonth"
                class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500"
            >
                <span class="sr-only">Next month</span>
                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
            </button>
        </div>
        <div class="mt-6 grid grid-cols-7 text-xs leading-6 text-gray-500">
            <div>M</div>
            <div>T</div>
            <div>W</div>
            <div>T</div>
            <div>F</div>
            <div>Sat</div>
            <div>Sun</div>
        </div>
        <div
            class="isolate mt-2 grid grid-cols-7 gap-px rounded-lg bg-gray-200 text-sm shadow ring-1 ring-gray-200"
        >
            <button
                v-for="(day, dayIdx) in days"
                @click="handleDayClick(day)"
                role="gridcell"
                :aria-selected="day.isSelected ? 'true' : 'false'"
                :key="day.date"
                type="button"
                :tabindex="day.isCurrentMonth ? '0' : '-1'"
                :aria-label="day.isToday ? 'Today' : null"
                :class="[
                    'py-1.5 focus:z-10 relative',
                    day.isCurrentMonth
                        ? 'bg-white cursor-pointer hover:bg-gray-100'
                        : 'bg-gray-50 cursor-default',
                    (day.isSelected || day.isToday) && 'font-semibold',
                    day.isSelected && 'text-white',
                    !day.isSelected &&
                        day.isCurrentMonth &&
                        !day.isToday &&
                        'text-gray-900',
                    !day.isSelected &&
                        !day.isCurrentMonth &&
                        !day.isToday &&
                        'text-gray-400',
                    day.isToday && !day.isSelected && 'text-accent-600',
                    day.isToday && 'current-day',
                    dayIdx === 0 && 'rounded-tl-lg',
                    dayIdx === 6 && 'rounded-tr-lg',
                    dayIdx === days.length - 7 && 'rounded-bl-lg',
                    dayIdx === days.length - 1 && 'rounded-br-lg',
                ]"
            >
                <!-- The tiny dot -->
                <span
                    v-if="getDayStatus(day.date) === 'worked'"
                    class="absolute top-1 right-1 w-2 h-2 rounded-full bg-green-600 opacity-50"
                ></span>
                <span
                    v-if="getDayStatus(day.date) === 'leave'"
                    class="absolute top-1 right-1 w-2 h-2 rounded-full bg-red-600"
                ></span>
                <time
                    :datetime="day.date"
                    :class="[
                        'mx-auto flex h-7 w-7 items-center justify-center rounded-full',
                        day.isSelected && day.isToday && 'bg-accent-600',
                        day.isSelected && !day.isToday && 'bg-gray-900',
                    ]"
                >
                    {{ day.date.split('-').pop().replace(/^0/, '') }}
                </time>
            </button>
        </div>

        <SecondaryButton @click="goToToday" class="mt-8">Today</SecondaryButton>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, defineEmits} from 'vue'
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/20/solid'

import SecondaryButton from '@/Components/buttons/SecondaryButton.vue'

const props = defineProps({
    today: Object,
    monthSessions: Object,
})

// Define the custom event that will be emitted
const emit = defineEmits(['update:selected-day'])

let daysStatus = ref([])

// Update the daysStatus based on the monthSessions prop
const updateDaysStatus = () => {
    /**
     * Get the days that have worked sessions and add them to the daysStatus array
     */
    for (let day of props.monthSessions.days) {
        if (day.sessions.length > 0) {
            // check if records object is not empty
            daysStatus.value.push({date: day.date, type: 'worked'})
        }
    }
}

// Call the updateDaysStatus function when the component is mounted
onMounted(updateDaysStatus)

function compareDates(date1, date2) {
    /**
     * Compare two dates and return true if they are the same
     */
    const d1 = new Date(date1)
    const d2 = new Date(date2)

    return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
    )
}

const getDayStatus = (date) => {
    // Loop through the daysStatus array
    for (const i in daysStatus.value) {
        const dayStatus = daysStatus.value[i]
        if (compareDates(dayStatus.date, date)) {
            return dayStatus.type
        }
    }
    return null
}

const currentMonth = ref(props.today.getMonth())
const currentYear = ref(props.today.getFullYear())
const monthNames = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
]
const currentMonthAndYearName = computed(
    () => `${monthNames[currentMonth.value]} ${currentYear.value}`
)

const selectedDate = ref(props.today)

function getDaysInMonth(month, year) {
    return new Date(year, month + 1, 0).getDate()
}

function goToToday() {
    currentMonth.value = props.today.getMonth()
    currentYear.value = props.today.getFullYear()
    selectedDate.value = props.today
    days.value = generateDays(currentMonth.value, currentYear.value)

    // Emit the selected date to the parent component
    emit('update:selected-day', props.today)
}

function handleDayClick(day) {
    /**
     * This function is called when a day is clicked on the calendar.
     */

    // Check if the day is from the current month before selecting it
    if (day.isCurrentMonth) {
        selectedDate.value = new Date(day.date)
        days.value = generateDays(currentMonth.value, currentYear.value) // refresh days

        // Emit the selected date to the parent component
        emit('update:selected-day', selectedDate.value)
    }
}

function generateDays(month, year) {
    let days = []
    let startDay = (new Date(year, month, 1).getDay() + 6) % 7
    let endDay =
        (new Date(year, month, getDaysInMonth(month, year)).getDay() + 6) % 7
    let prevMonthDays = getDaysInMonth(month - 1, year)

    // Add days from the previous month to fill up the grid
    for (let i = startDay; i > 0; i--) {
        days.push({
            date: `${year}-${month}-${prevMonthDays - i + 1}`,
            isCurrentMonth: false,
        })
    }

    // Add the days of the current month
    for (let i = 1; i <= getDaysInMonth(month, year); i++) {
        let isToday =
            i === props.today.getDate() &&
            month === props.today.getMonth() &&
            year === props.today.getFullYear()
        let isSelected =
            selectedDate.value &&
            i === selectedDate.value.getDate() &&
            month === selectedDate.value.getMonth() &&
            year === selectedDate.value.getFullYear()
        days.push({
            date: `${year}-${month + 1}-${i}`,
            isCurrentMonth: true,
            isToday: isToday,
            isSelected: isSelected,
        })
    }

    // Add days from the next month to fill up the grid
    for (let i = 1; i <= 6 - endDay; i++) {
        days.push({date: `${year}-${month + 2}-${i}`, isCurrentMonth: false})
    }

    return days
}

const days = ref(generateDays(currentMonth.value, currentYear.value))

function previousMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value--
    } else {
        currentMonth.value--
    }
    days.value = generateDays(currentMonth.value, currentYear.value)
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else {
        currentMonth.value++
    }
    days.value = generateDays(currentMonth.value, currentYear.value)
}
</script>
