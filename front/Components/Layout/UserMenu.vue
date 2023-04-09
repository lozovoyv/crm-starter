<template>
    <div class="application__user-menu">
        <div class="application__user-menu-button" @click="toggle">
            <div class="application__user-menu-button-avatar">
                <div class="application__user-menu-button-avatar-wrapper">
                    <img v-if="user?.avatar" :src="user.avatar" alt="avatar"/>
                    <IconUserCircle v-else/>
                </div>
            </div>
            <div class="application__user-menu-button-name">
                <div class="application__user-menu-button-name-title">{{ user?.name }}</div>
                <div v-if="user?.position" class="application__user-menu-button-name-description">{{ user.position }}</div>
            </div>
            <div class="application__user-menu-button-toggle">
                <IconDropdown class="application__user-menu-button-toggle-icon" :class="{'application__user-menu-button-toggle-icon-dropped': dropped}"/>
            </div>
        </div>
        <div class="application__user-menu-content" :class="{'application__user-menu-content-shown': dropped}">
            <div @click.stop="false">
                <div v-if="user?.organization" class="application__user-menu-content-organization">{{ user.organization }}</div>
                <div v-if="user?.position" class="application__user-menu-content-position">{{ user.position }}</div>
                <div v-if="user?.organization || user?.position" class="application__user-menu-content-divider"/>
                <div v-if="user?.name" class="application__user-menu-content-title">{{ user.name }}</div>
                <div v-if="user?.email" class="application__user-menu-content-description">{{ user.email }}</div>
            </div>
            <div class="application__user-menu-content-divider"/>
            <div class="application__user-menu-content-actions">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {User} from "@/Core/Types/User";
import IconDropdown from "@/Icons/IconDropdown.vue";
import {nextTick, ref} from "vue";
import IconUserCircle from "@/Icons/IconUserCircle.vue";

const props = defineProps<{
    user: User | null,
}>();

const dropped = ref<boolean>(false);
let is_initiator: boolean = false;

function toggle(): void {
    dropped.value = !dropped.value;
    if (dropped.value) {
        is_initiator = true;
        nextTick((): void => {
            document.addEventListener('click', close, {passive: true, capture: false});
        });
    }
}

function close(): void {
    if (is_initiator) {
        is_initiator = false;
    } else if (dropped.value) {
        dropped.value = false;
        nextTick((): void => {
            document.removeEventListener('click', close);
        });

    }
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

$menu_height: $base_size_unit * 1.5;

.application__user-menu {
    position: relative;
    display: flex;
    flex-grow: 0;
    height: $menu_height;

    &-button {
        display: flex;
        cursor: pointer;
        padding-right: 5px;
        transition: color $animation $animation_time;
        color: $color_text_black;

        &:hover {
            color: $color_default_lighten_1;
        }

        &-avatar {
            width: $menu_height;
            height: $menu_height;
            display: flex;
            align-items: center;
            justify-content: center;
            color: $color_gray_lighten_1;

            &-wrapper {
                width: 80%;
                height: 80%;
                border-radius: 50%;
                overflow: hidden;

                & > img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
            }
        }

        &-name {
            height: $menu_height;
            padding: 5px 5px 5px 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            box-sizing: border-box;
            flex-direction: column;
            @media screen and (max-width: 768px) {
                display: none;
            }

            &-title {
                font-family: $project_font;
                font-size: 14px;
                height: math.div($menu_height - 10px, 2);
                color: inherit;
            }

            &-description {
                font-family: $project_font;
                font-size: 12px;
                height: math.div($menu_height - 10px, 2);
                color: inherit;
                opacity: 0.75;
            }
        }

        &-toggle {
            height: $menu_height;
            width: math.div($base_size_unit, 2);
            display: flex;
            align-items: center;
            justify-content: center;

            &-icon {
                color: inherit;
                transition: transform $animation $animation_time;
                width: math.div($base_size_unit, 3);

                &-dropped {
                    transform: rotate(-180deg);
                }
            }
        }
    }

    &-content {
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        top: $menu_height;
        right: 0;
        background-color: $color_white;
        box-sizing: border-box;
        padding: 5px 10px 10px;
        box-shadow: $shadow_1;
        border-radius: 0 0 2px 2px;
        z-index: 250;
        font-family: $project_font;
        color: $color_text_black;
        cursor: default;
        min-width: 200px;

        &:before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: $color_white;
        }

        &:after {
            content: '';
            position: absolute;
            top: -1px;
            left: 10px;
            width: calc(100% - 20px);
            height: 1px;
            background-color: $color_gray_lighten_2;
            opacity: 0.25;
        }

        &-shown {
            opacity: 1;
            visibility: visible;
        }


        &-organization {
            font-size: 14px;
            font-weight: bold;
        }

        &-position {
            font-size: 12px;
            color: $color_gray_darken_1;
        }

        &-divider {
            border-bottom: 1px solid transparentize($color_gray_lighten_2, 0.5);
            margin: 5px 0 3px;
        }

        &-title {
            font-size: 14px;
        }

        &-description {
            font-size: 12px;
            color: $color_gray_darken_1;
        }

        &-actions {
            display: flex;
            flex-direction: column;

            & > * {
                font-size: 14px;
                display: flex;
                align-items: center;
                height: 22px;
                cursor: pointer;
                transition: color $animation $animation_time;
                color: $color_text_black;
                text-decoration: none;

                &:hover {
                    color: $color_default_lighten_1;
                }

                & > svg {
                    width: 14px;
                    height: 14px;
                    margin: 2px 3px 0 0;
                }
            }
        }
    }
}
</style>
