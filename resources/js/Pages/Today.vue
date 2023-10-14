
<template>
    <AppLayout title="Today">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Today
            </h2>
        </template>

        <PageContainer class="">
            <div class="px-6 py-24 sm:py-32 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">{{ greeting }}</h2>
                    <p class="mt-6 text-lg leading-8 text-gray-600">Welcome to the Today page</p>
                </div>
            </div>
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContainer from "@/Components/_util/PageContainer.vue";
import { computed } from 'vue';

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
