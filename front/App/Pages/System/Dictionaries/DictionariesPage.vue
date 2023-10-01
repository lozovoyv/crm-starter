<template>
    <LayoutPage title="Справочники" :breadcrumbs="[{name: 'Справочники'}]">

        <GuiTabs v-model="dictionary" :tabs="tabs" tab-key="dictionary"/>

        <DictionaryEditor :dictionary="dictionary" v-if="dictionary"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import {List} from "@/Core/List";
import DictionaryEditor from "@/Components/Dictionary/DictionaryEditor.vue";
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const dictionary = ref<string | undefined>(undefined);

const tabs = computed((): { [index: string]: string } => {
    let tabs: { [index: string]: string } = {};
    dictionaries.value.list.map(item => {
        tabs[item.name] = item.title;
    });
    return tabs;
});

const dictionaries = ref<List<{ [index: string]: string }>>(new List({
    load_url:apiEndPoint('get','/api/dictionaries'),
    use_pagination: false
}));

dictionaries.value.load();

</script>
