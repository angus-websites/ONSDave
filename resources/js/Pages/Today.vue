
<template>
    <AppLayout title="Today">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Today
            </h2>
        </template>

        <PageContainer class="">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ greeting }}</h2>
                <p class="mb-6 mt-2 text-lg leading-8 text-gray-600">Today you have worked...</p>
                <h1 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-8xl">00:00:00</h1>
                <hr class="my-10">

                <div class="mt-10">
                    <MultiLoader v-if="isLoading" type="PulseLoader" />
                    <PrimaryButton v-else size="xl" @click="toggleClock">{{ isClockedIn ? 'Stop' : 'Start' }}</PrimaryButton>
                </div>

            </div>
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContainer from "@/Components/_util/PageContainer.vue";
import { computed, ref } from 'vue';
import PrimaryButton from "@/Components/buttons/PrimaryButton.vue";
import MultiLoader from "@/Components/loader/MultiLoader.vue";
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    isClockedIn: Boolean,
})

const form = useForm({
    isClockedIn: props.isClockedIn,
});
const currentHour = new Date().getHours();
const isLoading = ref(false)

const toggleClock = () => {
    isLoading.value = true;
    form.post(route('time-records.store'), {
        preserveScroll: true,
        onFinish: () => isLoading.value = false,
    })
};

const greeting = computed(() => {
    if (currentHour >= 5 && currentHour < 12) {
        return "Good Morning";
    } else if (currentHour >= 12 && currentHour <= 16) {
        return "Good Afternoon";
    } else if (currentHour > 16 && currentHour <= 20) {
        return "Good Evening";
    } else {
        return "Good Night";
    }
});
</script>
