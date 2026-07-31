import { Link, usePage } from '@inertiajs/react';
import { ArchiveRestore, ArchiveX, ClipboardList, GaugeCircle, LogOut, Package, PackagePlus, RotateCcw, Trash2 } from 'lucide-react';

export function Sidebar() {
  const { url } = usePage();
  const { auth } = usePage().props as unknown as {
    auth: { user: { name: string; email: string; role: string } | null };
  };
  const user = auth?.user;

  const isLinkActive = (href: string) => {
    return url === href || url.startsWith(href + '?');
  };

  const getLinkClass = (href: string) => {
    const isActive = isLinkActive(href);
    return `flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-semibold transition duration-150 ${
      isActive
        ? 'bg-indigo-50 text-indigo-700 font-bold'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
    }`;
  };

  const getSubLinkClass = (href: string) => {
    const isActive = isLinkActive(href);
    return `flex items-center gap-2.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition duration-150 ${
      isActive
        ? 'bg-slate-100 text-slate-900 font-bold'
        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
    }`;
  };

  return (
    <aside className="w-64 border-r border-slate-200 bg-white flex flex-col justify-between h-screen sticky top-0 shrink-0 select-none">
      {/* Bagian menu atas (Dashboard, Products, & Operations) */}
      <div className="p-5 flex-1 overflow-y-auto space-y-6">
        <div className="flex items-center gap-2 pb-4 border-b border-slate-100">
          <div className="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-200">
            B
          </div>
          <div>
            <div className="text-sm font-bold text-slate-900 leading-tight">Sertifikasi BNSP</div>
            <div className="text-[10px] text-slate-400 font-medium tracking-wide">Uji Kompetensi</div>
          </div>
        </div>

        <nav className="space-y-6">
          <div className="space-y-1">
            <div className="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              Menu Utama
            </div>
            <Link className={getLinkClass('/dashboard')} href="/dashboard">
              <GaugeCircle className="h-4 w-4" />
              Dashboard
            </Link>
            <Link className={getLinkClass('/products')} href="/products">
              <Package className="h-4 w-4" />
              Products
            </Link>
          </div>

          <div className="space-y-1">
            <div className="mb-2 flex items-center gap-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              <ArchiveRestore className="h-3.5 w-3.5" /> Product Operations
            </div>
            <div className="space-y-1">
              <Link className={getSubLinkClass('/operations/restock')} href="/operations/restock">
                <PackagePlus className="h-3.5 w-3.5" />
                Restock Products
              </Link>
              <Link className={getSubLinkClass('/operations/delete')} href="/operations/delete">
                <ArchiveX className="h-3.5 w-3.5" />
                Delete Products
              </Link>
              <Link className={getSubLinkClass('/operations/restore')} href="/operations/restore">
                <RotateCcw className="h-3.5 w-3.5" />
                Restore Products
              </Link>
              <Link className={getSubLinkClass('/operations/purge')} href="/operations/purge">
                <Trash2 className="h-3.5 w-3.5" />
                Purge Products
              </Link>
            </div>
          </div>
        </nav>
      </div>

      {/* Bagian bawah: Link User Activity Logs + Biodata User */}
      <div className="border-t border-slate-200 bg-slate-50/50">
        <div className="p-3 border-b border-slate-200/60">
          <Link className={getLinkClass('/logs/user-activity')} href="/logs/user-activity">
            <ClipboardList className="h-4 w-4 text-indigo-600" />
            User Activity Logs
          </Link>
        </div>

        {user && (
          <div className="p-4 flex items-center justify-between gap-2 bg-slate-50">
            <div className="flex items-center gap-2.5 min-w-0">
              <div className="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200 shrink-0">
                {user.name.charAt(0).toUpperCase()}
              </div>
              <div className="min-w-0">
                <p className="text-xs font-bold text-slate-900 truncate">{user.name}</p>
                <p className="text-[10px] text-slate-500 truncate font-mono">{user.email}</p>
                <span className="inline-flex mt-1 items-center rounded bg-indigo-50 px-1.5 py-0.5 text-[9px] font-bold uppercase text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                  {user.role}
                </span>
              </div>
            </div>
            <Link
              href="/logout"
              method="post"
              as="button"
              className="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition duration-150 cursor-pointer shrink-0"
              title="Logout"
            >
              <LogOut className="h-4.5 w-4.5" />
            </Link>
          </div>
        )}
      </div>
    </aside>
  );
}
