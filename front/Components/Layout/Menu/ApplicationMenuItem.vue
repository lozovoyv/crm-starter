<template>
    <div class="application__menu-item" :class="{'application__menu-item-dropped': dropped}" @click="toggle">
        <span v-if="parent || !item.route" class="application__menu-item-link" @mouseenter="hovered" @click="click">
            <span>{{ item.title }}<IconDropdown class="application__menu-item-link-drop" :class="{'application__menu-item-link-drop-rotate': expanded && is_hidden}" v-if="parent"/></span>
        </span>
        <RouterLink v-else class="application__menu-item-link" :to="item.route" @mouseenter="hovered">
            <span>{{ item.title }}</span>
        </RouterLink>
        <div v-if="parent" class="application__menu-submenu" :class="{'application__menu-submenu-expanded': expanded, 'application__menu-submenu-is-hidden': is_hidden}">
            <ApplicationMenuItem v-for="(menuItem, key) in item.items"
                                 :is_hidden="is_hidden"
                                 :key="key"
                                 :item="menuItem"
                                 :toggleState="toggle_state_internal"
                                 @clicked="clicked"
                                 @hovered="internalHovered"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import {MenuItem} from "@/Core/Types/Menu";
import {computed, ref, watch} from "vue";
import IconDropdown from "@/Icons/IconDropdown.vue";

const props = defineProps<{
    item: MenuItem,
    toggleState: boolean,
    is_hidden?: boolean,
}>();

const emit = defineEmits<{
    (event: 'clicked'): void,
    (event: 'hovered'): void,
}>()

const parent = computed<boolean>((): boolean => {
    return typeof props.item.items !== 'undefined' && props.item.items.length > 0;
});

const expanded = ref<boolean>(false);
const toggle_state_internal = ref<boolean>(false);
const is_initiator = ref<boolean>(false);
const dropped = ref<boolean>(false);

function toggle(event: MouseEvent): void {
    event.stopPropagation();
    if (!parent.value) {
        emit('clicked');
    }
}

function hovered(): void {
    if(props.is_hidden) {
        return;
    }
    if (parent.value) {
        expanded.value = true;
    }
    is_initiator.value = true;
    emit('hovered');
}

function click() {
    if(!props.is_hidden) {
        return;
    }
    expanded.value = !expanded.value;
    is_initiator.value = true;
    emit('hovered');
}

watch(() => props.toggleState, () => {
    if (!is_initiator.value) {
        toggle_state_internal.value = !toggle_state_internal.value;
        expanded.value = false;
    }
    is_initiator.value = false;
});

function internalHovered(): void {
    toggle_state_internal.value = !toggle_state_internal.value;
}

function clicked(): void {
    emit('clicked');
}
</script>

<style lang="scss">
@import "@/variables.scss";

.application__menu {
    &-item {
        flex-grow: 0;
        @include font(15px);
        position: relative;
        color: $color_text_black;
        transition: color $animation $animation_time, background-color $animation $animation_time;

        &-link {
            text-decoration: none;
            color: $color_text_black;
            box-sizing: border-box;
            cursor: pointer;
            white-space: nowrap;
            line-height: 24px;
            width: 100%;
            padding: 0 3px 0 5px;
            display: flex;

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

                &-rotate{
                    transform: translateY(-50%) rotate(0deg);
                }
            }
        }

        &:first-child > &-link {
            padding-top: 7px;
        }

        &:last-child > &-link {
            padding-bottom: 7px;
        }

        &-link:hover {
            color: $color_default_lighten_1;
        }

        &-link.router-link-exact-active {
            color: $color_default;
        }
    }

    &-submenu {
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        top: 0;
        right: 3px;
        transform: translateX(100%);
        background-color: $color_white;
        box-sizing: border-box;
        padding: 0;
        box-shadow: $shadow_1;
        border-radius: 2px;
        min-width: 100%;
        z-index: 200;

        &-expanded {
            opacity: 1;
            visibility: visible;
            height: auto!important;
        }

        &-is-hidden{
            transition: unset;
            height: 0;
            position: relative;
            transform: unset;
            box-shadow: unset;
            right: 0;
            padding-left: 16px;
        }

        &-expanded#{&-is-hidden} {
            height: auto;
        }
    }
}
</style>
