function clone(original: any): any {
    // Handle not object types
    if (original === null || typeof original !== 'object') return original;

    // Handle Date
    if (original instanceof Date) {
        let copy: Date = new Date();
        copy.setTime(original.getTime());
        return copy;
    }

    // Handle Array
    if (original instanceof Array) {
        let copy: any[] = [];
        for (let i: number = 0, len = original.length; i < len; i++) {
            copy[i] = clone(original[i]);
        }
        return copy;
    }

    // Handle Object
    if (original instanceof Object) {
        let copy: { [index: string]: any } = {};
        let attr: string;
        for (attr in original) {
            if (original.hasOwnProperty(attr)) {
                copy[attr] = clone(original[attr]);
            }
        }
        return copy;
    }
}

export default clone;
