import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import type { PageProps } from '@/Types/inertia';
export function useFlashToast(): void { const { flash } = usePage<PageProps>().props; useEffect(() => { if (flash.success) toast.success(flash.success); if (flash.error) toast.error(flash.error); }, [flash.success, flash.error]); }
