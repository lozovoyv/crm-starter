<template>
    <div class="value" :class="classProxy">
        <span class="value__title" v-if="title">{{ title }}</span>
        <div class="value__value">
            <slot/>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {type: String, default: null},
        dots: {type: Boolean, default: false},
    },

    computed: {
        classProxy() {
            let cls = [];
            cls.push('value__dotted')
            return cls;
        }
    }
}
</script>

<style lang="scss">
@import "@/variables.scss";

.value {
    width: 100%;
    display: flex;
    flex-direction: row;
    margin: 8px 0;
    font-family: $project_font;

    &__dotted {
        border-bottom: 1px dashed transparentize($color_gray_lighten_2, 0.5);
    }

    &__title {
        width: 180px;
        flex-shrink: 0;
        font-size: 14px;
        color: transparentize($color_text_black, 0.25);
        display: flex;
        align-items: center;
    }

    &__value {
        flex-grow: 1;
        font-size: 14px;
        color: $color_text_black;
    }
}
</style>
