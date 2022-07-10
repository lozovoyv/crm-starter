import {File} from "../Classes/File";
import {ImageFile} from "../Classes/ImageFile";

/**
 * Get type of variable.
 * This method will determine if the attribute is a number, string, array or file and
 * return the proper string value accordingly laravel validator.
 *
 * @param value
 * @return String|Null
 */
function typeOf(value: unknown): string | null {

    if (typeof value === 'number') return 'numeric';

    if (typeof value === 'string') return 'string';

    if(value instanceof File) return 'file';
    if(value instanceof ImageFile) return 'file';

    if (typeof value === 'object') return 'array';

    return null;
}

export default typeOf;
