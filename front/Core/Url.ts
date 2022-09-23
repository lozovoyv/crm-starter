class Url {

    url: URL;

    constructor(url: string | null = null) {
        this.url = new URL(url !== null ? url : window.location.href);
    }

    getQueryParam(key: string): string | null {
        return this.url.searchParams.get(key);
    }

    setQueryParam(key: string, value: string | null): void {
        this.url.searchParams.set(key, value ? value : '');
    }

    removeQueryParam(key: string): void {
        this.url.searchParams.delete(key);
    }

    push(): void {
        window.history.pushState({}, '', this.url.toString());
    }

    replace(): void {
        window.history.replaceState({}, '', this.url.toString());
    }
}

export {Url};
