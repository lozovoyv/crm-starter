/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
export type ApiEndPointMethod = 'get' | 'post' | 'put' | 'patch' | 'delete'

export type ApiEndPoint = {
    method: ApiEndPointMethod,
    url: string,
}

export function apiEndPoint(method: ApiEndPointMethod, url: string, params: { [index: string]: number | string | null } = {}): ApiEndPoint {
    let query: { [index: string]: string } = {};
    Object.keys(params).map(function (key: string) {
        const placeholder: string = '{' + key + '}';
        if (url.indexOf(placeholder) !== -1) {
            url = url.replace(placeholder, (params[key] !== null && params[key] !== undefined) ? String(params[key]) : '');
        } else if (params[key] !== null && params[key] !== undefined) {
            query[key] = String(params[key]);
        }
    });
    if (Object.keys(query).length > 0) {
        let parts = url.split('?');
        let searchParams = new URLSearchParams(parts.length === 2 ? parts[1] : {});
        Object.keys(query).map(name => {
            searchParams.append(name, query[name]);
        })
        url = parts[0] + '?' + searchParams.toString();
    }
    return {method: method, url: url};
}

export function isApiEndPoint(endpoint: any): boolean {
    return (typeof endpoint === 'object' && endpoint !== null) && (!!endpoint.url) && (endpoint.method && ['get', 'post', 'put', 'patch', 'delete'].indexOf(endpoint.method) !== -1);
}
