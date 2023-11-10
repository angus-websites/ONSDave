<template>
    <div>
        <!-- Loading state -->
        <div
            v-if="isLoadingEntries === true"
            class="p-5 flex flex-row justify-center items-center h-full"
        >
            <div class="flex flex-col items-center space-y-3">
                <MultiLoader type="GridLoader" class="" />
                <small class="text-gray-600">Loading data</small>
            </div>
        </div>

        <template v-else>
            <!-- No entries -->
            <div v-if="entries.length < 1">
                <!-- Day stats -->
                <div class="py-5 text-center">
                    <p>Total worked time: 0</p>
                </div>
                <!-- Entries table -->
                <div
                    class="mt-2 relative overflow-x-auto shadow-md dark:shadow-none sm:rounded-lg"
                >
                    <MultiMessage
                        message="No entries for this day"
                        type="info"
                    />
                </div>
            </div>
            <!-- Display entries -->
            <div v-else>
                <!-- Day stats -->
                <div class="py-5 text-center">
                    <p>Total worked time: {{ totalWorkedTime }}</p>
                </div>
                <!-- Entries table -->
                <div
                    class="mt-2 relative overflow-x-auto shadow-md dark:shadow-none sm:rounded-lg"
                >
                    <table
                        class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                    >
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">Clock in</th>
                                <th scope="col" class="px-6 py-3">Clock out</th>
                                <th scope="col" class="px-6 py-3">Duration</th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="entry in entries"
                                :key="entry.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                            >
                                <td class="px-6 py-4">
                                    {{ entry.clock_in }}
                                </td>
                                <td v-if="entry.ongoing" class="px-6 py-4">
                                    Ongoing
                                </td>
                                <td v-else class="px-6 py-4">
                                    {{ entry.clock_out }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ entry.duration }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="#"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                        >Edit</a
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Button -->
            <div class="">
                <PrimaryButton class="mt-7"> New entry </PrimaryButton>
            </div>
        </template>
    </div>
</template>
<script setup>
import MultiMessage from '@/Components/messages/MultiMessage.vue'
import PrimaryButton from '@/Components/buttons/PrimaryButton.vue'
import MultiLoader from '@/Components/loader/MultiLoader.vue'
import {computed, ref} from 'vue'

const props = defineProps({
    selectedDate: Date,
    monthSessions: Object,
})

let isLoadingEntries = ref(false)

const entries = computed(() => {
    // Find the first session entry that matches the selected date
    const selectedEntry = props.monthSessions['days'].find((session) => {
        // Create a date object from the session date string
        const sessionDate = new Date(session.date)

        // Assuming props.selectedDate is a Date object
        const selectedDate = props.selectedDate

        // Return if the session date is the same as the selected date
        return sessionDate.getDate() === selectedDate.getDate()
    })

    // Return the 'sessions' from the matching entry or an empty array if no match is found
    return selectedEntry ? selectedEntry.sessions : []
})

// Create a computed property to calculate the total worked time
const totalWorkedTime = computed(() => {
    // Create a variable to store the total time
    let total_in_seconds = 0

    // Loop through the entries
    for (let entry of entries.value) {
        // Get the duration in seconds property
        const duration = entry['duration_in_seconds']
        total_in_seconds += duration
    }

    return total_in_seconds
})
</script>
