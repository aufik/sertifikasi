import { cn } from '@/Lib/cn';
import type { SelectHTMLAttributes, PropsWithChildren } from 'react';
export function Select({ className, children, ...props }: PropsWithChildren<SelectHTMLAttributes<HTMLSelectElement>>) { return <select className={cn('w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200', className)} {...props}>{children}</select>; }
