<template>
    <div class="login__wrapper">
        <div class="login">
            <LoadingProgress :loading="form.is_loading || form.is_saving">
                <div class="login__logo">
                    <div class="login__logo-img">
                        <Logo/>
                    </div>
                    <div class="login__logo-text">
                        <div class="login__logo-text-title">CRM</div>
                        <div class="login__logo-text-description">Вход в систему</div>
                    </div>
                </div>
                <div class="login__divider"></div>
                <div class="login__form">
                    <FormString :form="form" name="username" :clearable="true" autocomplete="username" @keyup.enter="enter" ref="login_input"/>
                    <FormPassword :form="form" name="password" :clearable="true" autocomplete="current-password" @keyup.enter="enter" ref="password_input"/>
                    <FormCheckBox :form="form" :name="'remember'"/>
                </div>
                <div class="login__actions">
                    <span class="link" @click="forgot">Забыл пароль</span>
                    <GuiButton type="default" @clicked="login">Войти</GuiButton>
                </div>
            </LoadingProgress>
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingProgress from "../Components/LoadingProgress.vue";
import Logo from "./Logo.vue";
import FormString from "../Components/Form/FormString.vue";
import FormPassword from "../Components/Form/FormPassword.vue";
import FormCheckBox from "../Components/Form/FormCheckBox.vue";
import GuiButton from "../Components/GUI/GuiButton.vue";
import {useRouter, useRoute} from 'vue-router';
import {onMounted, ref} from "vue";
import {Form} from "../Core/Form";
import dialog from "../Core/Dialog/Dialog";

const props = defineProps<{
    message?: string,
}>();

const router = useRouter();
const route = useRoute();
const login_input = ref<FormString | null>(null);
const password_input = ref<FormPassword | null>(null);
const form = ref<Form>(new Form(null, null, '/api/login'));

form.value.set('username', null, 'required', 'Имя пользователя', true);
form.value.set('password', null, 'required', 'Пароль', true);
form.value.set('remember', null, null, 'Запомнить меня', true);
form.value.load();

onMounted(() => {
    login_input.value.focus();
})

function enter() {
    if (form.value.values['username'] === null) {
        login_input.value.focus();
    } else if (form.value.values['password'] === null) {
        password_input.value.focus();
    } else {
        login();
    }
}

function login() {
    if (form.value.is_saving || !form.value.validate()) {
        return;
    }
    form.value.save(null, true)
        .then(() => {
            if (route.redirectedFrom) {
                router.replace(route.redirectedFrom);
            } else {
                router.replace({name: 'home'});
            }
        })
        .catch(error => {
            if (error.code === 301) {
                window.location.href = error.response.data.to;
            }
        });
}

function forgot() {
    dialog.show('Восстановление пароля', 'Чтобы восстановить доступ в систему обратитесь к администратору', [dialog.button('ok', 'OK', 'default')]);
}
</script>

<style lang="scss">
@import "../variables";

.login {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: calc(100% - 4px);
    margin: 0 2px;
    max-width: 400px;
    box-shadow: $shadow_1;
    background-color: #fff;
    border-radius: 3px;
    box-sizing: border-box;
    padding: 20px;

    &__wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    &__logo {
        width: 100%;
        height: auto;
        display: flex;

        &-img {
            width: 50px;
        }

        &-text {
            font-family: $project_font;
            color: $color-text-black;
            box-sizing: border-box;
            padding-left: 10px;
            flex-grow: 1;

            &-title {
                font-size: 20px;
                font-weight: bold;
            }

            &-description {
                margin-top: 5px;
                font-size: 16px;
            }
        }
    }

    &__divider {
        width: 100%;
        border-bottom: 1px solid #d9d9d9;
        margin: 15px 0 20px;
    }

    &__actions {
        margin-top: 15px;
        text-align: right;

        & > *:not(:last-child) {
            margin-right: 20px;
        }
    }

    .input-field__title {
        width: 150px;
    }
}

.link {
    text-decoration: none;
    color: $color-default;
    cursor: pointer;
    transition: color $animation $animation_time;
    font-family: $project_font;
    font-size: 15px;

    &__bold {
        font-weight: bold;
    }

    &:hover {
        color: lighten($color-default, 5%);
    }
}
</style>
