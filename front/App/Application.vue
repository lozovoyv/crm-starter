<template>
    <div class="application__header" v-if="user">
        <div class="application__header-wrapper">
            <router-link :to="{name: 'home'}" class="application__header-title">
                <div class="application__header-title-logo">
                    <Logo/>
                </div>
                <div v-if="organization" class="application__header-title-text">{{ organization }}</div>
            </router-link>
            <div class="application__header-menu">
                <ApplicationMenu :menu="menu"/>
            </div>
            <div class="application__header-widgets"></div>
            <div class="application__header-user-menu" v-if="user">
                <UserMenu :user="user"></UserMenu>
            </div>
        </div>
    </div>

    <div class="application__body">
        <RouterView/>
    </div>
    <div class="application__footer">

    </div>
</template>

<script setup lang="ts">
import {useStore} from "vuex";
import {computed} from "vue";
import {User} from "@/Core/Types/User";
import Logo from "@/App/Logo.vue";
import menuSrc from "@/App/menu";
import ApplicationMenu from "@/Components/Layout/Menu/ApplicationMenu.vue";
import {Menu} from "@/Core/Types/Menu";
import UserMenu from "@/Components/Layout/UserMenu.vue";

const store = useStore();
const user = computed((): User | null => {
    return store.getters['user/user'];
});
const organization = computed((): string|null|undefined => {
    return user.value?.organization;
});
const menu = computed((): Menu => {
//     todo add menu filtering
    return menuSrc;
})
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

$header_height: $base_size_unit * 1.5;

body, html {
    width: 100%;
    height: 100%;
    min-width: 300px;
    margin: 0;
}

#application {
    width: 100%;
    height: 100%;
    background-color: #f0f0f3;
    display: flex;
    flex-direction: column;
}

.application {
    &__header {
        height: $header_height;
        box-shadow: $shadow_1;
        flex-shrink: 0;
        flex-grow: 0;
        background-color: $color-white;

        &-wrapper {
            display: flex;
            flex-shrink: 1;
            flex-grow: 1;
            height: 100%;
        }

        &-title {
            flex-grow: 0;
            flex-shrink: 0;
            display: flex;
            text-decoration: none;
            margin-right: 15px;

            &-logo {
                width: $header_height;
                height: $header_height;
                position: relative;

                & > svg {
                    width: 80%;
                    height: 80%;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    transition: transform $animation_bounce $animation_time;
                }
            }

            &-text {
                font-family: $project_font;
                font-size: 16px;
                font-weight: bold;
                color: $color-text-black;
                line-height: $header_height;
            }

            &:hover &-logo > svg {
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        &-menu {
            flex-grow: 1;
            flex-shrink: 1;
        }

        &-widgets {
            //width: 50px;
        }

        &-user-menu {

        }
    }

    &__body {
        padding: 1px;
        flex-shrink: 0;
        flex-grow: 1;
    }

    &__footer {
        flex-shrink: 0;
        flex-grow: 0;
    }
}
</style>
