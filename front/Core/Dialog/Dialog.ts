import {DialogWindow} from "./DialogWindow";
import './dialog.scss';

export type DialogType = 'success' | 'info' | 'error' | 'confirmation' | null;

export type DialogWidth = {
    width?: string,
    min?: string,
    max?: string,
}

export type DialogButton = {
    result: string,
    caption: string,
    type: 'success' | 'info' | 'error' | 'default' | null,
}

class Dialog {

    private static instance: Dialog;

    container: HTMLElement;
    windows: DialogWindow[] = [];
    index: number = 0;

    constructor() {
        let container = document.getElementById('dialog');

        if (container === null) {
            container = document.createElement('div');
            container.id = 'dialog';
            container.className = 'dialog';
            document.body.appendChild(container);
        }

        this.container = container;
    };

    public static get Instance() {
        return this.instance || (this.instance = new Dialog());
    };

    public button(result: string, caption: string, type: 'success' | 'info' | 'error' | 'default' | null = null): DialogButton {
        return {
            result: result,
            caption: caption,
            type: type,
        };
    }

    show(title: string | null, message: string | null, buttons: DialogButton[], type: DialogType = null, align: 'left' | 'center' | 'right' | null = null, width: DialogWidth | null = null, blockOverlayClose: boolean = false) {
        const index = ++this.index;
        let window = new DialogWindow(
            index,
            title,
            message,
            type,
            buttons,
            align ? align : 'right',
            width,
            blockOverlayClose,
            () => {
                this.windows.some((window, key) => {
                    if (window !== null && window.id === index) {
                        window.element.remove();
                        this.windows.splice(key, 1);
                        return true;
                    }
                    return false;
                });
            }
        );

        this.windows.push(window);
        this.container.appendChild(window.element);
        window.show();

        return window.promise;
    };

    clear(): void {
        this.windows.map((window) => {
            if (window !== null) {
                window.remove();
            }
        });
        this.windows = [];
    };
}

export default Dialog.Instance;
