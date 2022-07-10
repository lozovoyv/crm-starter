export class Toast {

    id: number | null = null;
    container: HTMLElement | null = null;
    message: string | null = null;
    showTime: number = 0;
    type: 'success' | 'info' | 'error' | null = null;
    element: HTMLElement | null = null;
    remains: number = 0;
    interval: number | undefined = undefined;
    resolve: Function | null = null;

    constructor(id: number, container: HTMLElement, message: string, showTime: number, type: 'success' | 'info' | 'error' | null, resolve: Function) {
        this.id = id;
        this.container = container;
        this.message = message;
        this.showTime = showTime;
        this.type = type;
        this.resolve = resolve;

        // Create elements

        const close: HTMLElement = document.createElement("div");
        close.className = "toaster__toast-close";
        close.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">' +
            '<polygon fill="currentColor" points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>' +
            '</svg>';
        close.addEventListener('click', () => {
            this.remove();
        });

        this.element = document.createElement("div");
        this.element.className = "toaster__toast";

        switch (this.type) {
            case 'success':
                this.element.classList.add('toaster__toast-success');
                break;
            case 'info':
                this.element.classList.add('toaster__toast-info');
                break;
            case 'error':
                this.element.classList.add('toaster__toast-error');
                break;
        }

        this.element.innerHTML = this.message;
        this.element.appendChild(close);
        this.container.appendChild(this.element);

        this.startTimer();
        this.show();
    }

    remove() {
        window.clearInterval(this.interval);
        if (this.element === null) return;
        this.element.classList.add('toaster__toast-hide');
        window.setTimeout(() => {
            this.unset();
        }, 200);
    }

    unset() {
        if (this.element === null) return;
        this.element.remove();
        if (typeof this.resolve === "function") {
            this.resolve(this);
        }
    }

    show() {
        window.setTimeout(() => {
            if (this.element === null) return;
            this.element.classList.add('toaster__toast-show');
        }, 100);
    }

    startTimer() {
        if (this.showTime !== 0) {
            this.remains = this.showTime;
            this.interval = window.setInterval(() => {
                this.remains -= 100;
                if (this.remains <= 0) {
                    this.remove();
                }
            }, 100);
        }
    }
}
