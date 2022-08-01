import {RouteLocationRaw} from "vue-router";

export type MenuItem = {
    title: string | null,
    items?: Menu,
    route?: RouteLocationRaw,
    permission?: string | string[],
}

export type Menu = Array<MenuItem>
