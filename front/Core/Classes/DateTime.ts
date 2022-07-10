/**
 * Format:
 * Y-m-dTH:i:s
 * Y-m-dTH:i
 * Y-m-d
 * H:i:s
 * H:i
 */

export class DateTime {
    year: number | null = null;
    month: number | null = null;
    day: number | null = null;
    hours: number | null = null;
    minutes: number | null = null;
    seconds: number | null = null;

    use_date: boolean = true;
    use_time: boolean = true;
    use_seconds: boolean = false;

    constructor(value: object | string | null, date: boolean = true, time: boolean = true, seconds: boolean = false) {
        this.use_date = date;
        this.use_time = time;
        this.use_seconds = seconds;

        if (value === null) return;

        if (value instanceof Date) {
            if (date) {
                this.year = value.getFullYear();
                this.month = value.getMonth() + 1;
                this.day = value.getDate();
            }
            if (time) {
                this.hours = value.getHours();
                this.minutes = value.getMinutes();
                if (seconds) {
                    this.seconds = value.getSeconds();
                }
            }
        }
    }

    toString(): string | null {
        if (!this.isValid()) {
            return null;
        }

        let output = '';

        if (this.use_date) {
            output += `${this.year}-${this.month}-${this.day}`;
        }
        if (this.use_date && this.use_time) {
            output += 'T';
        }
        if (this.use_time) {
            output += `${this.hours}:${this.minutes}`;
            if (this.use_seconds) {
                output += `:${this.seconds}`;
            }
        }

        return output;
    }

    isValid(): boolean {
        return (!this.use_date || this.year !== null && this.month !== null && this.day !== null) &&
            (!this.use_time || this.hours !== null && this.minutes !== null && (!this.use_seconds || this.seconds !== null));
    }
}
