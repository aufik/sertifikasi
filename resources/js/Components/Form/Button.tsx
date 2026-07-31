import { cn } from '@/Lib/cn';
import type { ButtonHTMLAttributes, PropsWithChildren } from 'react';
export function Button({ className, children, ...props }: PropsWithChildren<ButtonHTMLAttributes<HTMLButtonElement>>) { return <button className={cn('rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60', className)} {...props}>{children}</button>; }
