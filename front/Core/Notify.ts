import toaster from "@/Core/Toaster/Toaster";

export function notify(message: string, delay: number, type: 'success' | 'info' | 'error' | null, use_toaster: boolean = true): void {
    if (use_toaster) {
        toaster.show(message, delay, type);
    } else {
        if (type === 'error') {
            console.error(message);
        } else if (type === 'info') {
            console.info(message);
        } else {
            console.log(message);
        }
    }
}
