<template>
    <div class="hint">
        <slot/>
    </div>
</template>

<script>
export default {}
</script>

<style lang="scss">
@import "@/variables.scss";

.hint {
    font-family: $project_font;
    font-size: 13px;
    color: $color_gray_darken_1;
}
</style>
