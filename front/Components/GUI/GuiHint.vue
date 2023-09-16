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
    @include font(14px);
    color: $color_gray_darken_1;
}
</style>
