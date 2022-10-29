import {Menu} from "@/Core/Types/Menu";

const menuRaw: Menu = [
    {
        title: 'Система', items: [
            {title: 'Сотрудники', route: {name: 'staff'}, permission: ['system.staff', 'system.staff.change']},
            {title: 'Добавить сотрудника', route: {name: 'staff_create'}, permission: 'system.staff.change'},
            {title: 'Учётные записи', route: {name: 'users'}, permission: 'system.users'},
            {title: 'Роли и права', route: {name: 'roles'}, permission: 'system.roles'},
            {title: 'Настройки', route: {name: 'settings'}, permission: 'system.settings'},
            {title: 'Журнал операций', route: {name: 'history'}, permission: 'system.history'},
        ]
    },
    {title: 'Test', route: {name: 'test'}},
];

export default menuRaw;
