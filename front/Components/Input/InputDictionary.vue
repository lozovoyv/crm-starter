<template>
    <InputDropDown
        :name="name"
        v-model="proxyValue"
        :original="original"
        :disabled="disabled"
        :has-errors="hasErrors"
        :clearable="clearable"
        :placeholder="placeholder"
        :has-null="hasNull"
        :null-caption="nullCaption"
        :empty-caption="emptyCaption"
        :options="options"
        :show-disabled-options="showDisabledOptions"
        :id-key="idKey"
        :caption-key="captionKey"
        :filter-key="filterKey"
        :hint-key="hintKey"
        :multi="multi"
        :search="search"
        @change="change"
        @dropped="refresh"
        ref="input"
    >
        <template #additional v-if="isEditable">
            <GuiActionsMenu class="input-dictionary-additional">
                <GuiLink name="Добавить запись" @click="add"/>
                <GuiLink v-if="modelValue && !multi" name="Редактировать запись" @click="edit"/>
                <GuiLink v-if="modelValue && !multi" name="Удалить запись" @click="remove"/>
                <hr/>
                <GuiLink :route="{name: 'dictionaries', query: {dictionary: dictionary}}" :new-tab="true" name="Редактировать справочник"/>
            </GuiActionsMenu>
            <DictionaryEditorForm ref="form" :dictionary="dictionary"/>
        </template>
    </InputDropDown>
</template>

<script setup lang="ts">
import {DropDownOptions, DropDownValueType} from "@/Components/Input/Helpers/Types";
import InputDropDown from "@/Components/Input/InputDropDown.vue";
import {computed, ref} from "vue";
import {useStore} from "vuex";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import DictionaryEditorForm from "@/Components/Dictionary/DictionaryEditorForm.vue";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: DropDownValueType,
    original?: DropDownValueType,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,

    // dropdown props
    placeholder?: string | null,
    hasNull?: boolean,
    nullCaption?: string,
    emptyCaption?: string,

    dictionary: string,
    freeze?: boolean,
    showDisabledOptions?: boolean,
    idKey?: string,
    captionKey?: string,
    filterKey?: string,
    hintKey?: string,
    editable?: boolean,

    multi?: boolean,

    search?: boolean,
}>();

const store = useStore();

const emit = defineEmits<{
    (e: 'update:modelValue', value: DropDownValueType): void,
    (e: 'change', value: DropDownValueType, name: string | undefined, payload: any): void,
}>()

const input = ref<InstanceType<typeof InputDropDown> | undefined>(undefined);
const form = ref<InstanceType<typeof DictionaryEditorForm> | undefined>(undefined);

const proxyValue = computed({
    get: (): DropDownValueType => {
        if (!ready.value) {
            return [];
        }
        return props.modelValue === undefined ? null : props.modelValue;
    },
    set: (value: DropDownValueType) => {
        emit('update:modelValue', value);
    }
});

const ready = computed((): boolean => {
    return store.getters['dictionaries/ready'](props.dictionary);
});

const isEditable = computed((): boolean => {
    return props.editable && store.getters['dictionaries/editable'](props.dictionary);
});

const options = computed((): DropDownOptions => {
    if (!loaded.value) {
        return [];
    }
    return store.getters['dictionaries/dictionary'](props.dictionary);
});

function change(value: DropDownValueType, name: string | undefined, payload: any): void {
    emit('change', value, name, payload);
}

const loaded = ref<boolean>(false);

function refresh(force: boolean = false): void {
    if (force) {
        store.dispatch('dictionaries/refreshForce', props.dictionary)
            .then(() => {
                loaded.value = true;
            });
    } else if (!loaded.value || props.freeze === false) {
        store.dispatch('dictionaries/refresh', props.dictionary)
            .then(() => {
                loaded.value = true;
            });
    }
}

refresh();

function add() {
    if (form.value) {
        form.value.show(null)
            ?.then((result: any) => {
                if (result['payload'] && result['payload']['id']) {
                    if (!props.multi) {
                        proxyValue.value = result['payload']['id'];
                    } else {
                        if (proxyValue.value instanceof Array) {
                            proxyValue.value.push(result['payload']['id']);
                        } else {
                            proxyValue.value = [result['payload']['id']];
                        }
                    }
                }
                refresh(true);
            });
    }
}

function edit() {
    const id: DropDownValueType = (!props.multi && props.modelValue) ? props.modelValue : null;
    if (id && form.value) {
        form.value.show(null)
            ?.then(() => {
                refresh(true);
            });
    }
}

function remove() {
    const id: DropDownValueType = (!props.multi && props.modelValue) ? props.modelValue : null;
    if (id && typeof id !== "object") {
        const item = store.getters['dictionaries/item'](props.dictionary, id);

        if (item === undefined) { //} || item.hash === undefined) {
            return;
        }

        processEntry({
            title: 'Удаление',
            question: `Удалить запись "${item.name}"?`,
            button: dialog.button('yes', 'Удалить', 'error'),
            method: 'delete',
            url: `/api/dictionaries/${props.dictionary}/${id}`,
            options: {hash: item.hash},
        }).then(() => {
            proxyValue.value = null;
            refresh(true);
        });
    }
}

defineExpose({});
</script>

<style lang="scss">
.input-dictionary-additional > .actions-menu__button {
    border: none !important;
    border-radius: 0;
    padding: 0 8px;
}
</style>
