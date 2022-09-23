import {Menu} from "@/Core/Types/Menu";

const menu: Menu = [
    {
        title: 'Настройки', items: [
            {title: 'Пользователи', route: {name: 'users'}},
            {title: 'Роли и права', route: {name: 'roles'}},
            {title: 'Настройки системы', route: {name: 'settings'}},
        ]
    },
];

export default menu;
