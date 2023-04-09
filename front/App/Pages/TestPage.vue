<template>
    <LayoutPage title="Test page">
        <InputDictionary v-model="variable" dictionary="test" :clearable="true" :editable="true" :search="true"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from '@/Components/Layout/LayoutPage.vue';
import InputDictionary from "@/Components/Input/InputDictionary.vue";
import {ref} from "vue";

const variable = ref(null);
</script>
