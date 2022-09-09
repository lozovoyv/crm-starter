<template>
    <div class="application__user-menu">
        <div class="application__user-menu-button">
            <div class="application__user-menu-button-avatar">
                <div class="application__user-menu-button-avatar-wrapper">
                    <img v-if="user.avatar" :src="user.avatar" alt="avatar"/>
                    <IconUser v-else/>
                </div>
            </div>
            <div class="application__user-menu-button-name">
                <div class="application__user-menu-button-name-title">{{ user.name }}</div>
                <div class="application__user-menu-button-name-description">{{ user.email }}</div>
            </div>
            <div class="application__user-menu-button-toggle">
                <IconDropdown class="application__user-menu-button-toggle-icon" :class="{'application__user-menu-button-toggle-icon-dropped': dropped}"/>
            </div>
        </div>
        <!--
        <div class="application__user-menu-content">
            <div class="application__user-menu-content-header">
                <div class="application__user-menu-content-header-avatar"></div>
                <div class="application__user-menu-content-header-name">
                    <div class="application__user-menu-content-header-title">{{ user.name }}</div>
                    <div class="application__user-menu-content-header-description">{{ user.email }}</div>
                </div>
            </div>
            <div class="application__user-menu-content-action">Профиль</div>
            <div class="application__user-menu-content-action">Выход</div>
        </div>
        -->
    </div>
</template>

<script setup lang="ts">
import {User} from "@/Core/Types/User";
import IconUser from "@/Icons/IconUser.vue";
import IconDropdown from "@/Icons/IconDropdown.vue";
import {ref} from "vue";

const props = defineProps<{
    user: User,
}>();

const dropped = ref<boolean>(false);

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
        color: $color-black;

        &:hover {
            color: $color-default-lighten-1;
        }

        &-avatar {
            width: $menu_height;
            height: $menu_height;
            display: flex;
            align-items: center;
            justify-content: center;
            color: $color-gray-lighten-1;

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

            @media screen and(max-width: 768px) {
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
        top: 0;
        right: 0;
        background-color: $color-white;
        box-sizing: border-box;
        padding: 0;
        box-shadow: $shadow_1;
        border-radius: 2px;
        z-index: 250;

        &-header {
            &-avatar {
            }

            &-name {
            }

            &-title {
            }

            &-description {
            }
        }

        &-action {
        }
    }
}
</style>
