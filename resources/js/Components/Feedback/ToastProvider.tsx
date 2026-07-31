import { Toaster } from 'sonner';
import type { PropsWithChildren } from 'react';
export function ToastProvider({ children }: PropsWithChildren) { return <>{children}<Toaster richColors position="top-right" /></>; }
