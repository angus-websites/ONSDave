<template>
    <component
        :is="loaders[type]"
        :color="color"
        :size="size"
    />
</template>

<script setup>
import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import SyncLoader from 'vue-spinner/src/SyncLoader.vue';
import GridLoader from 'vue-spinner/src/GridLoader.vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['PulseLoader', 'SyncLoader', /* ... other loaders */].includes(value),
    },
    color: {
        type: String,
        default: "#009FA1",
    },
    size: {
        type: [String, Number],
        default: undefined,
    },
});

const loaders = {
    PulseLoader,
    SyncLoader,
    GridLoader
};
</script>
