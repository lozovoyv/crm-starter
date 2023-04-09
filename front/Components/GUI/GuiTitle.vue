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
    font-family: $project_font;
    text-align: left;
    box-sizing: border-box;
    font-size: 16px;
    font-weight: bold;
    color: $color_text_black;
    margin: 12px 0;
}
</style>
