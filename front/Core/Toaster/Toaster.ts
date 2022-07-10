import {Toast} from "./Toast";
import "./toaster.scss";

class Toaster {

    private static instance: Toaster;

    container: HTMLElement;
    toasts: Toast[] = [];
    index: number = 0;

    constructor() {
        let container = document.getElementById('toaster');

        if (container === null) {
            container = document.createElement('div');
            container.id = 'toaster';
            container.className = 'toaster';
            document.body.appendChild(container);
        }

        this.container = container;
    };

    public static get Instance() {
        return this.instance || (this.instance = new Toaster());
    };

    show(message: string, delay = 0, type: 'success' | 'info' | 'error' | null = null) {
        const index = ++this.index;
        let toast = new Toast(index, this.container, message, delay, type, () => {
            this.toasts.some((toast, key) => {
                if (toast !== null && toast.id === index) {
                    this.toasts.splice(key, 1);
                    return true;
                }
                return false;
            });
        });

        this.toasts.push(toast);
    }

    success(message: string, delay: number = 0) {
        this.show(message, delay, 'success')
    }

    info(message: string, delay: number = 0) {
        this.show(message, delay, 'info')
    }

    error(message: string, delay: number = 0) {
        this.show(message, delay, 'error')
    }

    clear() {
        this.toasts.map((toast) => {
            toast.remove();
        });
        this.toasts = [];
    };
}

export default Toaster.Instance;
