import {Menu} from "@/Core/Types/Menu";

const menuRaw: Menu = [
    {
        title: 'Сотрудники', items: [
            {title: 'Сотрудники', route: {name: 'staff'}, permission: ['system.staff', 'system.staff.change']},
            {title: 'Добавить сотрудника', route: {name: 'staff_create'}, permission: 'system.staff.change'},
        ]
    },
    {
        title: 'Система', items: [
            {title: 'Роли и права', route: {name: 'roles'}, permission: 'system.roles'},
            {title: 'Учётные записи', route: {name: 'users'}, permission: 'system.users'},
            {title: 'Настройки системы', route: {name: 'settings'}, permission: 'system.settings'},
            {title: 'Журнал операций', route: {name: 'history'}, permission: 'system.history'},
        ]
    },
    {title: 'Test', route: {name: 'test'}},
];

export default menuRaw;
