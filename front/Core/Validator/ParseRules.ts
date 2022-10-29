export type FieldRules = { [index: string]: null | string[] }

/**
 * Parse validation rules from string to rule object.
 *
 * @param rules
 * @return Rule
 */

export function ParseFieldRules(rules: string | null): FieldRules {
    if (!rules) return {};

    let _rule_set = rules.split('|');
    if (_rule_set.length === 0) return {};

    let _rules: FieldRules = {};
    _rule_set.map((rule: string) => {
        let attributes = rule.split(':');
        _rules[attributes[0]] = typeof attributes[1] !== "undefined" ? attributes[1].split(',') : null;
    });

    return _rules;
}
