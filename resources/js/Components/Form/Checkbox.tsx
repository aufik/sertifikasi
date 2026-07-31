import type { InputHTMLAttributes } from 'react';
export function Checkbox(props: InputHTMLAttributes<HTMLInputElement>) { return <input type="checkbox" className="rounded border-slate-300 text-blue-600 focus:ring-blue-500" {...props} />; }
