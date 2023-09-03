<template>
    <LayoutPage title="Test page">

        <InputDictionary v-model="variable" dictionary="test" :clearable="true" :editable="true" :search="true"/>

        <div style="margin-top: 30px">
            <GuiButton type="unset">Отмена</GuiButton>
            <GuiButton type="info">Отмена</GuiButton>
            <GuiButton type="success">Отмена</GuiButton>
            <GuiButton type="error">Отмена</GuiButton>
            <GuiButton type="default">Отмена</GuiButton>
        </div>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from '@/Components/Layout/LayoutPage.vue';
import InputDictionary from "@/Components/Input/InputDictionary.vue";
import {ref} from "vue";
import GuiButton from "@/Components/GUI/GuiButton.vue";

const variable = ref(null);


</script>
