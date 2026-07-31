import { Link } from '@inertiajs/react';
import type { PaginationMeta } from '@/Types/inertia';
export function Pagination({ meta }: { meta: PaginationMeta }) { return <div className="flex items-center justify-between py-4 text-sm text-slate-600"><span>Page {meta.current_page} of {meta.last_page} · {meta.total} records</span><div className="space-x-2"><Link className="rounded border px-3 py-1" href={`?page=${Math.max(1, meta.current_page - 1)}`}>Prev</Link><Link className="rounded border px-3 py-1" href={`?page=${Math.min(meta.last_page, meta.current_page + 1)}`}>Next</Link></div></div>; }
