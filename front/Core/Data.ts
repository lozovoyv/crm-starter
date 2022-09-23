import {AxiosResponse} from "axios";
import clone from "./Helpers/Clone";
import toaster from "./Toaster/Toaster";
import {http} from "./Http/Http";

export class Data {
    /** Url to load form data */
    load_url: string | null = null;

    /** Default options to pass to request */
    options: { [index: string]: any } = {};

    data: { [index: string]: any } = {};
    payload: { [index: string]: any } = {};

    is_loading: boolean = false;
    is_loaded: boolean = false;
    is_forbidden: boolean = false;
    is_not_found: boolean = false;

    use_toaster: boolean = true;

    /** Callbacks */
    loaded_callback: ((data: object, payload: object) => void) | null = null;
    load_failed_callback: ((code: number, message: string, response: AxiosResponse) => void) | null = null;

    constructor(load_url: string | null, options: object = {}, use_toaster: boolean = true) {
        this.load_url = load_url;
        this.options = options;
        this.use_toaster = use_toaster;
    }

    /**
     * Load data.
     *
     * @param options Options to pass to request.
     * @param handleError
     *
     * @returns {Promise}
     */
    load(options: { [index: string]: any } = {}, handleError: boolean = false) {
        return new Promise((resolve: ((obj: { data: { [index: string]: any }, payload: { [index: string]: any } }) => void), reject: ((obj: { code: number, message: string, response: AxiosResponse | null }) => void)) => {

            if (this.load_url === null) {
                this.notify('Can not load data. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load data. Load URL is not defined.', response: null});
                return;
            }

            this.is_loading = true;

            let _options = clone(this.options);
            Object.keys(options).map(key => {
                _options[key] = options[key]
            });

            http.post(this.load_url, _options)
                .then(response => {
                    this.data = response.data.data;
                    this.payload = typeof response.data.payload !== "undefined" ? response.data.payload : {};
                    if (typeof this.loaded_callback === "function") {
                        this.loaded_callback(this.data, this.payload);
                    }
                    this.is_loaded = true;
                    this.is_forbidden = false;
                    this.is_not_found = false;

                    resolve({data: this.data, payload: this.payload});
                })
                .catch(error => {
                    this.is_forbidden = error.status === 403;
                    this.is_not_found = error.status === 404;
                    if ([403, 404, 500].indexOf(error.status) === -1) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    if (typeof this.load_failed_callback === "function") {
                        this.load_failed_callback(error.status, error.response.data.message, error.response);
                    }
                    if (handleError) {
                        reject({
                            code: error.status,
                            message: error.response && error.response.data && error.response.data.message,
                            response: error.response
                        });
                    }
                })
                .finally(() => {
                    this.is_loading = false;
                });
        });
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

    clear() {
        this.data = {};
        this.payload = {};
        this.is_loading = false;
        this.is_loaded = false;
        this.is_forbidden = false;
    }
}

