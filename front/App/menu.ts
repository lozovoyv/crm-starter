import {Menu} from "../Core/Types/Menu";

const menu: Menu = [
    {
        title: 'Спортсмены', items: [
            {title: 'Спортсмены', route: {name: 'test1'}},
            {title: 'Массовая загрузка разрядов', route: {name: 'test2'}},
            {title: 'Представления на разряды', route: {name: 'test3'}},
            {title: 'Контроль комплектности документов', route: {name: 'test4'}},
            {title: 'Контроль индивидуальных планов', route: {name: 'test5'}},
        ]
    },
    {
        title: 'Группы', items: [
            {title: 'Группы', route: {name: 'test6'}},
            {title: 'Периоды обучения', route: {name: 'test7'}},
            {title: 'Контроль ведения журналов', route: {name: 'test8'}},
            {title: 'Контроль ошибок в журналах', route: {name: 'test9'}},
        ]
    },
    {title: 'Спортобъекты', route: {name: 'test10'}},
    {
        title: 'Мероприятия', items: [
            {title: 'Календарь мероприятий', route: {name: 'test11'}},
            {title: 'Соревнования', route: {name: 'test12'}},
            {title: 'Тренировочные мероприятия', route: {name: 'test13'}},
            {title: 'Рейтинг результатов соревнований', route: {name: 'test14'}},
            {title: 'Календарные планы', route: {name: 'test15'}},
            {title: 'Статистика соревнований', route: {name: 'test16'}},
        ]
    },
    {
        title: 'Сборные', items: [
            {title: 'Сборные', route: {name: 'test17'}},
        ]
    },
    {title: 'Статистика', route: {name: 'test18'}},
    {
        title: 'Организации', items: [
            {title: 'Карточка организации', route: {name: 'test19'}},
            {title: 'Все организации', route: {name: 'test20'}},
        ]
    },
    {
        title: 'Документы', items: [
            {title: 'Реестр', route: {name: 'test21'}},
            {
                title: 'Финансы', items: [
                    {title: 'Сметы', route: {name: 'test22'}},
                    {title: 'Бюджет Доп. КР', route: {name: 'test23'}},
                ]
            },
            {title: 'Контроль сроков документов', route: {name: 'test24'}},
            {title: 'КПН Стандарты', route: {name: 'test25'}},
            {title: 'КПН Протоколы', route: {name: 'test26'}},
            {title: 'Журнал событий', route: {name: 'test27'}},
            {title: 'Готовые формы', route: {name: 'test28'}},
            {
                title: 'Инструменты', items: [
                    {title: 'Склеивание документов', route: {name: 'test29'}},
                    {title: 'Массовая загрузка документов', route: {name: 'test30'}},
                    {title: 'Копирование расписания', route: {name: 'test31'}},
                    {title: 'Инвентарь', route: {name: 'test32'}},
                ]
            },
            {title: 'Процедуры', route: {name: 'test33'}},
        ]
    },
    {
        title: 'Сотрудники', items: [
            {title: 'Сотрудники', route: {name: 'test34'}},
            {title: 'График отпусков', route: {name: 'test35'}},
        ]
    },

];

export default menu;
