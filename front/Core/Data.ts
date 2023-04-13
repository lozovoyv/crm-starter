import clone from "./Helpers/Clone";
import {http} from "./Http/Http";
import {notify} from "@/Core/Notify";
import {CommunicationError, CommunicationState} from "@/Core/Types/Communications";

type DataType = {[index: string]: any};

export type DataResponse = {
    data: { [index: string]: any },
    message?: string,
    payload?: { [index: string]: any },
}

export class Data<Type extends DataType> {


    /** Url to load form data */
    url: string | null = null;

    /** Default options to pass to request */
    options: { [index: string]: any } = {};

    // @ts-ignore
    data: Type = {};
    payload: { [index: string]: any } = {};

    state: CommunicationState = {
        is_loading: false,
        is_loaded: false,
        is_forbidden: false,
        is_not_found: false,
    }

    use_toaster: boolean = true;

    constructor(url: string | null, options: object = {}, use_toaster: boolean = true) {
        this.url = url;
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
        return new Promise((resolve: (response: DataResponse) => void, reject: (error: CommunicationError) => void) => {

            if (this.url === null) {
                this.notify('Can not load data. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load data. Load URL is not defined.', response: null});
                return;
            }

            this.state.is_loading = true;

            let _options = clone(this.options);
            Object.keys(options).map(key => {
                _options[key] = options[key]
            });

            http.get(this.url, {params: _options})
                .then(response => {
                    this.data = response.data.data;
                    this.payload = typeof response.data.payload !== "undefined" ? response.data.payload : {};
                    this.state.is_loaded = true;
                    this.state.is_forbidden = false;
                    this.state.is_not_found = false;

                    resolve({data: this.data, payload: this.payload});
                })
                .catch(error => {
                    this.state.is_forbidden = error.status === 403;
                    this.state.is_not_found = error.status === 404;
                    if ([403, 404, 500].indexOf(error.status) === -1) {
                        this.notify(error.data.message, 0, 'error');
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
                    this.state.is_loading = false;
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
    notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null) {
        notify(message, delay, type, this.use_toaster);
    }

    clear() {
        // @ts-ignore
        this.data = {};
        this.payload = {};
        this.state.is_loading = false;
        this.state.is_loaded = false;
        this.state.is_forbidden = false;
        this.state.is_not_found = false;
    }
}

