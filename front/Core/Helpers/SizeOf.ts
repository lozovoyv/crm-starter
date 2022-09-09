import {File} from "@/Core/Classes/File";
import {ImageFile} from "@/Core/Classes/ImageFile";

/**
 * Get the size of an attribute.
 * This method will determine if the attribute is a number, string, or file and
 * return the proper size accordingly. If it is a number, then number itself
 * is the size. If it is a file, we take kilobytes, and for a string the
 * entire length of the string will be considered the attribute size.
 *
 * @param value
 *
 * @return Number
 */
function sizeOf(value: unknown): number {
    if (value === null) return 0;
    if (typeof value === 'number') return value;
    if (typeof value === 'string') return value.length;
    if(value instanceof File) return value.size;
    if(value instanceof ImageFile) return value.size;
    if (typeof value === 'object') return Object.keys(value).length;

    return 0;
}

export default sizeOf;
