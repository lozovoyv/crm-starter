export type DropDownOption = number | string | { [index: string | number]: any };
export type DropDownOptions = { [index: number | string]: DropDownOption } | Array<DropDownOption>;
export type DropDownDisplayOption = { key: number | string, caption: string, hint?: string, enabled: boolean, payload: any };
export type DropDownDisplayOptions = Array<DropDownDisplayOption>;
export type DropDownValueType = string | number | boolean | null | Array<string | number>;

export interface InputCommonProps {
    name?: string,
    hasErrors?: boolean,
}

export interface InputStringCustomizableProps {
    disabled?: boolean,
    clearable?: boolean,
    type?: string,
    autocomplete?: string,
    placeholder?: string | null,
}
