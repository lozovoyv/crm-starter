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
     *
     * @returns {Promise}
     */
    load(options: { [index: string]: any } = {}) {
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

                    resolve({data: this.data, payload: this.payload});
                })
                .catch(error => {
                    if(error.status !== 500) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    this.is_forbidden = error.response.status === 403;
                    if (typeof this.load_failed_callback === "function") {
                        this.load_failed_callback(error.response.status, error.response.data.message, error.response);
                    }
                    reject({code: error.response.status, message: error.response.data.message, response: error.response});
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

