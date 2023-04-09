<template>
    <IconUser class="access-indicator" :class="{'access-indicator__locked': locked, 'access-indicator__unlocked': !locked}" />
</template>

<script>
import IconUser from "@/Icons/IconUser.vue";

export default {
    components: {
        IconUser,
    },
    props: {
        locked: {type: Boolean, default: true}
    },
}
</script>

<style lang="scss">
@import "@/variables.scss";

.access-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    vertical-align: baseline;
    margin-right: 6px;
    top: -1px;
    color: $color_gray_darken_1;

    &__unlocked {
        color: $color_success;
    }

    &__locked {
        color: $color_error;
    }
}
</style>
