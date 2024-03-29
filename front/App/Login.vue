<template>
    <div class="login__wrapper">
        <div class="login">
            <LoadingProgress :loading="form.state.is_loading || form.state.is_saving">
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
                    <FormString :form="form" name="username" autocomplete="username" :clearable="true" @keyup.enter="enter" ref="login_input"/>
                    <FormPassword :form="form" name="password" autocomplete="current-password" :clearable="true" @keyup.enter="enter" ref="password_input"/>
                    <FormCheckBox :form="form" :name="'remember'"/>
                </div>
                <div class="login__actions">
                    <span class="link" @click="forgot">Забыл пароль</span>
                    <GuiButton type="default" @click="login">Войти</GuiButton>
                </div>
            </LoadingProgress>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useRouter, useRoute} from 'vue-router';
import {onMounted, ref} from "vue";
import {Form} from "@/Core/Form";
import dialog from "@/Core/Dialog/Dialog";
import LoadingProgress from "@/Components/LoadingProgress.vue";
import Logo from "@/App/Logo.vue";
import FormString from "@/Components/Form/FormString.vue";
import FormPassword from "@/Components/Form/FormPassword.vue";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";
import GuiButton from "@/Components/GUI/GuiButton.vue";
import {useStore} from "vuex";
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const props = defineProps<{
    message?: string,
}>();

const store = useStore();
const router = useRouter();
const route = useRoute();
const login_input = ref<InstanceType<typeof FormString> | undefined>(undefined);
const password_input = ref<InstanceType<typeof FormPassword> | undefined>(undefined);

const form = ref<Form>(new Form({save_url: apiEndPoint('post', '/api/auth/login')}));

form.value.set('username', null, 'required', 'Адрес электронной почты / логин', true);
form.value.set('password', null, 'required', 'Пароль', true);
form.value.set('remember', null, null, 'Запомнить меня', true);
form.value.setLoaded();

onMounted(() => {
    login_input.value?.focus();
})

function enter() {
    if (form.value.values['username'] === null) {
        login_input.value?.focus();
    } else if (form.value.values['password'] === null) {
        password_input.value?.focus();
    } else {
        login();
    }
}

function login() {
    if (form.value.state.is_saving || !form.value.validate()) {
        return;
    }
    form.value.save(undefined, true)
        .then(() => {
            store.dispatch('user/refresh').then(() => {
                if (route.redirectedFrom) {
                    router.replace(route.redirectedFrom);
                } else {
                    router.replace({name: 'home'});
                }
            });
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
@import "@/variables";

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
            color: $color_text_black;
            box-sizing: border-box;
            padding-left: 10px;
            flex-grow: 1;

            &-title {
                @include font(20px, 600);
            }

            &-description {
                margin-top: 5px;
                @include font(16px);
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
    color: $color_default;
    cursor: pointer;
    transition: color $animation $animation_time;
    @include font(15px);

    &__bold {
        font-weight: bold;
    }

    &:hover {
        color: lighten($color_default, 5%);
    }
}
</style>
