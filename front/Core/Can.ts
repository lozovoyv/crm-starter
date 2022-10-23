import store from "@/store";

function can(permission: string | Array<string> | null): boolean {
    if (!store.getters['user/loaded']) {
        return false;
    }

    let able: boolean = false;

    if (permission === null) {
        able = true
    }
    if (typeof permission === "string") {
        able = store.getters['user/can'](permission);
    }
    if (Array.isArray(permission)) {
        able = permission.some(checking => store.getters['user/can'](checking));
    }
    return able;
}

export {can};
