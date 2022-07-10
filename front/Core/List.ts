import clone from "./Helpers/Clone";
import toaster from "./Toaster/Toaster";
import {ErrorResponse, http} from "./Http/Http";

type ListPagination = {
    current_page: number,
    last_page: number,
    from: number,
    to: number,
    total: number,
    per_page: number,
}

type ListResponse = {
    message: string,
    list: { [index: string]: any },
    filters: { [index: string]: string },
    default_filters: { [index: string]: string },
    titles: { [index: string]: string },
    payload: { [index: string]: any },
    pagination: ListPagination,
}

export class List {

    /** Url to load form data */
    load_url: string | null = null;
    /** Default options to pass to request */
    options: { [index: string]: any } = {};

    /** Pagination */
    pagination: ListPagination | null = null;

    titles: { [index: string]: string } = {};
    list: any[] = [];
    payload: { [index: string]: any } = {};

    filters: { [index: string]: any } = {};
    default_filters: { [index: string]: any } = {};

    search: string | null = null;
    search_by: null | { [index: string]: any } = null;

    order_by: string | null = null;
    order: 'asc' | 'desc' | null = null;

    is_loading: boolean = false;
    is_loaded: boolean = false;
    is_forbidden: boolean = false;

    use_toaster: boolean = true;

    /** Callbacks */
    loaded_callback: ((list: object, titles: object, payload: object) => void) | null = null;
    load_failed_callback: ((code: number, message: string, response: ErrorResponse) => void) | null = null;

    constructor(load_url: string | null, options: object = {}, pagination: boolean = true, use_toaster: boolean = true) {
        this.load_url = load_url;
        this.options = options;
        this.use_toaster = use_toaster;

        if (pagination) {
            this.pagination = {
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0,
                per_page: 10,
            };
        }
    }

    /**
     * Reload current page
     */
    reload() {
        return this.load(this.pagination ? this.pagination.current_page : 1, this.pagination ? this.pagination.per_page : null);
    }

    /**
     * Initial list load
     */
    initial() {
        return this.load(1, null, true);
    }

    /**
     * Load list.
     *
     * @param page
     * @param perPage
     * @param initial
     */
    load(page: number = 1, perPage: number | null = null, initial: boolean = false) {
        return new Promise((resolve: ((obj: { list: any[], titles: { [index: string]: string }, payload: { [index: string]: any } }) => void), reject: ((obj: { code: number, message: string, response: ErrorResponse | null }) => void)) => {
            if (this.load_url === null) {
                this.notify('Can not load list. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load data. Load URL is not defined.', response: null});
                return;
            }

            this.is_loading = true;

            let options = clone(this.options);

            options['filters'] = this.filters;
            options['search'] = this.search;
            options['search_by'] = this.search_by;
            options['order'] = this.order;
            options['order_by'] = this.order_by;
            options['page'] = page;
            options['per_page'] = perPage !== null ? perPage : (this.pagination ? this.pagination.per_page : null);
            options['initial'] = initial;

            http.post<ListResponse>(this.load_url, options)
                .then(response => {
                    this.list = [];
                    Object.keys(response.data.list).map(key => {
                        this.list.push(response.data.list[key]);
                    });
                    this.titles = typeof response.data.titles !== "undefined" ? response.data.titles : {};
                    this.pagination = typeof response.data.pagination !== "undefined" ? response.data.pagination : null;
                    this.filters = typeof response.data.filters !== "undefined" && response.data.filters !== null && Object.keys(response.data.filters).length > 0 ? response.data.filters : {};
                    this.default_filters = typeof response.data.default_filters !== "undefined" && response.data.default_filters !== null ? response.data.default_filters : {};
                    this.payload = typeof response.data.payload !== "undefined" ? response.data.payload : {};

                    this.is_loaded = true;
                    this.is_forbidden = false;

                    if (typeof this.loaded_callback === "function") {
                        this.loaded_callback(this.list, this.titles, this.payload);
                    }
                    resolve({list: this.list, titles: this.titles, payload: this.payload});
                })
                .catch((error: ErrorResponse) => {
                    if (error.status !== 500) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    this.is_forbidden = error.status === 403;
                    if (typeof this.load_failed_callback === "function") {
                        this.load_failed_callback(error.status, error.data.message, error);
                    }
                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally(() => {
                    this.is_loading = false;
                });
        });
    }

    /**
     * Clear list loaded data.
     */
    clear() {
        this.titles = {};
        this.list = [];
        this.payload = {};
        this.is_loading = false;
        this.is_loaded = false;
        this.is_forbidden = false;
    }

    /**
     * Show notification to user.
     *
     * @param message
     * @param delay
     * @param type
     */
    protected notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null) {
        if (this.use_toaster) {
            toaster.show(message, delay, type);
        } else {
            if (type === 'error') {
                console.error(message);
            } else {
                console.log(message);
            }
        }
    }
}
