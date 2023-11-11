<template>
    <div :class="messageClass">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <component
                    :is="iconComponent"
                    class="h-5 w-5"
                    :class="iconColor"
                    aria-hidden="true"
                />
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
                <p :class="textColor">{{ message }}</p>
            </div>
            <button @click="dismiss" type="button" :class="closeColour" class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5  inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800  dark:hover:bg-gray-700"  aria-label="Close">
              <span class="sr-only">Close</span>
              <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
              </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import {
    InformationCircleIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/20/solid'
import {ref, watchEffect, defineEmits} from 'vue'

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'info',
        validator: (value) =>
            ['info', 'success', 'danger', 'warning'].includes(value),
    },
})

const emit = defineEmits(['dismiss'])

function dismiss(){
  emit('dismiss')
}

const icons = {
    info: InformationCircleIcon,
    success: CheckCircleIcon,
    danger: ExclamationCircleIcon,
    warning: ExclamationTriangleIcon,
}

const colors = {
    info: {
        background: 'bg-blue-50',
        icon: 'text-blue-400',
        text: 'text-blue-700',
        close: 'bg-blue-50 hover:bg-blue-200 text-blue-500 focus:ring-blue-400 dark:text-blue-400',
    },
    success: {
        background: 'bg-green-50',
        icon: 'text-green-400',
        text: 'text-green-700',
        close: 'bg-green-50 hover:bg-green-200 text-green-500 focus:ring-green-400 dark:text-green-400',

    },
    danger: {
        background: 'bg-red-50',
        icon: 'text-red-400',
        text: 'text-red-700',
        close: 'bg-red-50 hover:bg-red-200 text-red-500 focus:ring-red-400 dark:text-red-400',
    },
    warning: {
        background: 'bg-yellow-50',
        icon: 'text-yellow-400',
        text: 'text-yellow-700',
        close: 'bg-yellow-50 hover:bg-yellow-200 text-yellow-500 focus:ring-yellow-400 dark:text-yellow-400',
    },
}

const iconComponent = ref(icons[props.type])
const messageClass = ref(colors[props.type].background + ' rounded-md p-4')
const iconColor = ref(colors[props.type].icon)
const textColor = ref(colors[props.type].text)
const closeColour = ref(colors[props.type].close)
watchEffect(() => {
    iconComponent.value = icons[props.type]
    messageClass.value = colors[props.type].background + ' rounded-md p-4'
    iconColor.value = colors[props.type].icon
    textColor.value = colors[props.type].text
    closeColour.value = colors[props.type].close
})
</script>
