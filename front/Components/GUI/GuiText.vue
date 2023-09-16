<template>
    <div class="text">
        <slot/>
    </div>
</template>

<script>
export default {}
</script>

<style lang="scss">
@import "@/variables.scss";

.text {
    @include font(15px);
    text-align: left;
    box-sizing: border-box;
    color: $color_text_black;
}
</style>
