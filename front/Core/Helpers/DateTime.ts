class DateTime {

    static toDate(date: Date | string | null): string | null {
        if (date === null) {
            return null;
        }
        let d: Date = new Date(date);
        return d.toLocaleDateString('ru-RU');
    }

    static toTime(date: Date | string | null, seconds: boolean = false): string | null {
        if (date === null) {
            return null;
        }
        let d: Date = new Date(date);
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + (seconds ? ':' + String(d.getSeconds()).padStart(2, '0') : '');
    }

    static toDatetime(date: Date | string | null, seconds: boolean = false): string | null {
        if (date === null) {
            return null;
        }
        let d: Date = new Date(date);
        return DateTime.toDate(d) + ', ' + DateTime.toTime(d, seconds);
    }
}

export {DateTime};
