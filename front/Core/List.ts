import clone from "./Helpers/Clone";
import toaster from "./Toaster/Toaster";
import {ErrorResponse, http} from "./Http/Http";
import {LocalStore} from "@/Core/LocalStore";
import {CommunicationError, CommunicationState} from "@/Core/Types/Communications";

export type ListPagination = {
    current_page: number,
    last_page: number,
    from: number,
    to: number,
    total: number,
    per_page: number,
}

export type ListConfig = {
    prefix?: string,
    without_pagination?: boolean,
    without_toaster?: boolean,
    remember?: {
        pagination?: boolean,
        filters?: Array<string>,
        order?: boolean,
    }
}

export type ListOptions = { [index: string]: any }

export type ListResponse = {
    list: { [index: string]: any },
    pagination: ListPagination | undefined,
    title: string | undefined,
    titles: { [index: string]: string } | undefined,
    filters: { [index: string]: string } | undefined,
    search: string | undefined,
    order: string | undefined,
    order_by: string | undefined,
    orderable: { [index: string]: any } | undefined,
    message: string | undefined,
    payload: { [index: string]: any } | undefined,
}

export class List<Type> {

    /** Url to load form data */
    url: string | undefined = undefined;

    /** Default options to pass to request */
    options: ListOptions | undefined = undefined;

    config: ListConfig | undefined = undefined;

    list: Type[] = [];
    pagination: ListPagination | undefined = undefined;
    title: string | undefined = undefined;
    titles: { [index: string]: string } = {};
    filters: { [index: string]: any } = {};
    search: string | null = null;
    order_by: string | null = null;
    order: 'asc' | 'desc' | null = null;
    orderable: null | { [index: string]: any } = null;
    payload: { [index: string]: any } = {};

    state: CommunicationState = {
        is_loading: false,
        is_loaded: false,
        is_forbidden: false,
        is_not_found: false,
    }

    use_toaster: boolean = true;

    constructor(url: string, options: ListOptions = {}, config: ListConfig | undefined = undefined) {
        this.url = url;
        this.options = options;
        this.config = config;

        if (!config || !config.without_pagination) {
            this.pagination = {
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0,
                per_page: 10,
            };
        }
        if (!config || !config.without_toaster) {
            this.use_toaster = false;
        }

        // get remembered
        if (config && config.prefix && config.remember) {
            const prefix: string = config.prefix;
            if (config.remember.pagination && this.pagination) {
                this.pagination.per_page = Number(LocalStore.get(prefix + '::per_page'));
            }
            if (config.remember.filters) {
                let json: string | null = LocalStore.get(prefix + '::filters');
                if (json !== null) {
                    let filters: { [index: string]: any } = JSON.parse(json);
                    Object.keys(filters).map((key: string) => {
                        this.filters[key] = filters[key];
                    })
                }
            }
            if (config.remember.order) {
                this.order_by = LocalStore.get(prefix + '::order_by');
                let order = LocalStore.get(prefix + '::order');
                if (order === 'asc') {
                    this.order = 'asc';
                } else if (order === 'desc') {
                    this.order = 'desc';
                } else {
                    this.order = null;
                }
            }
        }
    }

    /**
     * Reload current page
     */
    reload() {
        return this.load(this.pagination ? this.pagination.current_page : 1, this.pagination ? this.pagination.per_page : null);
    }

    /**
     * Load list.
     *
     * @param page
     * @param perPage
     * @param suffix
     */
    load(page: number = 1, perPage: number | null = null, suffix: string|undefined = undefined) {
        return new Promise((resolve: (response: ListResponse) => void, reject: (error: CommunicationError) => void) => {
            if (!this.url) {
                this.notify('Can not load list. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load data. Load URL is not defined.', response: null});
                return;
            }

            this.state.is_loading = true;

            let options = this.prepareOptions(page, perPage);

            const url = this.url + (suffix ? `${suffix}` : '');

            http.get<ListResponse>(url, {params: options})
                .then(response => {
                    this.list = [];
                    Object.keys(response.data.list).map(key => {
                        this.list.push(response.data.list[key]);
                    });
                    this.pagination = response.data.pagination ? response.data.pagination : undefined;
                    this.title = response.data.title ? response.data.title : undefined;
                    this.titles = response.data.titles ? response.data.titles : {};
                    this.filters = response.data.filters ? response.data.filters : this.filters;
                    this.search = response.data.search ? response.data.search : this.search;
                    this.order_by = response.data.order_by ? response.data.order_by : this.order_by;
                    this.order = response.data.order ? (response.data.order === 'desc' ? 'desc' : 'asc') : this.order;
                    this.orderable = response.data.orderable ? response.data.orderable : null;
                    this.payload = response.data.payload ? response.data.payload : {};

                    this.state.is_loaded = true;
                    this.state.is_forbidden = false;
                    this.state.is_not_found = false;

                    resolve({
                        list: this.list,
                        pagination: this.pagination,
                        title: this.title,
                        titles: this.titles,
                        filters: this.filters,
                        search: this.search ?? undefined,
                        order: this.order ?? undefined,
                        order_by: this.order_by ?? undefined,
                        orderable: this.orderable ?? undefined,
                        message: response.data.message,
                        payload: this.payload,
                    });
                })
                .catch((error: ErrorResponse) => {
                    this.state.is_forbidden = error.status === 403;
                    this.state.is_not_found = error.status === 404;
                    if ([403, 404, 500].indexOf(error.status) === -1) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally(() => {
                    this.state.is_loading = false;
                });
        });
    }

    prepareOptions(page: number, perPage: number | null): ListOptions {
        let options = clone(this.options);

        options['filters'] = this.filters;
        options['search'] = this.search;
        options['order'] = this.order;
        options['order_by'] = this.order_by;
        if (page !== 1) {
            options['page'] = page;
        }
        if (!this.config || !this.config?.without_pagination) {
            if (perPage !== null) {
                options['per_page'] = perPage;
            } else {
                options['per_page'] = this.pagination && this.pagination.per_page ? this.pagination.per_page : null
            }
        }
        // remember options
        if (this.config && this.config.prefix && this.config.remember) {
            const prefix: string = this.config.prefix;
            if (this.config.remember.pagination && this.pagination) {
                LocalStore.set(prefix + '::per_page', options['per_page']);
            }
            if (this.config.remember.filters) {
                let filters: { [index: string]: any } = {};
                this.config.remember.filters.map((filter: string) => {
                    filters[filter] = this.filters[filter];
                });
                LocalStore.set(prefix + '::filters', JSON.stringify(filters));
            }
            if (this.config.remember.order) {
                LocalStore.set(prefix + '::order', options['order']);
                LocalStore.set(prefix + '::order_by', options['order_by']);
            }
        }

        return options;
    }

    /**
     * Clear list loaded data.
     */
    clear() {
        this.list = [];
        this.pagination = !this.config || this.config.without_pagination ? undefined : {
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0,
            per_page: 10,
        };
        this.title = undefined;
        this.titles = {};
        this.filters = {};
        this.search = null;
        this.order_by = null;
        this.order = null;
        this.orderable = null;
        this.payload = {};
        this.state = {
            is_loading: false,
            is_loaded: false,
            is_forbidden: false,
            is_not_found: false,
        };
    }

    /**
     * Show notification to user.
     *
     * @param message
     * @param delay
     * @param type
     */
    notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null) {
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
