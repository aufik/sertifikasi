import { cn } from '@/Lib/cn';

const labels: Record<string, string> = {
  draft: 'Draft',
  active: 'Active',
  archived: 'Archived',
};

/** Badge kecil untuk membedakan status produk dengan warna yang gampang dibaca. */
export function StatusBadge({ status }: { status: string }) {
  const color = status === 'active' ? 'bg-green-100 text-green-700' : status === 'draft' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700';

  return <span className={cn('rounded-full px-2 py-1 text-xs font-semibold', color)}>{labels[status] ?? status}</span>;
}
