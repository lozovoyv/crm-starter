import {Menu} from "@/Core/Types/Menu";

const menu: Menu = [
    {
        title: 'Система', items: [
            {title: 'Сотрудники', route: {name: 'staff'}},
            {title: 'Роли и права', route: {name: 'roles'}},
            {title: 'Настройки', route: {name: 'settings'}},
            {title: 'Журнал операций', route: {name: 'history'}},
        ]
    },
    {title: 'Test', route: {name: 'test'}},
];

export default menu;
