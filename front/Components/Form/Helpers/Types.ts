/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

export interface FormFieldCommonProps {
    title?: string | null,
    hasErrors?: boolean,
    errors?: string[],
    required?: boolean,
}

export interface FormFieldCustomizableProps {
    withoutTitle?: boolean,
    hideTitle?: boolean,
    vertical?: boolean,
}
