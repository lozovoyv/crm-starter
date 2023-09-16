<template>
    <div class="title">
        <slot/>
    </div>
</template>

<script>
export default {}
</script>

<style lang="scss">
@import "@/variables.scss";

.title {
    @include font(16px, 600);
    text-align: left;
    box-sizing: border-box;
    color: $color_text_black;
    margin: 12px 0;
}
</style>
