<template>
    <div>
        <GuiGroupBox width="50%" min-width="400px">
            <GuiValue dots title="Фамилия:">{{ userData.lastname }}</GuiValue>
            <GuiValue dots title="Имя:">{{ userData.firstname }}</GuiValue>
            <GuiValue dots title="Отчество:">{{ userData.patronymic }}</GuiValue>
            <GuiValue dots title="Отображаемое имя:">{{ userData.display_name }}</GuiValue>
        </GuiGroupBox>
        <GuiGroupBox width="50%" min-width="400px">
            <GuiValue dots title="Статус учётной записи:"><GuiAccessIndicator :locked="!userData.is_active"/>{{ userData.status }}</GuiValue>
            <GuiValue dots title="Логин:">{{ userData.username }}</GuiValue>
            <GuiValue dots title="Email:">{{ userData.email }}</GuiValue>
            <GuiValue dots title="Телефон:">{{ userData.phone }}</GuiValue>
        </GuiGroupBox>
    </div>
</template>

<script setup lang="ts">
import {UserInfo} from "@/App/types";
import GuiGroupBox from "@/Components/GUI/GuiGroupBox.vue";
import GuiValue from "@/Components/GUI/GuiValue.vue";
import GuiAccessIndicator from "@/Components/GUI/GuiAccessIndicator.vue";

const props = defineProps<{
    userId: number,
    userData: UserInfo,
}>();

</script>
