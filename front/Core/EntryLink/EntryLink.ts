/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import router from "@/router";
import "./entry_link.scss";

type EntryMapItem = {
    resolver: (type: string, id: string | number, tag: string | null) => object,
    prefix?: string,
}

const entryMap: { [index: string]: EntryMapItem } = {
    'App\\Models\\Users\\User': {
        resolver: (type, id, tag) => ({name: 'user_view', params: {id: id}}),
        prefix: 'учётная запись',
    }
}

export function makeLink(entry_caption: string | null, entry_type: string | null, entry_id: number | string | null, entry_tag: string | null = null, has_entry: boolean = true, withPrefix: boolean = false): string | undefined {
    if (entry_caption === null) {
        return undefined;
    }

    if (!has_entry || entry_type === null || entry_id === null) {
        return makeSpanTag(entry_caption);
    }

    const entry = entryMap[entry_type];
    if (!entry) {
        return makeSpanTag(entry_caption);
    }

    const link = router.resolve(entry.resolver(entry_type, entry_id, entry_tag)).href;

    return makeLinkTag(link, (entry.prefix && withPrefix ? entry.prefix + ' ' : '') + entry_caption);
}

function makeSpanTag(caption: string): string {
    return `<span>${caption}</span>`;
}

function makeLinkTag(link: string, caption: string): string {
    return `<a href="${link}" class="entry-link">${caption}</a>`;
}
