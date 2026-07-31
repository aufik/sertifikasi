import { cn } from '@/Lib/cn';
import type { TextareaHTMLAttributes } from 'react';
export function Textarea({ className, ...props }: TextareaHTMLAttributes<HTMLTextAreaElement>) { return <textarea className={cn('w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200', className)} {...props} />; }
