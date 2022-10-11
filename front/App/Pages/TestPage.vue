<template>
    <LayoutPage title="Test page">
        <div style="width: 300px">
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o1 }}</pre>
                <InputDropDown :options="o1" v-model="r1" placeholder="Сделайте выбор" :has-null="true" null-caption="Без выбора"/>
                <pre>value: {{ r1 }} ({{ r1 === null ? 'null' : typeof r1 }})</pre>
            </div>
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o2 }}</pre>
                <InputDropDown :options="o2" v-model="r2"/>
                <pre>value: {{ r2 }} ({{ r2 === null ? 'null' : typeof r2 }})</pre>
            </div>
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o3 }}</pre>
                <InputDropDown :options="o3" v-model="r3"/>
                <pre>value: {{ r3 }} ({{ r3 === null ? 'null' : typeof r3 }})</pre>
            </div>
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o4 }}</pre>
                <InputDropDown :options="o4" v-model="r4"
                               :search="true"
                               placeholder="Сделайте выбор"
                               :has-null="true"
                               null-caption="Без выбора"
                               :multi="true" :disabled="disabled"  :clearable="true"
                />
                <InputCheckbox v-model="disabled"/>
                <pre>value: {{ r4 }} ({{ r4 === null ? 'null' : typeof r4 }})</pre>
            </div>
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o5 }}</pre>
                <InputDropDown :options="o5" v-model="r5" :disabled="true"/>
                <pre>value: {{ r5 }} ({{ r5 === null ? 'null' : typeof r5 }})</pre>
            </div>
            <div style="border: 1px solid #b700ff; margin-bottom: 10px; padding: 10px; border-radius: 2px">
                <pre style="border: 1px solid #a6a6a6; font-size: 10px; margin-bottom: 10px; border-radius: 2px">{{ o6 }}</pre>
                <InputDropDown :options="o6" v-model="r6"/>
                <pre>value: {{ r6 }} ({{ r6 === null ? 'null' : typeof r6 }})</pre>
            </div>
        </div>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from '@/Components/Layout/LayoutPage.vue';
import InputDropDown from "@/Components/Input/InputDropDown.vue";
import {ref} from "vue";
import InputCheckbox from "@/Components/Input/InputCheckbox.vue";

const o1 = [1, 3, 5];
const o2 = [
    {id: 1, name: 'test 1 test 1 test 1 test 1 test 1 test 1 ', hint: 'If you are not averse to using JavaScript and Regex, you can use the below solution.'},
    {id: 2, name: 'test 2 test 2 test 2 test 2 test 2 test 2 test 2 test 2 ', hint: 'If you are not averse to using JavaScript and Regex, you can use the below solution.'},
    {id: 3, name: 'test 3', hint: 'If you are not averse to using JavaScript and Regex, you can use the below solution.'},
];
const o3 = {3: 'test 1', 5: 'test 2', 7: 'test 3'};
const o4 = {
    test_1: 'test 1',
    test_2: 'test 2',
    test_3: 'test 3',
    test_4: 'test 4',
    test_5: 'test 5',
    test_6: 'test 6',
    test_7: 'test 7',
    test_8: 'test 8',
    test_9: 'test 9',
    test_10: 'test 10',
    test_11: 'test 11',
};
const o5 = [{id: 1, name: 'test 1'}, {id: 2, name: 'test 2'}, {id: 3, name: 'test 3'}];
const o6 = [{id: 1, name: 'test 1', enabled: true}, {id: 2, name: 'test 2', enabled: true}, {id: 3, name: 'test 3', enabled: false}];

const r1 = ref(null);
const r2 = ref(2);
const r3 = ref(null);
const r4 = ref(['test_2', 'test_4']);
const r5 = ref(2);
const r6 = ref(3);

const disabled = ref<boolean>(true);
</script>
