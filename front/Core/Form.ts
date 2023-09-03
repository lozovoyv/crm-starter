import clone from "./Helpers/Clone";
import empty from "./Helpers/Empty";
import {FieldRules, ParseFieldRules} from "./Validator/ParseRules";
import {validate} from "./Validator/Validate";
import formatErrorMessage from "./Validator/Message";
import {ErrorResponse, http} from "./Http/Http";
import {CommunicationError, CommunicationState} from "@/Core/Types/Communications";
import {notify} from "@/Core/Notify";

export type FormResponse = {
    values: { [index: string]: any },
    title?: string,
    titles?: { [index: string]: string },
    rules?: { [index: string]: string },
    messages?: { [index: string]: string },
    hash?: string | null,
    message?: string,
    payload?: { [index: string]: any },
}

export class Form {

    /** Url to load form data */
    url_load: string | null = null;

    /** Url to save form data */
    url_save: string | null = null;

    /** Model ID */
    id: number | undefined;

    /** Default options to pass to request */
    options: { [index: string]: any } = {};

    values: { [index: string]: any } = {};
    title: string | undefined = undefined;
    titles: { [index: string]: string } = {};
    rules: { [index: string]: FieldRules } = {};
    messages: { [index: string]: string } = {};
    hash: string | null | undefined = undefined;
    payload: { [index: string]: any } = {};

    originals: { [index: string]: any } = {};
    valid: { [index: string]: boolean } = {};
    errors: { [index: string]: string[] } = {};

    state: CommunicationState = {
        is_loading: false,
        is_loaded: false,
        is_saving: false,
        is_saved: false,
        is_forbidden: false,
        is_not_found: false,
    }

    use_toaster: boolean = true;
    validate_on_update: boolean = false;

    /**
     * Constructor
     *
     * @param title
     * @param url
     * @param options
     * @param use_toaster
     */
    constructor(url: string|null, title: string | undefined = undefined, options: { [index: string]: any } = {}, use_toaster: boolean = true) {
        this.title = title;
        this.url_load = url;
        this.url_save = url;
        this.options = options;
        this.use_toaster = use_toaster;
    };

    /**
     * Load form data.
     *
     * @param id
     * @param options Options to pass to load request. Overrides form default options if not null.
     */
    load(id: number | undefined, options: { [index: string]: any } | null = null) {

        this.id = id;

        return new Promise((resolve: (response: FormResponse) => void, reject: (error: CommunicationError) => void) => {
            if (this.url_load === null || this.url_load === '') {
                this.notify('Can not load form. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load form. Load URL is not defined.', response: null});
                return;
            }

            this.state = {is_loaded: false, is_loading: true, is_saving: false, is_not_found: false};

            const url = this.url_load + (this.id ? `/${this.id}` : '');

            http.get(url, {params: options !== null ? options : this.options})
                .then(response => {
                    this.values = response.data.values;
                    this.originals = clone(this.values);
                    this.title = response.data.title ? response.data.title : this.title;
                    this.titles = response.data.titles;
                    this.rules = {};
                    Object.keys(response.data.rules).map(key => {
                        this.rules[key] = ParseFieldRules(response.data.rules[key]);
                    });
                    this.messages = response.data.messages ? response.data.messages : [];
                    this.hash = response.data.hash ? response.data.hash : null;
                    this.payload = !empty(response.data.payload) ? response.data.payload : {};

                    this.valid = {};
                    this.errors = {};

                    this.state.is_loaded = true;
                    this.state.is_forbidden = false;
                    this.state.is_not_found = false;

                    resolve({
                        values: this.values,
                        payload: this.payload,
                    });
                })
                .catch((error: ErrorResponse) => {
                    if (error.status !== 500) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    this.state.is_forbidden = error.status === 403;
                    this.state.is_not_found = error.status === 404;

                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally((): void => {
                    this.state.is_loading = false;
                });
        });
    };

    /**
     * Save form data.
     *
     * @param options Options to pass to load request. Overrides form default options if not null.
     * @param silent Disable notifications for this operation.
     * @param use_post Use post method.
     */
    save(options = null, silent: boolean = false, use_post: boolean = false) {
        return new Promise((resolve: (response: FormResponse) => void, reject: (error: CommunicationError) => void) => {
            if (this.url_save === null) {
                this.notify('Can not save form. Save URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not save form. Save URL is not defined.', response: null});
                return;
            }

            if (this.state.is_loading || !this.state.is_loaded) {
                this.notify('Form is not loaded or in loading process.', 0, 'error');
                reject({code: 0, message: 'Form is not loaded or in loading process.', response: null});
                return;
            }

            this.state.is_saving = true;

            let _options = clone(options !== null ? options : this.options);
            _options['hash'] = this.hash;
            _options['data'] = this.values;

            const url = this.url_save + (this.id ? `/${this.id}` : '');
            let promise;

            if (use_post) {
                promise = http.post(url, _options);
            } else {
                promise = http.put(url, _options);
            }

            promise.then(response => {
                this.state.is_forbidden = false;
                if (!silent) {
                    this.notify(response.data.message, 5000, 'success');
                }
                this.originals = clone(this.values);
                if (!empty(response.data.payload)) {
                    this.payload = response.data.payload;
                }
                resolve({values: this.values, payload: this.payload});
            })
                .catch((error: ErrorResponse) => {
                    this.state.is_forbidden = error.status === 403;
                    this.state.is_not_found = error.status === 404;
                    if (error.status === 422) {
                        if (!silent) {
                            this.notify(error.data.message, 5000, 'error');
                        }
                        if (typeof error.data.errors !== "undefined") {
                            this.errors = error.data.errors;
                            Object.keys(this.errors).map(key => {
                                this.valid[key] = false;
                            });
                        }
                    }
                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally(() => {
                    this.state.is_saved = true;
                    this.state.is_saving = false;
                });
        });
    };

    /**
     * Set form state is loaded manually.
     */
    setLoaded(): void {
        this.state.is_loaded = true;
    }

    /**
     * Set form title.
     *
     * @param title
     */
    setTitle(title: string): void {
        this.title = title;
    }


    /**
     * Show notification to user.
     *
     * @param message
     * @param delay
     * @param type
     */
    notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null): void {
        notify(message, delay, type, this.use_toaster);
    };

    /**
     * Set field attributes.
     *
     * @param name
     * @param value
     * @param rules
     * @param title
     * @param initial
     */
    set(name: string, value: unknown, rules: string | null | undefined = undefined, title: string | undefined = undefined, initial: boolean | undefined = undefined) {
        this.values[name] = value;
        if (rules !== undefined) {
            if (empty(rules)) {
                delete this.rules[name];
            } else {
                this.rules[name] = ParseFieldRules(rules);
            }
        }
        if (title !== undefined || typeof this.titles[name] === "undefined") {
            this.titles[name] = title ? title : name;
        }
        if (initial !== undefined) {
            this.originals[name] = value;
        }
    };

    /**
     * Update field value.
     *
     * @param name
     * @param value
     * @param validate Force validation
     */
    update(name: string, value: any, validate: boolean = false) {
        this.values[name] = value;
        if (validate || this.validate_on_update) {
            this.validate(name);
        } else {
            this.errors[name] = [];
            this.valid[name] = true;
        }
    };

    /**
     * Validate all (or single) fields.
     *
     * @param name
     * @returns {boolean}
     */
    validate(name: string | null = null): boolean {
        if (name === null) {
            let all: boolean = true;
            Object.keys(this.rules).map(key => {
                all = this.validate(key) && all;
            });
            return all;
        }
        if (empty(this.rules) || Object.keys(this.rules[name]).length === 0) {
            this.errors[name] = [];
            this.valid[name] = true;
            return true;
        }
        let failed = validate(name, this.values[name], this.rules[name], this.values);

        if (failed.length === 0) {
            this.errors[name] = [];
            this.valid[name] = true;
            return true;
        }

        this.errors[name] = [];
        failed.map((failed_rule: string) => {
            const error: string | null = formatErrorMessage(name, this.values[name], failed_rule, this.rules[name], this.titles, this.values, this.messages);
            if (error) {
                this.errors[name].push(error);
            }
        });

        this.valid[name] = false;

        return false;
    };

    /**
     * Update originals to current changes.
     */
    apply(): void {
        Object.keys(this.values).map(key => {
            this.originals[key] = this.values[key];
        });
    }

    /**
     * Reset form changes to originals.
     */
    clear(except: null | Array<string> = null) {
        Object.keys(this.originals).map(key => {
            if (except === null || except?.indexOf(key) === -1) {
                this.update(key, this.originals[key]);
            }
        });
    };

    /**
     * Unset all form fields and data.
     */
    reset() {
        this.payload = {};
        this.values = {};
        this.originals = {};
        this.titles = {};
        this.rules = {};
        this.valid = {};
        this.errors = {};
        this.state.is_loaded = false;
        this.state.is_loading = false;
        this.state.is_saving = false;
        this.state.is_saved = false;
        this.state.is_forbidden = false;
    };

    /**
     * Unset field.
     *
     * @param name
     */
    unset(name: string) {
        delete this.values[name];
        delete this.originals[name];
        delete this.titles[name];
        delete this.rules[name];
        delete this.valid[name];
        delete this.errors[name];
    };
}
