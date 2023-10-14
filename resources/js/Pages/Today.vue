
<template>
    <AppLayout title="Today">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Today
            </h2>
        </template>

        <PageContainer class="">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-8xl">00:00:00</h1>
                <hr class="my-10">
                <h2 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ greeting }}</h2>

                <div class="mt-10">
                    <PrimaryButton size="xl">Start</PrimaryButton>
                </div>

            </div>
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContainer from "@/Components/_util/PageContainer.vue";
import { computed } from 'vue';
import PrimaryButton from "@/Components/buttons/PrimaryButton.vue";

const currentHour = new Date().getHours();

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
