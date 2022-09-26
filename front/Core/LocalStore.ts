class LocalStore {

    static get(key: string): string | null {
        let value: string | null = localStorage.getItem(key);
        return value === '' ? null : value;
    }

    static set(key: string, value: string | null): void {
        localStorage.setItem(key, value ? value : '');
    }

    static remove(key: string): void {
        localStorage.removeItem(key);
    }

    static has(key: string): boolean {
        let test: string | null | undefined = localStorage[key];
        return typeof test !== 'undefined';
    }
}

export {LocalStore};
