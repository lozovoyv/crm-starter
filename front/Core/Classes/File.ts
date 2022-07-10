export class File {
    id: number | null;
    name: string;
    type: string;
    size: number;
    content: string | null;

    constructor(id: number | null, name: string, type: string, size: number, content: string | null) {
        this.id = id;
        this.name = name;
        this.type = type;
        this.size = size;
        this.content = content;
    }
}
