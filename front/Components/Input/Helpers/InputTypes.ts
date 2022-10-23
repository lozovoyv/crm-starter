export type DropDownOption = number | string | { [index: string | number]: any };
export type DropDownOptions = { [index: number | string]: DropDownOption } | Array<DropDownOption>;
export type DropDownDisplayOption = { key: number | string, caption: string, hint?: string, enabled: boolean };
export type DropDownDisplayOptions = Array<DropDownDisplayOption>;
export type DropDownValueType = string | number | boolean | null | Array<string | number>;
