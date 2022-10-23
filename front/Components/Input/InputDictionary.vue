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
    />
</template>

<script setup lang="ts">
import {DropDownOptions, DropDownValueType} from "@/Components/Input/Helpers/InputTypes";
import InputDropDown from "@/Components/Input/InputDropDown.vue";
import {computed, ref} from "vue";
import {useStore} from "vuex";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: DropDownValueType,
    original?: DropDownValueType,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,

    // dropdown props
    placeholder?: string,
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

    multi?: boolean,

    search?: boolean,
}>();

const store = useStore();

const emit = defineEmits<{
    (e: 'update:modelValue', value: DropDownValueType): void,
    (e: 'change', value: DropDownValueType, name: string | undefined): void,
}>()

const input = ref<InstanceType<typeof InputDropDown> | null>(null);

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

const options = computed((): DropDownOptions => {
    if (!loaded.value) {
        return [];
    }
    return store.getters['dictionaries/dictionary'](props.dictionary);
});

function change(value: DropDownValueType, name: string | undefined): void {
    emit('change', value, name);
}

const loaded = ref<boolean>(false);

function refresh(): void {
    if (loaded.value && props.freeze === true) {
        return;
    }
    store.dispatch('dictionaries/refresh', props.dictionary)
        .then(() => {
            loaded.value = true;
        });
}

refresh();

defineExpose({

});
</script>
