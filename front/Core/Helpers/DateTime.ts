function toDate(date: Date | string | null): string | null {
    if (date === null) {
        return null;
    }
    let d: Date = new Date(date);
    return d.toLocaleDateString('ru-RU');
}

function toTime(date: Date | string | null, seconds: boolean = false): string | null {
    if (date === null) {
        return null;
    }
    let d: Date = new Date(date);
    return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + (seconds ? ':' + String(d.getSeconds()).padStart(2, '0') : '');
}

function toDatetime(date: Date | string | null, seconds: boolean = false): string | null {
    if (date === null) {
        return null;
    }
    let d: Date = new Date(date);
    return toDate(d) + ', ' + toTime(d, seconds);
}

export {toDate, toTime, toDatetime};
