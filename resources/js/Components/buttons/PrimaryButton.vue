<template>
    <Link v-if="isLink" :class="[sizeClass, baseClasses]">
        <slot />
    </Link>
    <button v-bind="$attrs" v-else :class="[sizeClass, baseClasses]">
        <slot />
    </button>
</template>

<script>
export default {
    name: 'PrimaryButton',
    props: {
        size: {
            default: 'm',
        },
        isLink: {
            default: false,
        },
    },

    computed: {
        sizeClass() {
            switch (this.size) {
                case 'xl':
                    return 'px-4 py-2.5 md:py-3.5 md:px-5 text-xl'
                case 'l':
                    return 'px-3 py-2 md:py-3 md:px-5 text-md'
                case 's':
                    return 'px-2 py-1 text-sm'
                default:
                    return 'px-3 py-2 text-sm'
            }
        },

        baseClasses() {
            return 'inline-flex items-center justify-center rounded-md bg-primary font-semibold text-primary-900 shadow hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-700'
        },
    },
}
</script>
