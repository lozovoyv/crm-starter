import dialog, {DialogButton} from "@/Core/Dialog/Dialog";
import {http} from "@/Core/Http/Http";
import toaster from "@/Core/Toaster/Toaster";

export type ProcessEntryConfig = {
    title: string,
    question: string,
    button: DialogButton,
    method: 'put' | 'patch' | 'post' | 'delete',
    url: string,
    options: { [index: string]: any },
    progress?: (e: boolean) => void
}

function processEntry(config: ProcessEntryConfig) {
    return new Promise((resolve) => {

        dialog.show(config.title, config.question, [config.button, dialog.button('cancel', 'Отмена')])
            .then(result => {
                if (result !== 'cancel' && result !== null) {
                    if (config.progress) {
                        config.progress(true);
                    }

                    let promise;

                    switch (config.method) {
                        case "patch":
                            promise = http.patch(config.url, config.options);
                            break;
                        case "put":
                            promise = http.put(config.url, config.options);
                            break;
                        case "delete":
                            promise = http.delete(config.url, {data: config.options});
                            break;
                        default:
                            promise = http.post(config.url, config.options);
                    }

                    promise
                        .then(response => {
                            toaster.success(response.data['message'], 3000);
                            resolve(response);
                        })
                        .catch(error => {
                            toaster.error(error.data['message'], 3000);
                        })
                        .finally(() => {
                            if (config.progress) {
                                config.progress(false);
                            }
                        });
                }
            })
    });
}

export {processEntry};
