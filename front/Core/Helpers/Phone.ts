function formatPhone(phone?: string | null): string | null {
    if (!phone) {
        return '—';
    }
    return phone.replace(/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/, '+$1 ($2) $3-$4-$5');
}

export {formatPhone};
