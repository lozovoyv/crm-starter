import {rules, excludeRules, implicitRules} from "./rules"
import empty from "@/Core/Helpers/Empty";
import {FieldRules} from "./ParseRules";

/**
 * Validate field in context of all fields.
 *
 * @param field_name
 * @param value
 * @param validation_rules
 * @param fields
 *
 * @returns {boolean|*[]}
 */
export function validate(field_name: string, value: any, validation_rules: FieldRules, fields: { [index: string]: any }): string[] {
    if (empty(field_name) || Object.keys(validation_rules).length === 0) {
        return [];
    }

    let rule_to_check : Array<string> = Object.keys(validation_rules);

    // unset bail rule
    let bailIndex: number = rule_to_check.indexOf('bail');
    if (bailIndex !== -1) {
        rule_to_check.splice(bailIndex, 1);
    }
    const is_bail = bailIndex !== -1;

    // unset nullable rule
    let nullableIndex:number = rule_to_check.indexOf('nullable');
    if (nullableIndex !== -1) {
        rule_to_check.splice(nullableIndex, 1);
    }
    let is_nullable: boolean = nullableIndex !== -1;

    // process exclude rules

    // process implicit rules

    let failedRules: string[] = [];

    for (let i = 0; i < rule_to_check.length; i++) {

        let attributes = validation_rules[rule_to_check[i]];
        // @ts-ignore
        if (typeof rules[rule_to_check[i]] === 'function') {
            let ruleCheck = null;
            try {
                if (!is_nullable || !empty(value)) {
                    // @ts-ignore
                    ruleCheck = rules[rule_to_check[i]](value, attributes, field_name, fields);
                }
            } catch (e) {
                console.error('Failed to check rule "' + rule_to_check[i] + '" on "' + field_name + '" field.', e);
            }
            if (ruleCheck === false) {
                failedRules.push(rule_to_check[i]);
                if (is_bail) {
                    return failedRules;
                }
            }
        }
    }

    return failedRules;
}
