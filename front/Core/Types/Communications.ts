import {ErrorResponse} from "@/Core/Http/Http";

export type CommunicationState = {
    is_loading?: boolean;
    is_loaded?: boolean;
    is_saving?: boolean;
    is_saved?: boolean;
    is_forbidden?: boolean;
    is_not_found?: boolean;
}

export type CommunicationError = {
    code: number,
    message: string,
    response: ErrorResponse | null
}

export function initialCommunicationState(): CommunicationState {
    return {
        is_loading: false,
        is_loaded: false,
        is_saving: false,
        is_saved: false,
        is_forbidden: false,
        is_not_found: false,
    }
}
