import dialog, {DialogButton} from "@/Core/Dialog/Dialog";
import {http} from "@/Core/Http/Http";
import toaster from "@/Core/Toaster/Toaster";
import {ApiEndPoint} from "@/Core/Http/ApiEndPoints";

export type ProcessEntryConfig = {
    title: string,
    question: string,
    button: DialogButton,
    url: ApiEndPoint,
    options: { [index: string]: any },
    progress?: (e: boolean) => void
}

function processEntry(config: ProcessEntryConfig) {
    return new Promise((resolve) => {

        dialog.show(config.title, config.question, [dialog.button('cancel', 'Отмена'), config.button])
            .then(result => {
                if (result !== 'cancel' && result !== null) {
                    if (config.progress) {
                        config.progress(true);
                    }

                    http.request({
                        url: config.url?.url,
                        method: config.url?.method,
                        data: config.options
                    })
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
