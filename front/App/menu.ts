import {Menu} from "@/Core/Types/Menu";

const menuSrc: Menu = [
    {
        title: 'Сотрудники', items: [
            {title: 'Сотрудники', route: {name: 'staff'}, permission: ['system__staff', 'system__staff_change']},
            {title: 'Зарегистрировать сотрудника', route: {name: 'staff_create'}, permission: 'system__staff_change'},
        ]
    },
    {
        title: 'Система', items: [
            {title: 'Справочники', route: {name: 'dictionaries'}, permission: 'system__dictionaries'},
            {title: 'Права', route: {name: 'permissions'}, permission: 'system__permissions'},
            {title: 'Учётные записи', route: {name: 'users'}, permission: 'system__users'},
            {title: 'Настройки системы', route: {name: 'settings'}, permission: 'system__settings'},
            {title: 'Журнал операций', route: {name: 'history'}, permission: 'system__history'},
        ]
    },
    {title: 'Test', route: {name: 'test'}},
];

export default menuSrc;
