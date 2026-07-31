export function Breadcrumbs({ items }: { items: string[] }) { return <div className="mb-4 text-sm text-slate-500">{items.join(' / ')}</div>; }
