import {Menu} from "@/Core/Types/Menu";

const menuSrc: Menu = [
    {
        title: 'Сотрудники', items: [
            {title: 'Сотрудники', route: {name: 'staff'}, permission: ['system.staff', 'system.staff.change']},
            {title: 'Зарегистрировать сотрудника', route: {name: 'staff_create'}, permission: 'system.staff.change'},
        ]
    },
    {
        title: 'Система', items: [
            {title: 'Справочники', route: {name: 'dictionaries'}, permission: 'system.dictionaries'},
            {title: 'Права', route: {name: 'permissions'}, permission: 'system.permissions'},
            {title: 'Учётные записи', route: {name: 'users'}, permission: 'system.users'},
            {title: 'Настройки системы', route: {name: 'settings'}, permission: 'system.settings'},
            {title: 'Журнал операций', route: {name: 'history'}, permission: 'system.history'},
        ]
    },
    {title: 'Test', route: {name: 'test'}},
];

export default menuSrc;
