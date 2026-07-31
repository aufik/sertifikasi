import { productApi } from '@/Api/productApi';
import { SkuAutocomplete } from '@/Components/Forms/SkuAutocomplete';
import AppLayout from '@/Layouts/AppLayout';
import type { Product } from '@/Types/product';
import { FormEvent, useEffect, useState } from 'react';
import { Copy, Search, ShieldAlert, Trash2 } from 'lucide-react';
import { toast } from 'sonner';

/** Halaman purge: tetap tampilkan list trash, lalu purge dari form SKU dan alasan. */
export default function ProductPurge() {
  const [search, setSearch] = useState('');
  const [products, setProducts] = useState<Product[]>([]);
  const [sku, setSku] = useState('');
  const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);
  const [purgeReason, setPurgeReason] = useState('Data produk sudah tidak dibutuhkan lagi.');
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    const timeout = window.setTimeout(async () => {
      setLoading(true);
      try {
        const response = await productApi.trash({ search, per_page: 8, sort: 'latest' });
        setProducts(response.data.data);
      } catch (error) {
        toast.error('Gagal mengambil daftar produk trash.');
      } finally {
        setLoading(false);
      }
    }, 300);

    return () => window.clearTimeout(timeout);
  }, [search]);

  /** Copy SKU supaya user bisa pakai di tempat lain tanpa mengetik ulang. */
  const copySku = async (sku: string) => {
    await navigator.clipboard.writeText(sku);
    toast.success(`SKU ${sku} berhasil dicopy.`);
  };

  /** Cari produk dari list trash berdasarkan SKU yang diketik, lalu purge setelah user konfirmasi. */
  const submitPurge = async (event: FormEvent) => {
    event.preventDefault();
    const normalizedSku = sku.trim();
    if (!normalizedSku) {
      toast.error('Isi SKU produk terlebih dahulu.');
      return;
    }

    const product = selectedProduct?.sku.toLowerCase() === normalizedSku.toLowerCase()
      ? selectedProduct
      : products.find((item) => item.sku.toLowerCase() === normalizedSku.toLowerCase());
    if (!product) {
      toast.error('SKU tidak ditemukan di list trash saat ini. Cari dulu SKU tersebut di kolom kiri, lalu submit lagi.');
      return;
    }

    if (!purgeReason.trim()) {
      toast.error('Isi alasan purge terlebih dahulu.');
      return;
    }

    const confirmed = window.confirm(`PERMANEN: Hapus ${product.name} dengan SKU ${product.sku}? Tindakan ini tidak dapat dibatalkan.`);
    if (!confirmed) return;

    setSubmitting(true);
    try {
      await productApi.purge(product.id);
      toast.success(`${product.name} berhasil dipurge secara permanen.`);
      setProducts((currentProducts) => currentProducts.filter((item) => item.id !== product.id));
      setSelectedProduct(null);
      setSku('');
      setPurgeReason('Data produk sudah tidak dibutuhkan lagi.');
    } catch (error) {
      toast.error('Purge gagal. Operasi ini hanya untuk Admin.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <AppLayout>
      <div className="max-w-6xl space-y-4">
        <div className="flex items-center justify-between border-b border-slate-200 pb-3">
          <div>
            <h1 className="text-xl font-bold text-slate-900">Purge Products</h1>
            <p className="text-xs text-slate-500">Lihat list produk trash, isi SKU dan alasan, lalu purge setelah konfirmasi.</p>
          </div>
          <span className="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/20">
            <ShieldAlert className="h-3.5 w-3.5" /> Critical Admin Operation
          </span>
        </div>

        <div className="grid gap-4 lg:grid-cols-12">
          <div className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-7">
            <label className="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500">List Produk di Trash</label>
            <div className="relative">
              <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
              <input className="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari nama produk atau SKU di trash..." />
            </div>

            <div className="mt-3 divide-y divide-slate-100 rounded-lg border border-slate-100">
              {loading ? <div className="p-4 text-center text-xs text-slate-400">Loading trash products...</div> : products.map((product) => (
                <div key={product.id} className="flex items-center justify-between gap-2 px-3 py-2.5 transition hover:bg-rose-50">
                  <button type="button" onClick={() => { setSku(product.sku); setSelectedProduct(product); }} className="min-w-0 flex-1 text-left">
                    <p className="truncate text-sm font-semibold text-slate-900">{product.name}</p>
                    <p className="text-[11px] font-mono text-slate-500">{product.sku} • Stok: {product.stock}</p>
                  </button>
                  <div className="flex shrink-0 items-center gap-2">
                    <span className="text-xs font-semibold text-rose-600">Pakai SKU</span>
                    <button type="button" onClick={() => copySku(product.sku)} className="inline-flex items-center gap-1 rounded-md bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-50"><Copy className="h-3 w-3" /> Copy SKU</button>
                  </div>
                </div>
              ))}
              {!loading && products.length === 0 && <div className="p-4 text-center text-xs text-slate-400">Tidak ada produk trash yang cocok.</div>}
            </div>
          </div>

          <form onSubmit={submitPurge} className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 lg:col-span-5">
            <h2 className="mb-3 flex items-center gap-2 text-sm font-bold text-slate-900"><Trash2 className="h-4 w-4 text-rose-600" /> Form Purge Permanen</h2>
            <div className="rounded-lg bg-rose-50 p-3 text-xs text-rose-800 ring-1 ring-rose-100">
              <p className="font-semibold">Purge berdasarkan SKU</p>
              <p className="mt-1 leading-relaxed">Ketik SKU manual, atau klik produk di list kiri untuk mengisi SKU. Setelah submit, sistem tetap menampilkan konfirmasi karena purge bersifat permanen.</p>
            </div>

            <div className="mt-3 space-y-3">
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">SKU Produk</label>
                <SkuAutocomplete value={sku} onChange={(nextSku) => { setSku(nextSku); setSelectedProduct(null); }} onProductSelect={setSelectedProduct} source="trash" placeholder="Cari atau ketik SKU produk trash..." />
              </div>
              <div>
                <label className="mb-1 block text-xs font-semibold text-slate-700">Alasan Purge</label>
                <textarea value={purgeReason} onChange={(e) => setPurgeReason(e.target.value)} className="h-24 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100" placeholder="Contoh: data produk sudah tidak dibutuhkan." />
              </div>
              <button disabled={submitting} className="w-full rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                {submitting ? 'Purging...' : 'Submit Purge Permanen via API'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
