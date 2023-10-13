<script setup>
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';

defineProps({
    title: String,
});

</script>

<template>
    <Head :title="title" />

    <Banner />

    <div class="body bg-surface-50 text-surface-800 dark:bg-surface-900 dark:text-surface-100">
        <slot />
    </div>
</template>
