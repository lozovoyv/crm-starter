import empty from "../Helpers/Empty";
import typeOf from "../Helpers/TypeOf";
import {FieldRules} from "./ParseRules";
import Localization from "./Localization";

/**
 * Replace 'search' with 'replace' in message.
 *
 * @param message
 * @param search
 * @param replace
 * @return {*}
 */
function replacePart(message: string, search: string, replace: string) {
    return message.replaceAll(search, replace);
}

/**
 * Association for rules and message formatter function
 */
const messageFormatters = [
    { // :attribute
        rules: [
            'accepted', 'active_url', 'alpha', 'alpha_dash', 'alpha_num', 'array',
            'boolean',
            'confirmed',
            'date', 'declined', 'dimensions', 'distinct',
            'email', 'enum', 'exists',
            'file', 'filled',
            'image', 'in', 'integer', 'ip', 'ipv4', 'ipv6',
            'json',
            'mac_address',
            'not_in', 'not_regex', 'numeric',
            'present', 'prohibited',
            'regex', 'required',
            'string',
            'timezone',
            'unique', 'uploaded', 'url', 'uuid',
        ],
        formatter: (message: string) => {
            return message;
        }
    },
    { // :attribute :min :max
        rules: [
            'between',
            'digits_between',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            if (empty(field_rules[failed_rule])) return message;
            let attributes = String(field_rules[failed_rule]).split(',');
            message = replacePart(message, ':min', attributes[0]);
            message = replacePart(message, ':max', attributes[1]);
            return message;
        },
    },
    { // :attribute :max
        rules: [
            'max',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            return replacePart(message, ':max', String(field_rules[failed_rule]));
        }
    },
    { // :attribute :min
        rules: [
            'min',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            return replacePart(message, ':min', String(field_rules[failed_rule]));
        }
    },
    { // :attribute :date
        rules: [
            'after', 'after_or_equal', 'before', 'before_or_equal', 'date_equals',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }, values: { [index: string]: any }) => {
            let attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            if (!!values[attributes]) {
                return replacePart(message, ':date', '"' + [values[attributes]] + '"');
            }
            // todo probably format the date
            return replacePart(message, ':date', attributes);
        }
    },
    { // :attribute :format
        rules: [
            'date_format',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            return replacePart(message, ':format', String(field_rules[failed_rule]));
        }
    },
    { // :attribute :size
        rules: [
            'size',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            return replacePart(message, ':size', String(field_rules[failed_rule]));
        }
    },
    { // :attribute :digits
        rules: [
            'digits',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules) => {
            return replacePart(message, ':digits', String(field_rules[failed_rule]));
        }
    },
    { // :attribute :other
        rules: [
            'different', 'in_array', 'prohibits', 'same',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }) => {
            let attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            let other = titles[attributes];
            return replacePart(message, ':other', other);
        }
    },
    { // :attribute :value
        rules: [
            'gt', 'gte', 'lt', 'lte', 'multiple_of',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }, values: { [index: string]: any }) => {
            let attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            if (!!values[attributes]) {
                return replacePart(message, ':date', '"' + values[attributes] + '"');
            }
            return replacePart(message, ':date', attributes);
        }
    },
    { // :attribute :values
        rules: [
            'doesnt_start_with',
            'ends_with',
            'mimes', 'mimetypes',
            'required_array_keys', 'required_with', 'required_with_all', 'required_without', 'required_without_all',
            'starts_with',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }) => {
            let attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            let others: string[] = [];
            attributes.split(',').map((name) => {
                others.push('"' + titles[name] + '"');
            });

            return replacePart(message, ':values', others.join(', '));
        }
    },
    { // :attribute :other :value
        rules: [
            'accepted_if',
            'declined_if',
            'prohibited_if',
            'required_if',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }, values: { [index: string]: any }) => {
            const attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            let heap = attributes.split(',');
            let other = heap.splice(0, 1)[0];
            const val = values[other];
            other = titles[other];
            message = replacePart(message, ':other', other);
            message = replacePart(message, ':value', val);

            return message;
        }
    },
    { // :attribute :other :values
        rules: [
            'prohibited_unless',
            'required_unless',
        ],
        formatter: (message: string, name: string, failed_rule: string, field_rules: FieldRules, titles: { [index: string]: string }) => {
            let attributes = field_rules[failed_rule];
            if (attributes === null) {
                return message;
            }
            let heap = attributes.split(',');
            let other = titles[heap[0]];
            message = replacePart(message, ':other', other);
            message = replacePart(message, ':values', heap.splice(0, 1).join(', '));

            return message;
        }
    },
];

/**
 * Make whole validation error message for given rule and field.
 */
function formatErrorMessage(name: string, value: any, failed_rule: string, validation_rule: FieldRules, titles: { [index: string]: string }, values: { [index: string]: any }) {

    // @ts-ignore
    let message: string | { [index: string]: string } = Localization[failed_rule];

    // todo handle password validation message

    if (typeof message === 'object') {
        let type = typeOf(value);
        if (type === null || !message[type]) return null;
        message = message[type];
    }

    if (!message) return null;

    message = message.replaceAll(':attribute', '"' + String(titles[name]).toLowerCase() + '"');

    messageFormatters.some(messageFormatter => {
        if (messageFormatter.rules.indexOf(failed_rule) === -1) return false;
        message = messageFormatter.formatter(String(message), name, failed_rule, validation_rule, titles, values);
        return true;
    });

    return message;
}

export default formatErrorMessage;
