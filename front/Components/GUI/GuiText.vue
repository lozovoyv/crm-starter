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
    font-family: $project_font;
    text-align: left;
    box-sizing: border-box;
    font-size: 14px;
    color: $color_text_black;
}
</style>
