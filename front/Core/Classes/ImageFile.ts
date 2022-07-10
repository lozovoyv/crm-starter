export class ImageFile {
    id: number | null;
    name: string;
    type: string;
    size: number;
    content: string | null;
    width: number;
    height: number;

    constructor(id: number | null, name: string, type: string, size: number, content: string | null, width: number, height: number) {
        this.id = id;
        this.name = name;
        this.type = type;
        this.size = size;
        this.content = content;
        this.width = width;
        this.height = height;
    }
}
