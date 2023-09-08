export type DropDownOption = number | string | { [index: string | number]: any };
export type DropDownOptions = { [index: number | string]: DropDownOption } | Array<DropDownOption>;
export type DropDownDisplayOption = { key: number | string, caption: string, hint?: string, enabled: boolean, payload: any };
export type DropDownDisplayOptions = Array<DropDownDisplayOption>;
export type DropDownValueType = string | number | boolean | null | Array<string | number>;

/**
 * Common props for all inputs
 */
export interface InputBaseProps {
    name?: string,
    hasErrors?: boolean,
}

interface InputCommonProps {
    disabled?: boolean,
    clearable?: boolean,
}

export interface InputStringProps extends InputCommonProps {
    type?: string,
    autocomplete?: string,
    placeholder?: string | null,
}

export interface InputPasswordProps extends InputCommonProps {
    autocomplete?: string,
    placeholder?: string | null,
}

export interface InputPhoneProps extends InputCommonProps {
    mask?: string,
    autocomplete?: string,
    placeholder?: string | null,
}
