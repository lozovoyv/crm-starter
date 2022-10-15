import {Form} from "@/Core/Form";

export function getFromForm(form: Form, scope: 'titles' | 'values' | 'originals' | 'valid' | 'errors' | 'rules', name: string, defaults: any = null): any {
    if (!form.is_loaded) {
        return defaults;
    }
    return typeof form[scope][name] !== "undefined" ? form[scope][name] : defaults;
}

export function getTitle(form: Form, name: string): string | null {
    return getFromForm(form, 'titles', name, '...');
}

export function getValue(form: Form, name: string, defaults: any | null = null): any | null {
    return getFromForm(form, 'values', name, defaults);
}

export function getOriginal(form: Form, name: string, defaults: any | null = null): any | null {
    return getFromForm(form, 'originals', name, defaults);
}

export function getErrors(form: Form, name: string): any | null {
    return getFromForm(form, 'errors', name, []);
}

export function isValid(form: Form, name: string): boolean {
    return getFromForm(form, 'valid', name, true);
}

export function isRequired(form: Form, name: string): boolean {
    const rules = getFromForm(form, 'rules', name, null);
    if (rules === null || typeof rules !== "object") {
        return false;
    }
    const keys = Object.keys(rules);
    return keys.length === 0 ? false : keys.some(rule => rule === 'required');
}
