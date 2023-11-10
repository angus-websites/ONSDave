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
import {ref, watchEffect} from 'vue'

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'info',
        validator: (value) =>
            ['info', 'success', 'danger', 'warning'].includes(value),
    },
})

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
    },
    success: {
        background: 'bg-green-50',
        icon: 'text-green-400',
        text: 'text-green-700',
    },
    danger: {
        background: 'bg-red-50',
        icon: 'text-red-400',
        text: 'text-red-700',
    },
    warning: {
        background: 'bg-yellow-50',
        icon: 'text-yellow-400',
        text: 'text-yellow-700',
    },
}

const iconComponent = ref(icons[props.type])
const messageClass = ref(colors[props.type].background + ' rounded-md p-4')
const iconColor = ref(colors[props.type].icon)
const textColor = ref(colors[props.type].text)

watchEffect(() => {
    iconComponent.value = icons[props.type]
    messageClass.value = colors[props.type].background + ' rounded-md p-4'
    iconColor.value = colors[props.type].icon
    textColor.value = colors[props.type].text
})
</script>
