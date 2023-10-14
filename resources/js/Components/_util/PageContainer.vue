<template>
<!--    <div class="container px-5 py-20 mx-auto">-->
<!--        <slot/>-->
<!--    </div>-->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
            <slot/>
        </div>
    </div>
</template>

<script>
export default {
    name: "PageContainer"
}
</script>
