import axios, {AxiosError, AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";
import dialog from "../Dialog/Dialog";

enum StatusCode {
    Unauthorized = 401,
    Forbidden = 403,
    NotFound = 403,
    Error = 406,
    TokenExpired = 419,
    ValidationError = 422,
    TooManyRequests = 429,
    InternalServerError = 500,
}

const headers: Readonly<Record<string, string | boolean>> = {
    Accept: "application/json",
    "Content-Type": "application/json; charset=utf-8",
    "Access-Control-Allow-Credentials": true,
    "X-Requested-With": "XMLHttpRequest",
};

export type ErrorResponse = {
    status: number,
    data: {
        message: string,
        errors?: { [index: string]: string[] },
        exception?: string,
        file?: string,
        line?: string,
        trace?: { file: string, line: string }[],
    }
}

// We can use the following function to inject the JWT token through an interceptor
// We get the `accessToken` from the localStorage that we set when we authenticate
const injectToken = (config: AxiosRequestConfig): AxiosRequestConfig => {
    // try {
    //     const token = localStorage.getItem("accessToken");
    //
    //     if (token != null) {
    //         config.headers.Authorization = `Bearer ${token}`;
    //     }
    return config;
    // } catch (error) {
    //     throw new Error(error);
    // }
};

class Http {
    private instance: AxiosInstance | null = null;

    private get http(): AxiosInstance {
        return this.instance !== null ? this.instance : this.initHttp();
    }

    initHttp() {
        const http = axios.create({
            headers,
            withCredentials: true,
        });

        http.interceptors.request.use(injectToken, (error) => Promise.reject(error));

        http.interceptors.response.use(
            (response: AxiosResponse) => response,
            (error: AxiosError<ErrorResponse, any>) => {
                const {response} = error;
                return Http.handleError<ErrorResponse>(response);
            }
        );

        this.instance = http;
        return http;
    }

    request<T = any, R = AxiosResponse<T>>(config: AxiosRequestConfig): Promise<R> {
        return this.http.request(config);
    }

    get<T = any, R = AxiosResponse<T>>(url: string, config?: AxiosRequestConfig): Promise<R> {
        return this.http.get<T, R>(url, config);
    }

    post<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: T,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.post<T, R>(url, data, config);
    }

    put<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: T,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.put<T, R>(url, data, config);
    }

    delete<T = any, R = AxiosResponse<T>>(url: string, config?: AxiosRequestConfig): Promise<R> {
        return this.http.delete<T, R>(url, config);
    }

    // Handle global app errors
    // We can handle generic app errors depending on the status code
    private static handleError<T = any, R = AxiosResponse<T>>(response: undefined | AxiosResponse<T>) {
        if (typeof response === "undefined") {
            return Promise.reject<R>(response);
        }

        const error: ErrorResponse = {
            status: response.status,
            data: <any>response.data,
        };

        switch (error.status) {
            case StatusCode.InternalServerError: {
                let message = '<b>' + error.data.message + '</b>';
                if (error.data.exception) message += '</br></br>Exception: <b>' + error.data.exception + '</b>';
                if (error.data.file) message += '</br></br>File: <b>' + error.data.file + '</b>';
                if (error.data.line) message += '</br>Line: <b>' + error.data.line + '</b>';
                if (error.data.trace) {
                    message += '<br/><br/><b>Trace:</b>'
                    error.data.trace.map(item => {
                        message += '<br/>' + item.file + ':' + item.line;
                    })
                }
                dialog.show('Ошибка сервера', message, [dialog.button('close', 'Закрыть', 'default'), dialog.button('copy', 'Скопировать и закрыть')], 'error')
                    .then(result => {
                        if(result === 'copy') {
                            let message = error.data.message + '\n';
                            if (error.data.exception) message += 'Exception: ' + error.data.exception + '\n';
                            if (error.data.file) message += 'File: ' + error.data.file + '\n';
                            if (error.data.line) message += 'Line: ' + error.data.line + '\n';
                            if (error.data.trace) {
                                message += 'Trace:\n'
                                error.data.trace.map(item => {
                                    message += item.file + ':' + item.line + '\n';
                                })
                            }
                            navigator.clipboard.writeText(message);
                        }
                    });
                break;
            }
            case StatusCode.Forbidden: {
                // Handle Forbidden
                break;
            }
            case StatusCode.Unauthorized: {
                // Handle Unauthorized
                break;
            }
            case StatusCode.TooManyRequests: {
                // Handle TooManyRequests
                break;
            }
        }

        return Promise.reject<R>(response);
    }
}

export const http = new Http();
