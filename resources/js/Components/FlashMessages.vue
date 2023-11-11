<template>
  <div class="my-5">
    <!-- Success flash messages -->
    <MultiMessage
      v-if="showSuccess && $page.props.flash.success"
      @dismiss="showSuccess = false"
      type="success"
      :message="$page.props.flash.success"
    />

    <!-- Multi error messages -->
    <div
      v-if="showError && Object.keys($page.props.errors).length > 0 && !hideErrors"
      class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
      role="alert"
    >
      <!-- Error message content -->
      <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <!-- SVG Path -->
      </svg>
      <span class="sr-only">Error</span>
      <span class="font-medium">Errors...</span>
      <ul class="mt-1.5 list-disc list-inside">
        <li v-for="(error, index) in $page.props.errors" :key="index">
          {{ error }}
        </li>
      </ul>
      <button @click="showError = false" class="ml-auto">Dismiss</button>
    </div>

    <!-- Single error message -->
    <MultiMessage
      v-if="showErrorSingle && $page.props.flash.error && !hideErrors"
      @dismiss="showErrorSingle = false"
      type="danger"
      :message="$page.props.flash.error"
    />

    <!-- Info messages -->
    <MultiMessage
      v-if="showInfo && $page.props.flash.info"
      @dismiss="showInfo = false"
      type="info"
      :message="$page.props.flash.info"
    />
  </div>
</template>

<script>
import MultiMessage from "./messages/MultiMessage.vue";

export default {
  props: {
    hideErrors: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    MultiMessage,
    // Other components
  },
  data() {
    return {
      showSuccess: true,
      showError: true,
      showErrorSingle: true,
      showInfo: true,
    }
  },
  watch: {
    '$page.props.flash': {
      deep: true,
    },
  },
}
</script>
