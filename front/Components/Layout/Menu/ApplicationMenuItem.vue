<template>
    <div class="application__menu-item" :class="{'application__menu-item-dropped': dropped}" @click="toggle">
        <span v-if="parent || !item.route" class="application__menu-item-link">
            <span>{{ item.title }}<IconDropdown class="application__menu-item-link-drop" v-if="parent"/></span>
        </span>
        <RouterLink v-else class="application__menu-item-link" :to="item.route">
            <span>{{ item.title }}</span>
        </RouterLink>
        <div v-if="parent" class="application__menu-submenu">
            <ApplicationMenuItem v-for="(menuItem, key) in item.items"
                                 :key="key"
                                 :item="menuItem"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import {MenuItem} from "../../../Core/Types/Menu";
import {computed, defineEmits, ref} from "vue";
import IconDropdown from "../../../Icons/IconDropdown.vue";

const props = defineProps<{
    item: MenuItem,
}>();

const emit = defineEmits<{
    (event: 'clicked'): void,
}>()

const parent = computed<boolean>((): boolean => {
    return typeof props.item.items !== 'undefined' && props.item.items.length > 0;
});

const dropped = ref<boolean>(false);

function toggle(event: PointerEvent): void {
    event.stopPropagation();
    if (!parent.value) {
        emit('clicked');
    }
}
</script>

<style lang="scss">
@import "@/variables.scss";

.application__menu {
    &-item {
        flex-grow: 0;
        font-family: $project_font;
        font-size: 14px;
        position: relative;
        color: $color-text-black;
        transition: color $animation $animation_time, background-color $animation $animation_time;
        display: flex;
        align-items: center;

        &-link {
            text-decoration: none;
            color: $color-text-black;
            box-sizing: border-box;
            cursor: pointer;
            white-space: nowrap;
            line-height: 22px;
            flex-grow: 1;

            & > span {
                padding: 0 14px 0 6px;
                display: flex;
                position: relative;
            }

            &-drop {
                position: absolute;
                top: 50%;
                right: 0;
                width: 8px;
                height: 8px;
                transition: transform $animation $animation_time;
                transform: translateY(-50%) rotate(-90deg);
            }
        }

        &-link:hover {
            color: $color-default-lighten-1;
        }

        &-link.router-link-exact-active {
            color: $color-default;
        }
    }

    &-submenu {
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        left: 0;
        bottom: 0;
        transform: translateY(100%);
        background-color: $color-white;
        box-sizing: border-box;
        padding: 8px 5px 12px;
        box-shadow: $shadow_2;
        border-radius: 0 0 3px 3px;
        min-width: 100%;
        z-index: 200;
    }
}

//.application__menu-item-0.application__menu-item-dropped .application__menu-submenu-0 {
//    opacity: 1;
//    visibility: visible;
//}
//
//.application__menu-item-0.application__menu-item-dropped > .application__menu-item-link .application__menu-item-link-drop,
//.application__menu-item-0.application__menu-item-dropped > .application__menu-item-no-link .application__menu-item-link-drop {
//    transform: rotate(-180deg);
//}
</style>
