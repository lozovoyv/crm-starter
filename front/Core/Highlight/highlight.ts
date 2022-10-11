import "./highlight.scss";

function highlight(input: string | number | null, search: string | null, full_match: boolean = false): string | number | null {
    if (input === null || search === null) {
        return input;
    }

    let text = String(input);
    let ref = text.toLowerCase();
    let terms: Array<string> = search.split(' ');

    terms.map(term => {
        let index = ref.indexOf(term.toLowerCase());
        console.log(index, );
        if (index >= 0 && (!full_match || ref === term)) {
            text = text.substring(0, index) + "<span class='highlight'>" + text.substring(index, index + term.length) + "</span>" + text.substring(index + term.length);
            ref = text.toLowerCase();
        }
    });

    return text;
}

export {highlight}
