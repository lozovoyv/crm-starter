import dialog, {DialogButton} from "@/Core/Dialog/Dialog";
import {http} from "@/Core/Http/Http";
import toaster from "@/Core/Toaster/Toaster";

function processEntry(title: string, question: string, button: DialogButton, url: string, options: { [index: string]: any }, progress: (e: boolean) => void) {
    return new Promise((resolve) => {
        dialog.show(title, question, [button, dialog.button('cancel', 'Отмена')])
            .then(result => {
                if (result !== 'cancel' && result !== null) {
                    progress(true);

                    http.post(url, options)
                        .then(response => {
                            toaster.success(response.data['message'], 3000);
                            resolve(response);
                        })
                        .catch(error => {
                            toaster.error(error.data['message'], 3000);
                        })
                        .finally(() => {
                            progress(false);
                        });
                }
            })
    });
}

export {processEntry};
