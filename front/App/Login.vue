<template>
    <div class="login">
        <LoadingProgress :loading="form.is_loading">
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
                <FormString :form="form" name="username" :clearable="true" :vertical="true" autocomplete="username" @keyup.enter="enter" ref="login"/>
                <FormPassword :form="form" name="password" :clearable="true" :vertical="true" autocomplete="current-password" @keyup.enter="enter" ref="password"/>
<!--                                <FormCheckBox :form="form" :name="'remember'"/>-->
            </div>
            <div class="login__actions">
                <span class="link" @click="forgot">Забыл пароль</span>
                <GuiButton type="default" @clicked="login">Войти</GuiButton>
            </div>
        </LoadingProgress>
    </div>
</template>

<script>
import {Form} from "@/Core/Form";
import dialog from "@/Core/Dialog/Dialog";
import Logo from "@/App/Logo";
import GuiButton from "@/Components/GUI/GuiButton";
import LoadingProgress from "../../front_temp/Components/LoadingProgress";
import FormString from "@/Components/Form/FormString";
import FormPassword from "@/Components/Form/FormPassword";
// import FormCheckBox from "@/Components/Form/FormCheckBox";

export default {

    components: {FormPassword, FormString, LoadingProgress, GuiButton, Logo},

    props: {
        // message: {type: [String,null], default:null},
    },

    data: () => ({
        form: new Form(null, null, '/login'),
    }),

    created() {
        this.form.set('username', null, 'required', 'Имя пользователя', true);
        this.form.set('password', null, 'required', 'Пароль', true);
        this.form.set('remember', null, null, 'Запомнить меня', true);
        this.form.load();
    },

    mounted() {
        // if (typeof message !== "undefined" && message !== null) {
        //     this.form.errors['login'] = [message];
        //     this.form.valid = {login: false, password: true};
        // }
        this.$refs.login.focus();
        // this.form.is_loading = true;
    },

    methods: {
        enter() {
            if (this.form.values['username'] === null) {
                this.$refs.login.focus();
            } else if (this.form.values['password'] === null) {
                this.$refs.password.focus();
            } else {
                this.login();
            }
        },

        login() {
            if (this.form.is_saving || !this.form.validate()) {
                return;
            }

            this.form.save()
                .then(response => {
                    console.log(response)
                })
                .catch(error => {
                    if (error.code === 301) {
                        window.location.href = error.response.data.to;
                    }
                });
        },
        forgot() {
            dialog.show('Восстановление пароля', 'Чтобы восстановить доступ в систему обратитесь к администратору', [dialog.button('ok', 'OK', 'default')]);
        },
    }
}
</script>

<style lang="scss">
@import "../variables";

body, html {
    width: 100%;
    height: 100%;
    background-color: #f9fafd;
    min-width: 300px;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.login {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: calc(100% - 4px);
    margin: 0 2px;
    max-width: 300px;
    box-shadow: $shadow_1;
    background-color: #fff;
    border-radius: 3px;
    box-sizing: border-box;
    padding: 20px;

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
    font-size: 14px;

    &__bold {
        font-weight: bold;
    }

    &:hover {
        color: lighten($color-default, 5%);
    }
}
</style>
