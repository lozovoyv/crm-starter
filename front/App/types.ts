export type User = {
    id: number,
    is_active: boolean,
    lastname: string,
    firstname: string,
    patronymic: string,
    username: string,
    display_name: string,
    email: string,
    phone: string,
    created_at: string,
    updated_at: string,
    hash: string | null,
};

export type UserInfo = {
    id: number,
    is_active: boolean,
    status: string,
    name: string,
    lastname: string,
    firstname: string,
    patronymic: string,
    username: string,
    display_name: string,
    email: string,
    phone: string,
    created_at: string,
    updated_at: string,
    hash: string | null,
};

export type Position = {
    id: number,
    active: boolean,
    created_at: string,
    updated_at: string,
    hash: string,
    user: User,
};

export type StaffInfo = {
    id: number,
    active: boolean,
    status: string,
    type: string,
    status_id: number,
    name: string,
    user: User,
};
