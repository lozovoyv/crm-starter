<template>
    <LayoutPage title="Подтверждение адреса электронной почты">
        <LoadingProgress :loading="is_processing">
            <div class="email-confirmation-message" :class="{'email-confirmation-message-error': is_error}">
                {{ message }}
            </div>
        </LoadingProgress>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import {ref} from "vue";
import LoadingProgress from "@/Components/LoadingProgress.vue";
import {http} from "@/Core/Http/Http";

const urlParams = new URLSearchParams(window.location.search);
let token: string | null = null;

if (urlParams.has('token')) {
    token = urlParams.get('token');
}

const message = ref<string>((token === null) ? 'Не задан ключ для смены адреса' : 'Подтверждаем...');
const is_processing = ref<boolean>(token !== null);
const is_error = ref<boolean>(token === null);

if(token !== null) {
    http.post('/api/auth/confirm/email', {token: token})
        .then(response => {
            is_error.value = false;
            console.log(response);
            message.value = response.data.message;
        })
        .catch(error => {
            is_error.value = true;
            console.log(error);
            message.value = error.data.message;
        })
        .finally(() => {
            is_processing.value = false;
        });
}
</script>

<style lang="scss">
@import "@/variables.scss";

.email-confirmation-message {
    @include font(20px, 400);
    color: $color_text_black;
    text-align: center;
    padding: 40px 0;

    &-error {
        color: $color_error;
    }
}
</style>
