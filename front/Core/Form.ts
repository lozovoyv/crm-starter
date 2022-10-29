import clone from "./Helpers/Clone";
import empty from "./Helpers/Empty";
import {FieldRules, ParseFieldRules} from "./Validator/ParseRules";
import {validate} from "./Validator/Validate";
import formatErrorMessage from "./Validator/Message";
import toaster from "./Toaster/Toaster";
import {ErrorResponse, http} from "./Http/Http";

export class Form {

    /** Url to load form data */
    load_url: string | null = null;
    /** Url to save form data */
    save_url: string | null = null;
    /** Default options to pass to request */
    options: { [index: string]: any } = {};
    /** Form title */
    title: string | null = null;

    /** Validate field when its value changed */
    validate_on_update: boolean = false;

    values: { [index: string]: any } = {};
    originals: { [index: string]: any } = {};
    hash: string | null = null;
    titles: { [index: string]: string } = {};
    rules: { [index: string]: FieldRules } = {};
    payload: { [index: string]: any } = {};

    valid: { [index: string]: boolean } = {};
    errors: { [index: string]: string[] } = {};

    is_loading: boolean = false
    is_loaded: boolean = false
    is_saving: boolean = false
    is_saved: boolean = false
    is_forbidden: boolean = false
    is_not_found: boolean = false;

    use_toaster: boolean = true;

    /** Callbacks */
    loaded_callback: ((values: object, payload: object) => void) | null = null;
    load_failed_callback: ((code: number, message: string, response: ErrorResponse) => void) | null = null;
    saved_callback: ((values: object, payload: object) => void) | null = null;
    save_failed_callback: ((code: number, message: string, response: ErrorResponse) => void) | null = null;

    /**
     * Constructor
     *
     * @param title
     * @param load_url
     * @param save_url
     * @param options
     * @param use_toaster
     */
    constructor(title: string | null, load_url: string | null, save_url: string | null, options: object = {}, use_toaster: boolean = true) {
        this.title = title;
        this.load_url = load_url;
        this.save_url = save_url;
        this.options = options;
        this.use_toaster = use_toaster;
    };

    /**
     * Set form title.
     *
     * @param title
     */
    setTitle(title: string): void {
        this.title = title;
    }

    /**
     * Load form data.
     *
     * @param options Options to pass to load request. Overrides form default options if not null.
     */
    load(options: { [index: string]: any } | null = null) {
        return new Promise((resolve: ((obj: { values: { [index: string]: any }, payload: { [index: string]: any } }) => void), reject: ((obj: { code: number, message: string, response: ErrorResponse | null }) => void)) => {
            if (this.load_url === null || this.load_url === '') {
                this.is_loaded = true;
                if (typeof this.loaded_callback === "function") {
                    this.loaded_callback(this.values, this.payload);
                }
                resolve({values: this.values, payload: this.payload});
                return;
            }

            this.is_loaded = false;
            this.is_loading = true;
            this.is_saving = false;
            this.is_not_found = false;

            http.post(this.load_url, options !== null ? options : this.options)
                .then(response => {
                    this.values = response.data.values;
                    this.originals = clone(this.values);
                    this.hash = response.data.hash ? response.data.hash : null;
                    this.title = response.data.title ? response.data.title : this.title;
                    this.titles = response.data.titles;
                    this.rules = {};
                    Object.keys(response.data.rules).map(key => {
                        this.rules[key] = ParseFieldRules(response.data.rules[key]);
                    });
                    this.payload = !empty(response.data.payload) ? response.data.payload : {};
                    this.valid = {};
                    this.errors = {};

                    this.is_loaded = true;
                    this.is_forbidden = false;
                    this.is_not_found = false;

                    if (typeof this.loaded_callback === "function") {
                        this.loaded_callback(this.values, this.payload);
                    }

                    resolve({values: this.values, payload: this.payload});
                })
                .catch((error: ErrorResponse) => {
                    if (error.status !== 500) {
                        this.notify(error.data.message, 0, 'error');
                    }
                    this.is_forbidden = error.status === 403;
                    this.is_not_found = error.status === 404;

                    if (typeof this.load_failed_callback === "function") {
                        this.load_failed_callback(error.status, error.data.message, error);
                    }

                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally((): void => {
                    this.is_loading = false;
                });
        });
    };

    /**
     * Save form data.
     *
     * @param options Options to pass to load request. Overrides form default options if not null.
     * @param silent Disable notifications for this operation.
     */
    save(options = null, silent: boolean = false) {
        return new Promise((resolve: ((obj: { values: object, payload: object }) => void), reject: ((obj: { code: number, message: string, response: ErrorResponse | null }) => void)) => {
            if (this.save_url === null) {
                this.notify('Can not save form. Save URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not save form. Save URL is not defined.', response: null});
                return;
            }

            if (this.is_loading || !this.is_loaded) {
                this.notify('Form is not loaded or in loading process.', 0, 'error');
                return;
            }

            this.is_saving = true;

            let _options = clone(options !== null ? options : this.options);
            _options['hash'] = this.hash;
            _options['data'] = this.values;

            http.post(this.save_url, _options)
                .then(response => {
                    this.is_forbidden = false;
                    if (!silent) {
                        this.notify(response.data.message, 5000, 'success');
                    }
                    this.originals = clone(this.values);
                    if (!empty(response.data.payload)) {
                        this.payload = response.data.payload;
                    }
                    if (typeof this.saved_callback === "function") {
                        this.saved_callback(this.values, this.payload);
                    }
                    resolve({values: this.values, payload: this.payload});
                })
                .catch((error: ErrorResponse) => {
                    this.is_forbidden = error.status === 403;
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
                    } else {
                        if (error.status !== 500) {
                            this.notify(error.data.message, 0, 'error');
                        }
                        if (typeof this.save_failed_callback === "function") {
                            this.save_failed_callback(error.status, error.data.message, error);
                        }
                    }
                    reject({code: error.status, message: error.data.message, response: error});
                })
                .finally(() => {
                    this.is_saved = true;
                    this.is_saving = false;
                });
        });
    };

    /**
     * Show notification to user.
     *
     * @param message
     * @param delay
     * @param type
     */
    protected notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null): void {
        if (this.use_toaster) {
            toaster.show(message, delay, type);
        } else {
            if (type === 'error') {
                console.error(message);
            } else {
                console.log(message);
            }
        }
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
            const error: string | null = formatErrorMessage(name, this.values[name], failed_rule, this.rules[name], this.titles, this.values);
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
    clear() {
        Object.keys(this.originals).map(key => {
            this.update(key, this.originals[key]);
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
        this.is_loaded = false;
        this.is_loading = false;
        this.is_saving = false;
        this.is_saved = false;
        this.is_forbidden = false;
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
