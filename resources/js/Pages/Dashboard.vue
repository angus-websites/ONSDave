<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import PageContainer from '@/Components/_util/PageContainer.vue'
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <PageContainer>
            <p class="dark:text-white">Dashboard</p>
        </PageContainer>
    </AppLayout>
</template>
