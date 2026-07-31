import type { PropsWithChildren } from 'react';
export function DataTable({ children }: PropsWithChildren) { return <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><table className="min-w-full divide-y divide-slate-200">{children}</table></div>; }
