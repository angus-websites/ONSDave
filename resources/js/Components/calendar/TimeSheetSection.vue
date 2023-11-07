<template>
    <div>
        <!-- Loading state -->
        <div v-if="isLoadingEntries === true" class="p-5 flex flex-row justify-center items-center h-full">
            <div class="flex flex-col items-center space-y-3">
                <MultiLoader type="GridLoader"  class=""/>
                <small class="text-gray-600">Loading data</small>
            </div>
        </div>

        <template v-else>
            <!-- No entries -->
            <div v-if="entries.length < 1">
                <MultiMessage message="No entries for this day" type="info" />
            </div>
            <!-- Display entries -->
            <div v-else>
                <!-- Day stats -->
                <div class="py-5 text-center">
                    <p>Total worked time: {{ totalWorkedTime }}</p>
                </div>
                <!-- Entries table -->
                <div class="mt-2 relative overflow-x-auto shadow-md dark:shadow-none sm:rounded-lg">
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
                            <td class="px-6 py-4 ">
                                {{ entry.start }}
                            </td>
                            <td v-if="entry.ongoing" class="px-6 py-4">
                                Ongoing
                            </td>
                            <td v-else class="px-6 py-4">
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

            <!-- Button -->
            <div class="">
                <PrimaryButton class="mt-7">
                    New entry
                </PrimaryButton>
            </div>

        </template>
    </div>
</template>
<script setup>
import MultiMessage from "@/Components/messages/MultiMessage.vue";
import PrimaryButton from "@/Components/buttons/PrimaryButton.vue";
import MultiLoader from "@/Components/loader/MultiLoader.vue";
import {computed, ref} from "vue";

let isLoadingEntries = ref(false);

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
    {
        id: 3,
        start: '18:00',
        ongoing: true,
        duration: '2h'
    },
]

// Create a computed property to calculate the total worked time
const totalWorkedTime = computed(() => {
    return "8h 15m"
})

</script>
