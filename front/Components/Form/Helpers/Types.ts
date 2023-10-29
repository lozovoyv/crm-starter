/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

export interface FormFieldBaseProps {
    hasErrors?: boolean,
    errors?: string[],
    required?: boolean,
}

export interface FormFieldProps {
    title?: string | null,
    withoutTitle?: boolean,
    hideTitle?: boolean,
    vertical?: boolean,
}
