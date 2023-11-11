<template>
    <div>
        <!-- Success flash messages -->
        <div
            v-if="$page.props.flash.success && show"
            class="rounded-md bg-green-50 border border-green-700 p-4"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <CheckCircleIcon
                        class="h-5 w-5 text-green-400"
                        aria-hidden="true"
                    />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ $page.props.flash.success }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button
                            @click="show = false"
                            type="button"
                            class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50"
                        >
                            <span class="sr-only">Dismiss</span>
                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error flash messages -->
        <div
            v-if="
                ($page.props.flash.error ||
                    Object.keys($page.props.errors).length > 0) &&
                show &&
                !hideErrors
            "
            class="rounded-md border border-red-700 bg-red-50 p-4"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <XCircleIcon
                        class="h-5 w-5 text-red-400"
                        aria-hidden="true"
                    />
                </div>
                <div class="ml-3">
                    <p
                        v-if="$page.props.flash.error"
                        class="text-sm font-medium text-red-800"
                    >
                        {{ $page.props.flash.error }}
                    </p>
                    <div v-else>
                        <p class="text-sm font-medium text-red-800">
                            Some problems...
                        </p>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                <li
                                    v-for="(error, index) in $page.props.errors"
                                    :key="index"
                                >
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info messages -->
        <div
            v-if="$page.props.flash.info && show"
            class="rounded-md border border-blue-700 bg-blue-50 p-4"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <InformationCircleIcon
                        class="h-5 w-5 text-blue-400"
                        aria-hidden="true"
                    />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ $page.props.flash.info }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button
                            @click="show = false"
                            type="button"
                            class="inline-flex rounded-md bg-blue-50 p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-blue-50"
                        >
                            <span class="sr-only">Dismiss</span>
                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    CheckCircleIcon,
    XMarkIcon,
    XCircleIcon,
    InformationCircleIcon,
} from '@heroicons/vue/20/solid'
export default {
    props: {
        hideErrors: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        CheckCircleIcon,
        XMarkIcon,
        XCircleIcon,
        InformationCircleIcon,
    },

    data() {
        return {
            show: true,
        }
    },
    watch: {
        '$page.props.flash': {
            handler() {
                this.show = true
            },
            deep: true,
        },
    },
}
</script>
