
<template>
    <AppLayout title="History">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                History
            </h2>
        </template>

        <PageContainer>
            <HistoryCalendar />
        </PageContainer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContainer from "@/Components/_util/PageContainer.vue";
import HistoryCalendar from "@/Components/calendar/HistoryCalendar.vue";

const props = defineProps({
    timeRecords: Object,
})

</script>
