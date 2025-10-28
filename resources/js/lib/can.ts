import { usePage } from '@inertiajs/vue3';
const page = usePage();
export function can(permission: string) {
    const permissions = page.props.auth.permissions ?? [];
    return permissions.includes(permission);
}
