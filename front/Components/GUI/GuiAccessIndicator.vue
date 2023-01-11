<template>
    <IconUserSlash class="access-indicator access-indicator__locked" v-if="locked"/>
    <IconUser class="access-indicator access-indicator__unlocked" v-else/>
</template>

<script>
import IconUserSlash from "@/Icons/IconUserSlash";
import IconUser from "@/Icons/IconUser";

export default {
    components: {
        IconUser,
        IconUserSlash,
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
