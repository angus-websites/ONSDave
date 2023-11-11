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
          class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
          <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">Error</span>


          <div v-if="$page.props.errors">
            <span class="font-medium">Errors...</span>
            <ul class="mt-1.5 list-disc list-inside">
              <li
                v-for="(error, index) in $page.props.errors"
                :key="index"
              >
                {{ error }}
              </li>
            </ul>
          </div>
          <div v-else-if="$page.props.flash.error">
            <span class="font-medium">{{ $page.props.flash.error }}</span>
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
