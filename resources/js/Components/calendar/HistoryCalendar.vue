<template>
    <div>
        <h2 class="h3">14th October</h2>
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-16">

            <!-- Calendar -->
            <div class="mt-10 text-center lg:col-start-8 lg:col-end-13 lg:row-start-1 lg:mt-9 xl:col-start-8">

                <div class="flex items-center text-gray-900">
                    <button type="button" @click="previousMonth" class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Previous month</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                    <div class="flex-auto text-sm font-semibold text-surface-800 dark:text-surface-50">{{ currentMonthAndYearName }}</div>
                    <button type="button" @click="nextMonth" class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
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
                <div class="isolate mt-2 grid grid-cols-7 gap-px rounded-lg bg-gray-200 text-sm shadow ring-1 ring-gray-200">
                    <button v-for="(day, dayIdx) in days"  :key="day.date" type="button" :class="['py-1.5 hover:bg-gray-100 focus:z-10', day.isCurrentMonth ? 'bg-white' : 'bg-gray-50', (day.isSelected || day.isToday) && 'font-semibold', day.isSelected && 'text-white', !day.isSelected && day.isCurrentMonth && !day.isToday && 'text-gray-900', !day.isSelected && !day.isCurrentMonth && !day.isToday && 'text-gray-400', day.isToday && !day.isSelected && 'text-indigo-600', dayIdx === 0 && 'rounded-tl-lg', dayIdx === 6 && 'rounded-tr-lg', dayIdx === days.length - 7 && 'rounded-bl-lg', dayIdx === days.length - 1 && 'rounded-br-lg']">
                        <time :datetime="day.date" :class="['mx-auto flex h-7 w-7 items-center justify-center rounded-full', day.isSelected && day.isToday && 'bg-indigo-600', day.isSelected && !day.isToday && 'bg-gray-900']">{{ day.date.split('-').pop().replace(/^0/, '') }}</time>
                    </button>
                </div>
                <button type="button" class="mt-8 w-full rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600" @click="goToToday">Go to Today</button>
            </div>

            <!-- Timsheet section -->
            <div class="mt-10 lg:col-span-7 xl:col-span-7">

                <!-- Day stats -->
                <div class="py-5 text-center">
                   <p>Total worked time: 8h 15</p>
                </div>
                <!-- Entries table -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Clock in
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Clock out
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in entries" :key="entry.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 ">
                                <td scope="row" class="px-6 py-4 ">
                                    {{ entry.start }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ entry.end }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ entry.duration }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
    CalendarIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    EllipsisHorizontalIcon,
    MapPinIcon,
} from '@heroicons/vue/20/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const entries = [
    {
        id: 1,
        start: '8:00',
        end: '12:00',
        duration: '4h'
    },
    {
        id: 2,
        start: '12:45',
        end: '17:00',
        duration: '4h 15m'
    },
]
const today = ref(new Date());
const currentMonth = ref(today.value.getMonth());
const currentYear = ref(today.value.getFullYear());

const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

const currentMonthAndYearName = computed(() => `${monthNames[currentMonth.value]} ${currentYear.value}`);


function getDaysInMonth(month, year) {
    return new Date(year, month + 1, 0).getDate();
}

function goToToday() {
    currentMonth.value = today.value.getMonth();
    currentYear.value = today.value.getFullYear();
    days.value = generateDays(currentMonth.value, currentYear.value);
}



function generateDays(month, year) {
    let days = [];
    let startDay = (new Date(year, month, 1).getDay() + 6) % 7;
    let endDay = (new Date(year, month, getDaysInMonth(month, year)).getDay() + 6) % 7;
    let prevMonthDays = getDaysInMonth(month - 1, year);

    // Add days from the previous month to fill up the grid
    for (let i = startDay; i > 0; i--) {
        days.push({ date: `${year}-${month + 1}-${prevMonthDays - i + 1}`, isCurrentMonth: false });
    }

    // Add the days of the current month
    for (let i = 1; i <= getDaysInMonth(month, year); i++) {
        let isToday = i === today.value.getDate() && month === today.value.getMonth() && year === today.value.getFullYear();
        days.push({ date: `${year}-${month + 1}-${i}`, isCurrentMonth: true, isToday: isToday });
    }

    // Add days from the next month to fill up the grid
    for (let i = 1; i <= 6 - endDay; i++) {
        days.push({ date: `${year}-${month + 2}-${i}`, isCurrentMonth: false });
    }

    return days;
}

const days = ref(generateDays(currentMonth.value, currentYear.value));

function previousMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
    days.value = generateDays(currentMonth.value, currentYear.value);
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
    days.value = generateDays(currentMonth.value, currentYear.value);
}

</script>
