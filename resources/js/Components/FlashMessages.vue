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
      role="alert"
      class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
      <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
      </svg>
      <span class="sr-only">Errors</span>
      <div>
        <span class="font-medium">Errors...</span>
        <ul class="mt-1.5 list-disc list-inside">
          <li v-for="(error, index) in $page.props.errors" :key="index">
            {{ error }}
          </li>
        </ul>
      </div>
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
      handler() {
        this.showSuccess = !!this.$page.props.flash.success;
        this.showErrorSingle = !!this.$page.props.flash.error;
        this.showInfo = !!this.$page.props.flash.info;
      },
      deep: true,
      immediate: true
    },
    '$page.props.errors': function () {
      this.showError = Object.keys(this.$page.props.errors).length > 0;
    },
  },
}
</script>
