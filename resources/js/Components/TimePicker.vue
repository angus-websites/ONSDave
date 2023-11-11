<template>
    <div class="flex flex-col items-center justify-center p-8 bg-gray-100 rounded-lg shadow-lg w-full lg:w-3/4 max-w-2xl">
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
                >
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
                >
                <button
                    class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                    @click="decrementMinutes"
                >
                    &#9660;
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, defineEmits } from 'vue';

const hours = ref('');
const minutes = ref('');
const manualInteraction = ref(false);
const emit = defineEmits(['update-time']);

const updateTimeEmit = () => {
    emit('updateTime', `${hours.value}:${minutes.value}`);
};

const updateTime = () => {
    if (!manualInteraction.value) {
        const now = new Date();
        hours.value = now.getHours().toString().padStart(2, '0');
        minutes.value = now.getMinutes().toString().padStart(2, '0');
        updateTimeEmit();
    }
};

watch([hours, minutes], updateTimeEmit);

onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
});

const incrementHours = () => {
    hours.value = (parseInt(hours.value) + 1) % 24;
    hours.value = hours.value.toString().padStart(2, '0');
    manualUpdate();
};

const decrementHours = () => {
    hours.value = (parseInt(hours.value) - 1 + 24) % 24;
    hours.value = hours.value.toString().padStart(2, '0');
    manualUpdate();
};

const incrementMinutes = () => {
    minutes.value = (parseInt(minutes.value) + 1) % 60;
    minutes.value = minutes.value.toString().padStart(2, '0');
    manualUpdate();
};

const decrementMinutes = () => {
    minutes.value = (parseInt(minutes.value) - 1 + 60) % 60;
    minutes.value = minutes.value.toString().padStart(2, '0');
    manualUpdate();
};

const manualUpdate = () => {
    manualInteraction.value = true;
    updateTimeEmit();
};
</script>

<style scoped>
/* Additional styles if needed */
</style>
