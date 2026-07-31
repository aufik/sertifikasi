import { usePage } from '@inertiajs/react';
import type { PageProps } from '@/Types/inertia';
export function usePermission() { const { auth } = usePage<PageProps>().props; const role = auth.user?.role; return { canMutate: role === 'admin' || role === 'manager', canDelete: role === 'admin', role }; }
