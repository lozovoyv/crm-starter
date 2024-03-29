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
                <UserMenu :user="user">
                    <RouterLink :to="{name: 'profile'}">
                        <IconUser/>
                        Профиль
                    </RouterLink>
                    <span @click="logout"><IconExit/> Выход</span>
                </UserMenu>
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
import {Menu, MenuItem} from "@/Core/Types/Menu";
import UserMenu from "@/Components/Layout/UserMenu.vue";
import IconUser from "@/Icons/IconUser.vue";
import IconExit from "@/Icons/IconExit.vue";
import dialog from "@/Core/Dialog/Dialog";
import {http} from "@/Core/Http/Http";
import toaster from "@/Core/Toaster/Toaster";
import {can} from "@/Core/Can";

const store = useStore();

const user = computed((): User => {
    return <User>store.getters['user/user'];
});

const organization = computed((): string | null | undefined => {
    return user.value?.organization;
});

const menu = computed((): Menu => {
    return filterMenu(menuSrc);
})

function logout(): void {
    dialog.show('Подтверждение', 'Выйти из системы?', [dialog.button('cancel', 'Отмена'), dialog.button('ok', 'Выйти', 'default')])
        .then(result => {
            if (result === 'ok') {
                http.post('/api/auth/logout', {})
                    .then(() => {
                        window.location.reload();
                    })
                    .catch(error => {
                        toaster.error(error.response.data['message']);
                    });
            }
        })
}

function filterMenu(items: Menu): Menu {
    let filtered: Menu = [];
    items.map((item: MenuItem) => {
        let filteredItem: MenuItem | null = filterMenuItem(item);
        if (filteredItem !== null) {
            filtered.push(filteredItem);
        }
    });

    return filtered;
}

function filterMenuItem(item: MenuItem): MenuItem | null {
    if (item.permission !== undefined && !can(item.permission)) {
        return null;
    }
    let subMenu: Menu | undefined = undefined;
    if (item.items !== undefined) {
        subMenu = filterMenu(item.items);
    }
    if (subMenu !== undefined && subMenu.length === 0) {
        return null;
    }
    return {
        title: item.title,
        items: subMenu,
        route: item.route,
        permission: item.permission,
    };
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

$header_height: $base_size_unit * 5;
html {
    min-height: 100%;
    display: flex;
}

body {
    flex-grow: 1;
    flex-shrink: 0;
}

body, html {
    width: 100%;
    min-width: 300px;
    margin: 0;
}

#application {
    width: 100%;
    height: 100%;
    background-color: $page_background;
    display: flex;
    flex-direction: column;
}

.application {
    &__header {
        height: $header_height;
        box-shadow: $shadow_2;
        flex-shrink: 0;
        flex-grow: 0;
        background-color: $color_white;

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
                @include font(16px, 600);
                color: $color_text_black;
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
