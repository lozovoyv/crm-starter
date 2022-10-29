import empty from "@/Core/Helpers/Empty";
import sizeOf from "@/Core/Helpers/SizeOf";

const Rules = function () {
    return {

        /**
         * The field under validation must have a minimum value. Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.
         */
        min(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
            if(attributes === null || attributes[0] === undefined) {
                console.error(`min rule for ${field_name} requires 1 parameters`);
                return false;
            }
            return sizeOf(value) >= Number(attributes[0]);
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
            if(attributes === null || attributes[0] === undefined || attributes[1] === undefined) {
                console.error(`required_if rule for ${field_name} requires 2 parameters`);
                return false;
            }
            return all_fields[attributes[0]] && all_fields[attributes[0]] == attributes[1] && !empty(value);
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
            if(attributes === null) {
                console.error(`required_without rule for ${field_name} requires at least 1 parameters`);
                return false;
            }
            return attributes.filter(key => empty(all_fields[key])).length === 0 || !empty(value);
        },

        /**
         * The field under validation must be present and not empty only when all the other specified fields are empty or not present.
         */
        required_without_all(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
            if(attributes === null) {
                console.error(`required_without_all rule for ${field_name} requires at least 1 parameters`);
                return false;
            }
            return attributes.filter(key => !empty(all_fields[key])).length !== 0 || !empty(value);
        },
        // required_array_keys(value: any, attributes: string[] | null, field_name: string, all_fields: { [index: string]: any }): boolean {
        //     return !empty(value);
        // },
    };
};


export default Rules;
