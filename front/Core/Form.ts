/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import clone from "./Helpers/Clone";
import empty from "./Helpers/Empty";
import {FieldRules, ParseFieldRules} from "./Validator/ParseRules";
import {validate} from "./Validator/Validate";
import formatErrorMessage from "./Validator/Message";
import {ErrorResponse, http} from "./Http/Http";
import {CommunicationError, CommunicationState, initialCommunicationState} from "@/Core/Types/Communications";
import {notify} from "@/Core/Notify";
import {ApiEndPoint, isApiEndPoint} from "@/Core/Http/ApiEndPoints";

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

export type FormConfig = {
    load_url?: ApiEndPoint,
    save_url?: ApiEndPoint,
    use_toaster?: boolean,
    validate_on_update?: boolean,
}

export class Form {
    /** Form config */
    config: FormConfig;

    title: string | undefined = undefined;

    values: { [index: string]: any } = {};
    originals: { [index: string]: any } = {};
    hash: string | null | undefined = undefined;
    payload: { [index: string]: any } = {};

    titles: { [index: string]: string } = {};
    rules: { [index: string]: FieldRules } = {};
    messages: { [index: string]: string } = {};

    valid: { [index: string]: boolean } = {};
    errors: { [index: string]: string[] } = {};

    state: CommunicationState;

    /**
     * Constructor
     *
     * @param config
     * @param title
     */
    constructor(config: FormConfig, title: string | undefined = undefined) {
        this.config = config;
        if (this.config.use_toaster === undefined) {
            this.config.use_toaster = true;
        }
        if (this.config.validate_on_update === undefined) {
            this.config.validate_on_update = true;
        }
        this.title = title;
        this.state = initialCommunicationState();
    };

    /**
     * Load form data.
     */
    load() {

        return new Promise((resolve: (response: FormResponse) => void, reject: (error: CommunicationError) => void) => {
            if (!isApiEndPoint(this.config.load_url)) {
                this.notify('Can not load form. Load URL is not defined.', 0, 'error');
                reject({code: 0, message: 'Can not load form. Load URL is not defined.', response: null});
                return;
            }

            this.state = {is_loaded: false, is_loading: true, is_saving: false, is_not_found: false};

            http.request(<ApiEndPoint>this.config.load_url)
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
     */
    save(options = undefined, silent: boolean = false) {
        return new Promise((resolve: (response: FormResponse) => void, reject: (error: CommunicationError) => void) => {
            if (!isApiEndPoint(this.config.save_url)) {
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

            let data = clone(options);
            data['hash'] = this.hash;
            data['data'] = this.values;

            http.request({
                url: this.config.save_url?.url,
                method: this.config.save_url?.method,
                data: data
            })
                .then(response => {
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
        notify(message, delay, type, this.config.use_toaster);
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
        if (validate || this.config.validate_on_update) {
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
        this.state = initialCommunicationState();
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
