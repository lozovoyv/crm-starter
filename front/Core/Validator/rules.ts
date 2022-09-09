import empty from "@/Core/Helpers/Empty";

const Rules = function () {
    return {
        required(value: any, attributes: string | null, field_name: string, fields: { [index: string]: any }): boolean {
            // todo probably check files, dates e.t.c.
            return !empty(value);
        }
    };
};


export default Rules;
