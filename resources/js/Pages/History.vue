<template>
    <AppLayout title="History">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                History
            </h2>
        </template>

        <PageContainer>
            <div>
                <h2 class="h3">
                    {{ displayedDate }}
                    <small class="text-base">{{ displayedMonthYear }}</small>
                </h2>
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-16">
                    <!-- Calendar -->
                    <MonthCalendar
                        @update:selected-day="handleSelectedDay"
                        :today="today"
                        :monthSessions="monthSessions.data"
                        class="mt-10 text-center lg:col-start-8 lg:col-end-13 lg:row-start-1 lg:mt-9 xl:col-start-8"
                    />

                    <!-- Timesheet section -->
                    <TimeSheetSection
                        :selectedDate="selectedDate"
                        :monthSessions="monthSessions.data"
                        class="mt-10 lg:col-span-7 xl:col-span-7"
                    />
                </div>
            </div>
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import PageContainer from '@/Components/_util/PageContainer.vue'
import MonthCalendar from '@/Components/calendar/MonthCalendar.vue'
import TimeSheetSection from '@/Components/calendar/TimeSheetSection.vue'

import {computed, ref} from 'vue'

const props = defineProps({
    monthSessions: Object,
})

const today = ref(new Date())
const selectedDate = ref(today.value)

const displayedDate = computed(() => {
    const options = {weekday: 'short', day: 'numeric'}
    return selectedDate.value
        ? selectedDate.value.toLocaleDateString(undefined, options)
        : today.value.toLocaleDateString(undefined, options)
})

const displayedMonthYear = computed(() => {
    const options = {month: 'short', year: 'numeric'}
    return selectedDate.value
        ? selectedDate.value.toLocaleDateString(undefined, options)
        : today.value.toLocaleDateString(undefined, options)
})

function handleSelectedDay(selectedDay) {
    /**
     * Handler for the emit event from the MonthCalendar component
     */
    selectedDate.value = selectedDay
}
</script>
