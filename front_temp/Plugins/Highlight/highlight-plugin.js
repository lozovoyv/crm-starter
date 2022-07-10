import "./highlight.scss";

export default {
    install: (app) => {
        app.config.globalProperties.$highlight = function (text, terms, full_match = false) {
            if (typeof text !== "undefined" && text !== null && terms !== null) {
                text = String(text);
                let ref = text.toLowerCase();
                terms = terms.split(' ');
                terms.map(term => {
                    let index = ref.indexOf(term.toLowerCase());
                    if (index >= 0 && (!full_match || ref === term)) {
                        text = text.substring(0, index) + "<span class='highlight'>" + text.substring(index, index + term.length) + "</span>" + text.substring(index + term.length);
                        ref = text.toLowerCase();
                    }
                });
            }
            return text;
        }
    }
}
