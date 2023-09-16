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
    Object.keys(params).map(function (key: string) {
        url = url.replace('{' + key + '}', params[key] !== null ? String(params[key]) : '');
    });
    return {method: method, url: url};
}

export function isApiEndPoint(endpoint: any): boolean {
    return (typeof endpoint === 'object' && endpoint !== null) && (!!endpoint.url) && (endpoint.method && ['get', 'post', 'put', 'patch', 'delete'].indexOf(endpoint.method) !== -1);
}
