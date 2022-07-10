import {DialogButton, DialogType, DialogWidth} from "./Dialog";

export class DialogWindow {
    id: number;
    element: HTMLElement;
    blockOverlayClose: boolean;
    unset: () => void;
    promise: Promise<string | null>;
    resolve: ((result: string | null) => void) | undefined = undefined;

    constructor(id: number, title: string | null, message: string | null, type: DialogType, buttons: DialogButton[], align: 'left' | 'center' | 'right', width: DialogWidth | null, blockOverlayClose: boolean = false, unset: () => void) {
        this.id = id;
        this.unset = unset;
        this.blockOverlayClose = blockOverlayClose;

        // Create window
        const window = document.createElement("div");
        window.className = "dialog__window";
        if (width !== null) {
            if (width.min) {
                window.style.minWidth = width.min;
            }
            if (width.width) {
                window.style.width = width.width;
            }
            if (width.max) {
                window.style.maxWidth = width.max;
            }
        }

        // Append header
        window.appendChild(this.createHeader(title, type));

        // Append message container
        if (message !== null) {
            const messageContainer = document.createElement("div");
            messageContainer.className = "dialog__window-message";
            messageContainer.innerHTML = message;
            window.appendChild(messageContainer);
        }

        // Create buttons
        if (buttons.length > 0) {
            const buttonsContainer = document.createElement("div");
            buttonsContainer.className = "dialog__window-buttons";
            buttonsContainer.classList.add("dialog__window-buttons-" + align);
            buttons.map(button => {
                const windowButton = document.createElement("span");
                windowButton.className = 'dialog__window-buttons-button';
                if (button.type !== null) {
                    windowButton.classList.add('dialog__window-buttons-button-' + button.type);
                }
                windowButton.innerHTML = button.caption;
                windowButton.addEventListener('click', () => this.click(button.result));
                buttonsContainer.appendChild(windowButton);
            });
            window.appendChild(buttonsContainer);
        }

        // Create overlay
        const overlay = document.createElement("div");
        overlay.className = "dialog__overlay";
        overlay.addEventListener('click', this.discard.bind(this));
        overlay.appendChild(window);

        // Prepare options
        this.element = overlay;

        this.promise = new Promise((resolve: (result: string | null) => void) => {
            this.resolve = resolve;
        });
    }

    createHeader(title: string | null, type: DialogType): HTMLElement {
        const header = document.createElement("div");
        header.className = "dialog__window-header";

        // icon
        if (type !== null) {
            const icon = document.createElement("div");
            icon.className = "dialog__window-header-icon";
            icon.classList.add("dialog__window-header-icon-" + type);
            switch (type) {
                case 'success':
                    icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"/></svg>';
                    break;
                case 'info':
                    icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"/></svg>';
                    break;
                case 'error':
                    icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"/></svg>';
                    break;
                case 'confirmation':
                    icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" shape-rendering="geometricPrecision"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z"/></svg>';
                    break;
            }
            header.appendChild(icon);
        }

        // title
        const headerTitle = document.createElement("div");
        headerTitle.className = "dialog__window-header-title";
        if (title !== null) {
            headerTitle.innerHTML = title;
        }
        header.appendChild(headerTitle);

        // close button
        const close = document.createElement("div");
        close.className = "dialog__window-header-close";
        close.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">' +
            '<polygon fill="currentColor" points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>' +
            '</svg>';

        close.addEventListener('click', () => {
            this.click(null);
            this.remove();
        });
        header.appendChild(close);

        return header;
    }

    click(result: string | null) {
        if (this.resolve) {
            this.resolve(result);
        }
        this.remove();
    }

    remove() {
        this.element.classList.add('dialog__overlay-hide');
        setTimeout(() => {
            this.unset();
        }, 300);
    }

    show() {
        setTimeout(() => {
            this.element.classList.add('dialog__overlay-shown');
        }, 100);
    }

    discard(event: Event) {
        if (event.target === this.element && !this.blockOverlayClose) {
            this.click(null);
        }
    }
}
