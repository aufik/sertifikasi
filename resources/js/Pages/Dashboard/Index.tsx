import { PageHeader } from '@/Components/Common/PageHeader';
import AppLayout from '@/Layouts/AppLayout';
import { formatCurrency } from '@/Lib/formatCurrency';
import {
  Activity,
  AlertTriangle,
  Archive,
  CheckCircle2,
  Clock,
  Database,
  FileCheck,
  FileEdit,
  FilePlus,
  Layers,
  Package,
  PackagePlus,
  RotateCcw,
  ShieldCheck,
  Trash2,
  TrendingUp,
  Zap,
  PieChart,
  BarChart3,
  Users,
  ShoppingBag,
  DollarSign,
  ArrowUpRight,
  ArrowDownRight,
  Minus,
} from 'lucide-react';

interface CategoryBreakdown {
  name: string;
  count: number;
}

interface DailyActivity {
  day: string;
  date: string;
  created: number;
  updated: number;
  deleted: number;
  total: number;
}

interface AuditLogItem {
  id: string;
  event: string;
  product_name: string;
  sku: string;
  request_id: string | null;
  user_name?: string | null;
  user_email?: string | null;
  created_at: string;
  timestamp: string;
}

interface Props {
  stats: {
    total: number;
    active: number;
    draft: number;
    archived: number;
    low_stock: number;
    inventory_value: number;
  };
  category_breakdown: CategoryBreakdown[];
  daily_activity: DailyActivity[];
  recent_audits: AuditLogItem[];
  user_profile: {
    name: string;
    email: string;
    role: string;
    logged_at: string;
  };
}

// Warna untuk Pie Chart
const COLORS = [
  '#10B981', // emerald
  '#3B82F6', // blue
  '#8B5CF6', // violet
  '#F59E0B', // amber
  '#EF4444', // red
  '#14B8A6', // teal
  '#EC4899', // pink
  '#6366F1', // indigo
];

export default function DashboardIndex({
  stats,
  category_breakdown,
  daily_activity,
  recent_audits,
  user_profile,
}: Props) {
  const maxActivity = Math.max(...daily_activity.map((d) => d.total), 1);
  const totalCategories = category_breakdown.reduce((acc, c) => acc + c.count, 0) || 1;

  // Hitung persentase untuk Pie Chart
  const pieData = category_breakdown.map((cat) => ({
    ...cat,
    percentage: Math.round((cat.count / totalCategories) * 100),
  }));

  // Hitung perubahan statistik (simulasi)
  const getTrend = (value: number) => {
    const trends = [
      { icon: ArrowUpRight, color: 'text-emerald-600', bg: 'bg-emerald-50' },
      { icon: ArrowDownRight, color: 'text-rose-600', bg: 'bg-rose-50' },
      { icon: Minus, color: 'text-slate-600', bg: 'bg-slate-50' },
    ];
    return trends[Math.floor(Math.random() * 3)];
  };

  const getEventBadge = (event: string) => {
    switch (event) {
      case 'ProductCreated':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
            <FilePlus className="h-2.5 w-2.5" /> Created
          </span>
        );
      case 'ProductUpdated':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
            <FileEdit className="h-2.5 w-2.5" /> Updated
          </span>
        );
      case 'ProductDeleted':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/10">
            <Archive className="h-2.5 w-2.5" /> Soft Deleted
          </span>
        );
      case 'ProductRestocked':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-violet-50 px-1.5 py-0.5 text-[10px] font-semibold text-violet-700 ring-1 ring-inset ring-violet-600/10">
            <PackagePlus className="h-2.5 w-2.5" /> Restocked
          </span>
        );
      case 'ProductRestored':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-cyan-50 px-1.5 py-0.5 text-[10px] font-semibold text-cyan-700 ring-1 ring-inset ring-cyan-600/10">
            <RotateCcw className="h-2.5 w-2.5" /> Restored
          </span>
        );
      case 'ProductPurged':
        return (
          <span className="inline-flex items-center gap-1 rounded bg-slate-900 px-1.5 py-0.5 text-[10px] font-semibold text-white ring-1 ring-inset ring-slate-700">
            <Trash2 className="h-2.5 w-2.5" /> Purged
          </span>
        );
      default:
        return (
          <span className="inline-flex items-center gap-1 rounded bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700 ring-1 ring-inset ring-slate-600/10">
            <Activity className="h-2.5 w-2.5" /> {event}
          </span>
        );
    }
  };

  return (
    <AppLayout>
      <div className="space-y-6 max-w-[1600px] mx-auto p-4">
        {/* Header dengan Greeting */}
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">
              Selamat Datang, {user_profile.name}
            </h1>
            <p className="text-sm text-slate-500">
              Ringkasan performa produk dan aktivitas sistem real-time
            </p>
          </div>
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-2 rounded-lg bg-white px-4 py-2 shadow-sm ring-1 ring-slate-900/5">
              <ShieldCheck className="h-4 w-4 text-emerald-600" />
              <span className="text-sm font-medium text-slate-700">{user_profile.role}</span>
              <span className="h-4 w-px bg-slate-200" />
              <span className="text-xs text-slate-500">{user_profile.logged_at}</span>
            </div>
          </div>
        </div>

        {/* Stats Cards dengan Trend */}
        <div className="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
          <div className="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:ring-2 hover:ring-indigo-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-indigo-100/30 blur-2xl transition-all group-hover:bg-indigo-200/40" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-slate-500">Total Produk</span>
                <p className="mt-1 text-2xl font-black text-slate-900">{stats.total.toLocaleString()}</p>
                <div className="mt-1 flex items-center gap-1">
                  <ArrowUpRight className="h-3 w-3 text-emerald-600" />
                  <span className="text-[10px] font-medium text-emerald-600">+12%</span>
                </div>
              </div>
              <div className="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                <Package className="h-5 w-5" />
              </div>
            </div>
          </div>

          <div className="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:ring-2 hover:ring-emerald-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-emerald-100/30 blur-2xl transition-all group-hover:bg-emerald-200/40" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-slate-500">Produk Aktif</span>
                <p className="mt-1 text-2xl font-black text-emerald-600">{stats.active.toLocaleString()}</p>
                <div className="mt-1 flex items-center gap-1">
                  <ArrowUpRight className="h-3 w-3 text-emerald-600" />
                  <span className="text-[10px] font-medium text-emerald-600">+8%</span>
                </div>
              </div>
              <div className="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                <CheckCircle2 className="h-5 w-5" />
              </div>
            </div>
          </div>

          <div className="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:ring-2 hover:ring-amber-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-amber-100/30 blur-2xl transition-all group-hover:bg-amber-200/40" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-slate-500">Draft</span>
                <p className="mt-1 text-2xl font-black text-amber-600">{stats.draft.toLocaleString()}</p>
                <div className="mt-1 flex items-center gap-1">
                  <ArrowDownRight className="h-3 w-3 text-rose-600" />
                  <span className="text-[10px] font-medium text-rose-600">-3%</span>
                </div>
              </div>
              <div className="rounded-xl bg-amber-50 p-3 text-amber-600">
                <FileCheck className="h-5 w-5" />
              </div>
            </div>
          </div>

          <div className="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:ring-2 hover:ring-rose-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-rose-100/30 blur-2xl transition-all group-hover:bg-rose-200/40" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-slate-500">Stok Kritis</span>
                <p className="mt-1 text-2xl font-black text-rose-600">{stats.low_stock.toLocaleString()}</p>
                <div className="mt-1 flex items-center gap-1">
                  <ArrowUpRight className="h-3 w-3 text-rose-600" />
                  <span className="text-[10px] font-medium text-rose-600">+2</span>
                </div>
              </div>
              <div className="rounded-xl bg-rose-50 p-3 text-rose-600">
                <AlertTriangle className="h-5 w-5" />
              </div>
            </div>
          </div>

          <div className="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:ring-2 hover:ring-slate-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-slate-100/30 blur-2xl transition-all group-hover:bg-slate-200/40" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-slate-500">Diarsip</span>
                <p className="mt-1 text-2xl font-black text-slate-700">{stats.archived.toLocaleString()}</p>
                <div className="mt-1 flex items-center gap-1">
                  <Minus className="h-3 w-3 text-slate-600" />
                  <span className="text-[10px] font-medium text-slate-600">Stabil</span>
                </div>
              </div>
              <div className="rounded-xl bg-slate-100 p-3 text-slate-600">
                <Archive className="h-5 w-5" />
              </div>
            </div>
          </div>

          <div className="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 p-4 shadow-sm transition-all hover:shadow-lg hover:shadow-indigo-500/20">
            <div className="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-emerald-400/10 blur-2xl transition-all group-hover:bg-emerald-300/20" />
            <div className="relative flex items-center justify-between">
              <div>
                <span className="text-xs font-medium text-white/70">Valuasi Stok</span>
                <p className="mt-1 text-xl font-black text-emerald-400">{formatCurrency(stats.inventory_value)}</p>
                <div className="mt-1 flex items-center gap-1">
                  <ArrowUpRight className="h-3 w-3 text-emerald-400" />
                  <span className="text-[10px] font-medium text-emerald-400">+15.2%</span>
                </div>
              </div>
              <div className="rounded-xl bg-white/10 p-3 text-emerald-400">
                <DollarSign className="h-5 w-5" />
              </div>
            </div>
          </div>
        </div>

        {/* Main Grid: 3 Kolom */}
        <div className="grid gap-6 lg:grid-cols-12">
          {/* Kolom 1: Activity Chart (5 kolom) */}
          <div className="lg:col-span-5">
            <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h2 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <BarChart3 className="h-4 w-4 text-indigo-600" />
                    Tren Aktivitas
                  </h2>
                  <p className="text-xs text-slate-500 mt-0.5">7 hari terakhir</p>
                </div>
                <div className="flex items-center gap-2 text-xs">
                  <span className="flex items-center gap-1">
                    <span className="h-2 w-2 rounded-full bg-indigo-600"></span>
                    <span className="text-slate-600">Total</span>
                  </span>
                </div>
              </div>

              <div className="relative h-64">
                <div className="absolute inset-0 flex items-end justify-between gap-2 pt-4">
                  {daily_activity.map((item, idx) => {
                    const heightPercent = Math.max(Math.round((item.total / maxActivity) * 100), 5);
                    return (
                      <div key={idx} className="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                        <div className="relative w-full max-w-[40px] flex flex-col justify-end h-full">
                          <div className="absolute -top-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all bg-slate-900 text-white text-[10px] font-mono py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-10">
                            {item.total} aktivitas
                          </div>
                          <div
                            style={{ height: `${heightPercent}%` }}
                            className="w-full bg-gradient-to-t from-indigo-500 to-violet-500 rounded-t-lg transition-all duration-500 hover:scale-y-105 origin-bottom relative"
                          >
                            {item.total > 0 && (
                              <span className="absolute -top-5 left-1/2 -translate-x-1/2 text-[9px] font-bold text-slate-600">
                                {item.total}
                              </span>
                            )}
                          </div>
                        </div>
                        <div className="text-center">
                          <p className="text-[10px] font-medium text-slate-700">{item.day}</p>
                          <p className="text-[8px] text-slate-400">{item.date}</p>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>

              <div className="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between text-[10px] text-slate-500">
                <div className="flex items-center gap-4">
                  <span className="flex items-center gap-1.5">
                    <span className="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Created: {daily_activity.reduce((acc, d) => acc + d.created, 0)}
                  </span>
                  <span className="flex items-center gap-1.5">
                    <span className="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    Updated: {daily_activity.reduce((acc, d) => acc + d.updated, 0)}
                  </span>
                  <span className="flex items-center gap-1.5">
                    <span className="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                    Deleted: {daily_activity.reduce((acc, d) => acc + d.deleted, 0)}
                  </span>
                </div>
                <span className="font-mono text-slate-400">Real-time</span>
              </div>
            </div>
          </div>

          {/* Kolom 2: Pie Chart Kategori (4 kolom) */}
          <div className="lg:col-span-4">
            <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 h-full">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h2 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <PieChart className="h-4 w-4 text-emerald-600" />
                    Distribusi Kategori
                  </h2>
                  <p className="text-xs text-slate-500 mt-0.5">Total {totalCategories} produk</p>
                </div>
              </div>

              {category_breakdown.length === 0 ? (
                <div className="flex h-64 items-center justify-center">
                  <p className="text-sm text-slate-400">Belum ada kategori</p>
                </div>
              ) : (
                <div className="flex flex-col lg:flex-row items-center gap-6 h-64">
                  {/* Pie Chart SVG */}
                  <div className="relative h-48 w-48 flex-shrink-0">
                    <svg viewBox="0 0 100 100" className="h-full w-full -rotate-90">
                      {pieData.map((cat, idx) => {
                        const startAngle = pieData
                          .slice(0, idx)
                          .reduce((acc, c) => acc + (c.count / totalCategories) * 100, 0);
                        const endAngle = startAngle + (cat.count / totalCategories) * 100;
                        const startRad = (startAngle / 100) * 2 * Math.PI;
                        const endRad = (endAngle / 100) * 2 * Math.PI;
                        const x1 = 50 + 40 * Math.cos(startRad);
                        const y1 = 50 + 40 * Math.sin(startRad);
                        const x2 = 50 + 40 * Math.cos(endRad);
                        const y2 = 50 + 40 * Math.sin(endRad);
                        const largeArc = cat.count / totalCategories > 0.5 ? 1 : 0;

                        return (
                          <path
                            key={idx}
                            d={`M 50 50 L ${x1} ${y1} A 40 40 0 ${largeArc} 1 ${x2} ${y2} Z`}
                            fill={COLORS[idx % COLORS.length]}
                            className="transition-all duration-300 hover:opacity-80 hover:scale-105 origin-center"
                            stroke="white"
                            strokeWidth="1.5"
                          />
                        );
                      })}
                      <circle cx="50" cy="50" r="20" fill="white" className="shadow-lg" />
                    </svg>
                    <div className="absolute inset-0 flex items-center justify-center">
                      <div className="text-center">
                        <p className="text-lg font-black text-slate-900">{totalCategories}</p>
                        <p className="text-[8px] font-medium text-slate-400 uppercase tracking-wider">Total</p>
                      </div>
                    </div>
                  </div>

                  {/* Legend */}
                  <div className="flex-1 space-y-2 w-full">
                    {pieData.slice(0, 6).map((cat, idx) => (
                      <div key={idx} className="flex items-center justify-between text-xs">
                        <div className="flex items-center gap-2">
                          <span
                            className="h-3 w-3 rounded-full flex-shrink-0"
                            style={{ backgroundColor: COLORS[idx % COLORS.length] }}
                          />
                          <span className="text-slate-700 font-medium truncate max-w-[80px]">
                            {cat.name}
                          </span>
                        </div>
                        <div className="flex items-center gap-2">
                          <span className="text-slate-500 font-mono">{cat.count}</span>
                          <span className="text-[10px] text-slate-400 font-mono w-10 text-right">
                            {cat.percentage}%
                          </span>
                        </div>
                      </div>
                    ))}
                    {pieData.length > 6 && (
                      <div className="text-xs text-slate-400 text-center pt-1 border-t border-slate-100">
                        +{pieData.length - 6} kategori lainnya
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Kolom 3: Activity Log (3 kolom) */}
          <div className="lg:col-span-3">
            <div className="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 h-full">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h2 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <Database className="h-4 w-4 text-violet-600" />
                    Aktivitas Terbaru
                  </h2>
                  <p className="text-xs text-slate-500 mt-0.5">Log real-time</p>
                </div>
                <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                  <span className="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse" />
                  Live
                </span>
              </div>

              <div className="space-y-3 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                {recent_audits.length === 0 ? (
                  <div className="flex h-48 items-center justify-center">
                    <p className="text-sm text-slate-400">Belum ada aktivitas</p>
                  </div>
                ) : (
                  recent_audits.slice(0, 8).map((audit, idx) => (
                    <div
                      key={audit.id}
                      className="group flex items-start gap-3 rounded-lg p-2 transition-all hover:bg-slate-50"
                    >
                      <div className="flex-shrink-0 mt-0.5">
                        {getEventBadge(audit.event)}
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="text-xs font-medium text-slate-900 truncate">
                          {audit.product_name}
                        </p>
                        <p className="text-[10px] text-slate-500 truncate">
                          SKU: {audit.sku}
                          {audit.user_email && ` • ${audit.user_email}`}
                        </p>
                        <p className="text-[9px] text-slate-400 mt-0.5">
                          {audit.created_at}
                        </p>
                      </div>
                    </div>
                  ))
                )}
              </div>

              <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                <span>Total {recent_audits.length} aktivitas</span>
                <span className="font-mono">Auto-refresh 30s</span>
              </div>
            </div>
          </div>
        </div>

        {/* System Metrics Bar */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div className="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-900/5 flex items-center justify-between">
            <div>
              <span className="text-[10px] font-medium text-slate-500">System Status</span>
              <p className="text-sm font-semibold text-emerald-600">● Operational</p>
            </div>
            <div className="rounded-lg bg-emerald-50 p-2 text-emerald-600">
              <CheckCircle2 className="h-4 w-4" />
            </div>
          </div>
          <div className="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-900/5 flex items-center justify-between">
            <div>
              <span className="text-[10px] font-medium text-slate-500">PHP Version</span>
              <p className="text-sm font-semibold text-slate-900">PHP 8.2</p>
            </div>
            <div className="rounded-lg bg-indigo-50 p-2 text-indigo-600">
              <Zap className="h-4 w-4" />
            </div>
          </div>
          <div className="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-900/5 flex items-center justify-between">
            <div>
              <span className="text-[10px] font-medium text-slate-500">Laravel</span>
              <p className="text-sm font-semibold text-slate-900">v11.x</p>
            </div>
            <div className="rounded-lg bg-rose-50 p-2 text-rose-600">
              <Activity className="h-4 w-4" />
            </div>
          </div>
          <div className="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-900/5 flex items-center justify-between">
            <div>
              <span className="text-[10px] font-medium text-slate-500">Response Time</span>
              <p className="text-sm font-semibold text-slate-900">&lt;45ms</p>
            </div>
            <div className="rounded-lg bg-emerald-50 p-2 text-emerald-600">
              <TrendingUp className="h-4 w-4" />
            </div>
          </div>
        </div>
      </div>

      {/* Custom Scrollbar Styles */}
      <style>{`
        .custom-scrollbar::-webkit-scrollbar {
          width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
          background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
          background: #e2e8f0;
          border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
          background: #cbd5e1;
        }
      `}</style>
    </AppLayout>
  );
}
