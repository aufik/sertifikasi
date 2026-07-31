import AppLayout from '@/Layouts/AppLayout';
import { router } from '@inertiajs/react';
import { Activity, Filter, Search, UserRound } from 'lucide-react';
import { FormEvent, useState } from 'react';

interface ActivityLogItem {
  id: string;
  event: string;
  product_name: string;
  sku: string;
  user_name: string | null;
  user_email: string | null;
  method: string | null;
  path: string | null;
  ip_address: string | null;
  request_id: string | null;
  created_at: string;
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginatedLogs {
  data: ActivityLogItem[];
  links: PaginationLink[];
  from: number | null;
  to: number | null;
  total: number;
}

interface Props {
  logs: PaginatedLogs;
  events: string[];
  filters: {
    search: string;
    event: string;
  };
}

/** Halaman ini menampilkan seluruh log bisnis user dari product_audits. */
export default function UserActivity({ logs, events, filters }: Props) {
  const [search, setSearch] = useState(filters.search ?? '');
  const [event, setEvent] = useState(filters.event ?? '');

  const submitFilter = (submitEvent: FormEvent) => {
    submitEvent.preventDefault();
    router.get('/logs/user-activity', { search, event }, { preserveState: true, replace: true });
  };

  const openPage = (url: string | null) => {
    if (!url) return;
    router.visit(url, { preserveState: true });
  };

  return (
    <AppLayout>
      <div className="max-w-7xl space-y-4">
        <div className="flex items-center justify-between border-b border-slate-200 pb-3">
          <div>
            <h1 className="text-xl font-bold text-slate-900">User Activity Logs</h1>
            <p className="text-xs text-slate-500">Log keseluruhan user dari audit bisnis produk, termasuk create, update, delete, restock, restore, dan purge.</p>
          </div>
          <span className="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-600/20">
            <Activity className="h-3.5 w-3.5" /> Business Audit Trail
          </span>
        </div>

        <form onSubmit={submitFilter} className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5">
          <div className="grid gap-3 md:grid-cols-12">
            <div className="md:col-span-7">
              <label className="mb-1 block text-xs font-semibold text-slate-700">Cari Log</label>
              <div className="relative">
                <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                <input value={search} onChange={(inputEvent) => setSearch(inputEvent.target.value)} className="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Cari user, email, event, path, atau request id..." />
              </div>
            </div>
            <div className="md:col-span-3">
              <label className="mb-1 block text-xs font-semibold text-slate-700">Event</label>
              <select value={event} onChange={(selectEvent) => setEvent(selectEvent.target.value)} className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua Event</option>
                {events.map((item) => <option key={item} value={item}>{item}</option>)}
              </select>
            </div>
            <div className="flex items-end md:col-span-2">
              <button className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <Filter className="h-4 w-4" /> Filter
              </button>
            </div>
          </div>
        </form>

        <div className="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-900/5">
          <div className="border-b border-slate-100 px-4 py-3 text-xs text-slate-500">
            Menampilkan {logs.from ?? 0} - {logs.to ?? 0} dari {logs.total} log.
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-slate-100 text-sm">
              <thead className="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                  <th className="px-4 py-3">Waktu</th>
                  <th className="px-4 py-3">User</th>
                  <th className="px-4 py-3">Event</th>
                  <th className="px-4 py-3">Produk</th>
                  <th className="px-4 py-3">API</th>
                  <th className="px-4 py-3">Request ID</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {logs.data.map((log) => (
                  <tr key={log.id} className="hover:bg-slate-50">
                    <td className="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-500">{log.created_at}</td>
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-2">
                        <div className="rounded-full bg-slate-100 p-1.5 text-slate-500"><UserRound className="h-3.5 w-3.5" /></div>
                        <div>
                          <p className="font-semibold text-slate-900">{log.user_name ?? 'System'}</p>
                          <p className="text-xs text-slate-500">{log.user_email ?? '-'}</p>
                        </div>
                      </div>
                    </td>
                    <td className="whitespace-nowrap px-4 py-3 font-semibold text-indigo-700">{log.event}</td>
                    <td className="px-4 py-3">
                      <p className="font-semibold text-slate-900">{log.product_name}</p>
                      <p className="font-mono text-xs text-slate-500">{log.sku}</p>
                    </td>
                    <td className="px-4 py-3">
                      <p className="font-mono text-xs text-slate-700">{log.method ?? '-'}</p>
                      <p className="font-mono text-xs text-slate-500">{log.path ?? '-'}</p>
                    </td>
                    <td className="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-500">{log.request_id ?? '-'}</td>
                  </tr>
                ))}
                {logs.data.length === 0 && (
                  <tr>
                    <td colSpan={6} className="px-4 py-10 text-center text-sm text-slate-400">Belum ada log yang cocok.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
          <div className="flex flex-wrap gap-2 border-t border-slate-100 px-4 py-3">
            {logs.links.map((link, index) => (
              <button key={`${link.label}-${index}`} type="button" disabled={!link.url} onClick={() => openPage(link.url)} className={`rounded-md px-3 py-1 text-xs font-semibold ring-1 transition disabled:cursor-not-allowed disabled:opacity-40 ${link.active ? 'bg-indigo-600 text-white ring-indigo-600' : 'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
            ))}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
