<template>
    <AppLayout title="Today">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Today
            </h2>
        </template>

        <PageContainer class="">
            <div class="mx-auto max-w-2xl text-center">
                <h1
                    class="text-5xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                >
                    {{ greeting }}
                </h1>
                <p class="mb-6 mt-2 text-lg leading-8 text-gray-600">
                    Today you have worked...
                </p>
                <p
                    class="text-5xl font-bold tracking-tight text-gray-900 sm:text-8xl"
                >
                    00:00:00
                </p>
                <hr class="my-10" />

                <div v-if="canSpecifyClockTime" class="my-10">
                   <TimePicker @update-time="handleUpdateTime" @manual-update="handleManualTimeChange" class="mx-auto" />
                </div>

                <div class="mt-10">
                    <MultiLoader
                        v-if="loading.clockLoading"
                        type="PulseLoader"
                    />
                    <button
                        v-else
                        :class="[
                            'px-4 py-2.5 md:py-3.5 md:px-5 text-xl inline-flex items-center justify-center rounded-md font-semibold shadow focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                            isClockedIn
                                ? 'bg-red-500 text-white hover:bg-red-700'
                                : 'bg-green-500 hover:bg-green-600',
                        ]"
                        @click="toggleClock"
                    >
                        {{ isClockedIn ? 'Clock out' : 'Clock in' }}
                    </button>

                </div>
            </div>

            <div>
                <ConfettiExplosion v-if="confettiVisible" class="mx-auto" />
            </div>
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import PageContainer from '@/Components/_util/PageContainer.vue'
import {computed, ref, reactive} from 'vue'
import PrimaryButton from '@/Components/buttons/PrimaryButton.vue'
import MultiLoader from '@/Components/loader/MultiLoader.vue'
import {useForm} from '@inertiajs/vue3'
import ConfettiExplosion from 'vue-confetti-explosion'
import TimePicker from "@/Components/TimePicker.vue";


const props = defineProps({
    isClockedIn: Boolean,
    canSpecifyClockTime: Boolean,
})

// If the user specifies a specific time
let timeHasBeenManuallySpecified = ref(false)
let manualClockTime = ref(null);

const form = useForm({
    isClockedIn: props.isClockedIn,
})

const currentHour = new Date().getHours()

// Reactive structure to store loading state
let loading = reactive({
    clockLoading: false,
    clockTimeoutId: null,
})

let confettiVisible = ref(false)

function explodeConfetti() {
    confettiVisible.value = true
    setTimeout(() => {
        confettiVisible.value = false
    }, 3000)
}

const toggleClock = () => {
    let wasClockingIn = props.isClockedIn

    // Set a timeout to only show loading after 1 second
    loading.clockTimeoutId = setTimeout(() => {
        loading.clockLoading = true
    }, 250)

    console.log("DATA: ", manualClockTime.value)
    form
        .transform((data) => {
            // Initialize a new object for transformed data
            let transformedData = { ...data };

            // Conditionally add clockTime only if manualClockTime exists and it's been manually specified
            if (manualClockTime.value && timeHasBeenManuallySpecified.value) {
                // Create a new object to avoid mutating the original data
                transformedData.clock_time = manualClockTime.value;
            }

            return transformedData

        })
        .post(route('time-records.store'), {
            preserveScroll: true,
            onFinish: () => {
                console.log("Finished")
                clearTimeout(loading.clockTimeoutId);
                loading.clockLoading = false;
            },
            onSuccess: () => {
                // Only call confetti if we are clocking out
                if (wasClockingIn) {
                    explodeConfetti();
                }
            },
        });
}

function handleUpdateTime(time) {
    // Update the clock in time
    manualClockTime.value = time
}

function handleManualTimeChange(){
    // Set the flag to true
    timeHasBeenManuallySpecified.value = true
}


const greeting = computed(() => {
    if (currentHour >= 5 && currentHour < 12) {
        return 'Good Morning'
    } else if (currentHour >= 12 && currentHour <= 16) {
        return 'Good Afternoon'
    } else if (currentHour > 16 && currentHour <= 20) {
        return 'Good Evening'
    } else {
        return 'Good Night'
    }
})
</script>
