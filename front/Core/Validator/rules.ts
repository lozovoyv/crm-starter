import empty from "@/Core/Helpers/Empty";
import sizeOf from "@/Core/Helpers/SizeOf";
import typeOf from "@/Core/Helpers/TypeOf";

/**
 * NOTICE:
 * All non-implicit rules must return true on empty values.
 */

export const excludeRules = [
    'exclude',
    'exclude_if',
    'exclude_unless',
    'exclude_with',
    'exclude_without'
];

export const implicitRules = [
    'accepted',
    'accepted_if',
    'declined',
    'declined_if',
    'filled',
    'missing',
    'missing_if',
    'missing_unless',
    'missing_with',
    'missing_with_all',
    'present',
    'required',
    'required_if',
    'required_if_accepted',
    'required_unless',
    'required_with',
    'required_with_all',
    'required_without',
    'required_without_all',
];

export const rules = {

    /**
     * The field under validation must have a minimum value. Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.
     */
    min(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        if (attributes === null || attributes[0] === undefined) {
            console.error(`min rule for ${field_name} requires 1 parameters`);
            return false;
        }
        return empty(value) || sizeOf(value) >= Number(attributes[0]);
    },

    /**
     * The field under validation must be present in the input data and not empty. A field is considered "empty" if one of the following conditions are true:
     * The value is null.
     * The value is an empty string.
     * The value is an empty array or empty object.
     */
    required(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        return !empty(value);
    },

    /**
     * The field under validation must be present and not empty if the another field is equal to value.
     */
    required_if(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        if (attributes === null || attributes[0] === undefined || attributes[1] === undefined) {
            console.error(`required_if rule for ${field_name} requires 2 parameters`);
            return false;
        }
        return all_fields[attributes[0]] === undefined || all_fields[attributes[0]] != attributes[1] || !empty(value);
    },
    // required_unless(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
    //     return !empty(value);
    // },
    // required_with(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
    //     return !empty(value);
    // },
    // required_with_all(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
    //     return !empty(value);
    // },

    /**
     * The field under validation must be present and not empty only when any of the other specified fields are empty or not present.
     */
    required_without(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        if (attributes === null) {
            console.error(`required_without rule for ${field_name} requires at least 1 parameters`);
            return false;
        }
        return attributes.filter(key => empty(all_fields[key])).length === 0 || !empty(value);
    },

    /**
     * The field under validation must be present and not empty only when all the other specified fields are empty or not present.
     */
    required_without_all(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        if (attributes === null) {
            console.error(`required_without_all rule for ${field_name} requires at least 1 parameters`);
            return false;
        }
        return attributes.filter(key => !empty(all_fields[key])).length !== 0 || !empty(value);
    },
    // required_array_keys(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
    //     return !empty(value);
    // },

    /**
     * The field under validation must be present and not empty only when any of the other specified fields are empty or not present.
     */
    size(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        if (attributes === null) {
            console.error(`size rule for ${field_name} requires 1 parameters`);
            return false;
        }
        if (empty(value)) {
            return true;
        }
        const size = Number(attributes[0]);
        switch (typeOf(value)) {
            case 'numeric':
                return value === size;
            case 'string':
            case 'array':
                return value.length === size;
        }
        return true;
    },
};
