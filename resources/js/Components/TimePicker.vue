<template>
    <div
        class="relative flex flex-col items-center justify-center p-8 bg-gray-100 rounded-lg shadow-lg w-full lg:w-3/4 max-w-2xl"
    >
        <div class="flex items-center space-x-6">
            <!-- Hour Selector -->
            <div class="flex flex-col items-center">
                <button
                    class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                    @click="incrementHours"
                >
                    &#9650;
                </button>
                <input
                    type="text"
                    class="w-24 text-center text-2xl border-2 border-gray-300 rounded-lg p-3 mt-2 mb-2"
                    v-model="hours"
                    @input="manualUpdate"
                />
                <button
                    class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                    @click="decrementHours"
                >
                    &#9660;
                </button>
            </div>

            <span class="text-3xl">:</span>

            <!-- Minute Selector -->
            <div class="flex flex-col items-center">
                <button
                    class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                    @click="incrementMinutes"
                >
                    &#9650;
                </button>
                <input
                    type="text"
                    class="w-24 text-center text-2xl border-2 border-gray-300 rounded-lg p-3 mt-2 mb-2"
                    v-model="minutes"
                    @input="manualUpdate"
                />
                <button
                    class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                    @click="decrementMinutes"
                >
                    &#9660;
                </button>
            </div>
        </div>

      <button v-if="manualInteraction" @click="resetTime" type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-600 bg-zinc-200 hover:bg-zinc-300 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>
        <span class="sr-only">Reset time</span>
      </button>
    </div>
</template>

<script setup>
import {ref, watch, onMounted, onUnmounted, defineEmits} from 'vue'

let hours = ref('')
let minutes = ref('')
let manualInteraction = ref(false)
let inactivityTimer = ref(null)
const emit = defineEmits(['update-time', 'manual-update'])

const resetInactivityTimer = () => {
    clearTimeout(inactivityTimer.value)
    inactivityTimer.value = setTimeout(() => {
        console.log('Inactivity timer triggered')
        manualInteraction.value = false
        emit('manual-update', false)
        updateTime()
    }, 30000) // 30 seconds
}

const updateTimeEmit = () => {
    // Create a dateime object from the time string
    const now = new Date()
    const time = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate(),
        hours.value,
        minutes.value
    )
    emit('update-time', time)
}

const updateTime = () => {
    if (!manualInteraction.value) {
        const now = new Date()
        hours.value = now.getHours().toString().padStart(2, '0')
        minutes.value = now.getMinutes().toString().padStart(2, '0')
        updateTimeEmit()
    }
}

const resetTime = () => {
  /**
   * Reset the time to the current time
   */
  const now = new Date();
  hours.value = now.getHours().toString().padStart(2, '0');
  minutes.value = now.getMinutes().toString().padStart(2, '0');
  manualInteraction.value = false; // Reset manual interaction flag
  updateTimeEmit();
};

watch([hours, minutes], updateTimeEmit)

onMounted(() => {
    updateTime()
    setInterval(updateTime, 1000) // Update time every second
    resetInactivityTimer() // Initialize inactivity timer
})

onUnmounted(() => {
    clearTimeout(inactivityTimer.value) // Clear timer when component unmounts
})

const incrementHours = () => {
    hours.value = (parseInt(hours.value) + 1) % 24
    hours.value = hours.value.toString().padStart(2, '0')
    manualUpdate()
}

const decrementHours = () => {
    hours.value = (parseInt(hours.value) - 1 + 24) % 24
    hours.value = hours.value.toString().padStart(2, '0')
    manualUpdate()
}

const incrementMinutes = () => {
    minutes.value = (parseInt(minutes.value) + 1) % 60
    minutes.value = minutes.value.toString().padStart(2, '0')
    manualUpdate()
}

const decrementMinutes = () => {
    minutes.value = (parseInt(minutes.value) - 1 + 60) % 60
    minutes.value = minutes.value.toString().padStart(2, '0')
    manualUpdate()
}

const manualUpdate = () => {
    emit('manual-update', true)
    manualInteraction.value = true
    updateTimeEmit()
    resetInactivityTimer() // Reset the timer on each user interaction
}
</script>

<style scoped>
/* Additional styles if needed */
</style>
