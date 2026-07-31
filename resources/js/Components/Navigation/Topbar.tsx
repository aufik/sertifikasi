import { Link, usePage } from '@inertiajs/react';
import { LogOut } from 'lucide-react';
import type { PageProps } from '@/Types/inertia';

/** Header atas dengan informasi Request ID, nama user, role, dan tombol logout cepat. */
export function Topbar() {
  const { auth, requestId } = usePage<PageProps>().props;

  return (
    <header className="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-3.5 shadow-sm">
      <span className="text-xs font-mono text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md">
        Request ID: {requestId ?? '-'}
      </span>
      <div className="flex items-center gap-4">
        <span className="text-sm font-semibold text-slate-700">
          {auth.user?.name}
          <span className="ml-2 rounded bg-indigo-50 px-1.5 py-0.5 font-mono text-[10px] font-bold uppercase text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
            {auth.user?.role}
          </span>
        </span>
        <div className="h-4 w-px bg-slate-200"></div>
        <Link
          href="/logout"
          method="post"
          as="button"
          className="flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition duration-150 cursor-pointer"
        >
          <LogOut className="h-3.5 w-3.5" />
          Logout
        </Link>
      </div>
    </header>
  );
}
